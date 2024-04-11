import styled from "styled-components";
import {useWebComponentEvents} from "./hooks/useWebComponentEvents";
import {createIslandWebComponent} from 'preact-island'
import {useState, useEffect, useRef, useCallback, useMemo} from 'preact/hooks';
import {deserialize} from "./tools/deserialize";
import {buildMenuTree, MenuContentItem} from "./tools/build-menu-tree";
import {DRUPAL_DOMAIN} from './config/env'
import Caret from "./components/caret";
import Hamburger from "./components/hamburger";
import Close from "./components/close";
import MagnifyingGlass from "./components/magnifying-glass";
import useOutsideClick from "./hooks/useOutsideClick";
import {useEventListener} from "usehooks-ts";

const islandName = 'main-menu-island'

const MenuWrapper = styled.div<{ open?: boolean }>`
  display: ${props => props.open ? "block" : "none"};
  position: absolute;
  top: 0;
  left: 0;
  width: 100vw;
  margin-left: calc(50% - 50vw);
  margin-right: calc(50% - 50vw);

  @media (min-width: 992px) {
    display: block;
    position: relative;
    width: 100%;
    margin: 0 auto;
  }
`

const TopList = styled.ul`
  flex-wrap: wrap;
  justify-content: flex-end;
  list-style: none;
  margin: 0;
  background: #2e2d29;
  padding: 24px;
  font-size: 18px;

  @media (min-width: 992px) {
    display: flex;
    background: transparent;
    padding: 0;
    font-size: 19px;
    width: 100%;

  }
`

const MobileMenuButton = styled.button`
  position: absolute;
  top: -60px;
  right: 10px;
  box-shadow: none;
  background: transparent;
  border: 0;
  border-bottom: 2px solid transparent;
  color: #2e2d29;
  padding: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  font-size: 1.6rem;

  &:hover, &:focus {
    border-bottom: 2px solid #2e2d29;
    background: transparent;
    color: #2e2d29;
    box-shadow: none;
  }

  @media (min-width: 992px) {
    display: none;
  }
`

const SearchContainer = styled.div`
  padding: 10px 30px 0;
  margin: 0;
  background: #2e2d29;

  form {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  label {
    padding: 0 10px;
    margin: 0;
    color: #fff;
  }

  input {
    margin: 0;
    width: 100%;
    border-radius: 999px;
    height: 40px;
    padding: 0 20px;
    max-width: 100%;
  }

  button {
    position: absolute;
    top: 0;
    right: 0;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: none;
    color: #b1040e;
    border: 1px solid transparent;
    border-radius: 999px;
    aspect-ratio: 1;
    padding: 0;
    margin: 0;

    &:hover, &:focus {
      border: 1px solid #2e2d29;
    }
  }

  @media (min-width: 992px) {
    display: none;
  }
`

export const MainMenu = ({}) => {
  useWebComponentEvents(islandName)
  const [menuItems, setMenuItems] = useState<MenuContentItem[]>(window.drupalSettings?.stanford_basic?.decoupledMenuItems || []);
  const [menuOpen, setMenuOpen] = useState<boolean>(false);
  const buttonRef = useRef<HTMLButtonElement | null>(null);
  const navRef = useRef<HTMLElement | null>(null);
  useOutsideClick(navRef, () => setMenuOpen(false));

  useEffect(() => {
    if (menuItems.length) return;

    fetch(DRUPAL_DOMAIN + '/jsonapi/menu_items/main')
      .then(res => res.json())
      .then(data => setMenuItems(deserialize(data)))
      .catch(err => console.error(err));
  }, [])

  const handleEscape = useCallback((event: KeyboardEvent) => {
    if (event.key === "Escape" && menuOpen) {
      setMenuOpen(false);
      buttonRef.current?.focus();
    }
  }, [menuOpen]);

  useEventListener("keydown", handleEscape);

  const menuTree = useMemo(() => buildMenuTree(menuItems), [menuItems]);
  if (!menuTree.items || menuTree.items?.length === 0) return;

  // Remove the default menu.
  const existingMenu = document.getElementsByClassName('su-multi-menu');
  if (existingMenu.length > 0) existingMenu[0].remove();

  return (
    <nav
      ref={navRef}
      style={{position: "relative"}}
    >
      <MobileMenuButton
        ref={buttonRef}
        onClick={() => setMenuOpen(!menuOpen)}
        aria-expanded={menuOpen}
      >
        {menuOpen ? <Close/> : <Hamburger/>}
        {menuOpen ? "Close" : "Menu"}
      </MobileMenuButton>

      <MenuWrapper open={menuOpen}>
        <SearchContainer>
          <form action="/search" method="get">
            <label htmlFor="mobile-search-input">Keyword Search</label>
            <div style={{position: "relative"}}>
              <input
                id="mobile-search-input"
                type="text"
                placeholder="Search this site"
                name="key"
              />
              <button type="submit">
                <MagnifyingGlass style={{width: "25px", height: "25px"}}/>
                <span className="visually-hidden">Submit Search</span>
              </button>
            </div>
          </form>

        </SearchContainer>
        <TopList>
          {menuTree.items.sort((a, b) => a.weight < b.weight ? -1 : 1).map(item => <MenuItem key={item.id} {...item}/>)}
        </TopList>
      </MenuWrapper>
    </nav>
  )
}

const Button = styled.button`
  color: #fff;
  background: #b1040e;
  border: none;
  border-bottom: 1px solid transparent;
  padding: 0;
  margin: 0 0 -4px;
  box-shadow: none;
  flex-shrink: 0;
  border-radius: 999px;
  transition: color 0.2s ease-in-out, background 0.2s ease-in-out, border 0.2s ease-in-out;
  width: 38px;
  height: 38px;

  &:hover, &:focus {
    box-shadow: none;
    border-bottom: 1px solid #b1040e;
    background: #f4f4f4;
    color: #000;
  }

  @media (min-width: 992px) {
    color: #b1040e;
    background: transparent;
    border-radius: 0;

    &:hover, &:focus {
      border-bottom: 1px solid #2e2d29;
      color: #2e2d29;
      background: transparent;
    }
  }
`

const MenuItemContainer = styled.div<{ level?: number }>`
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-right: ${props => props.level === 0 ? "32px" : "0"};

  width: 100%;

  @media (min-width: 992px) {
    width: ${props => props.level === 0 ? "fit-content" : "100%"};
    margin-bottom: ${props => props.level === 0 ? "6px" : ""};
  }
`

const MenuLink = styled.a<{ isCurrent?: boolean, inTrail?: boolean, level?: number }>`
  color: #ffffff;
  font-weight: 600;
  text-decoration: none;
  padding: 16px 0 16px 16px;
  transition: all 0.2s ease-in-out;
  border-left: ${({isCurrent}) => isCurrent ? "6px solid #b1040e" : "6px solid transparent"};
  width: 100%;

  &:hover, &:focus {
    text-decoration: underline;
    color: #fff;
    border-left: 6px solid #fff;
  }

  @media (min-width: 992px) {
    color: #b1040e;
    padding: ${({level}) => level != 0 ? "16px 0 16px 16px" : "16px 0"};
    border-bottom: ${({level, inTrail, isCurrent}) => level === 0 ? (isCurrent ? "6px solid #2e2d29" : (inTrail ? "6px solid #b6b1a9" : "6px solid transparent")) : ""};
    border-left: ${({level, isCurrent}) => level != 0 ? (isCurrent ? "6px solid #b1040e" : "6px solid transparent") : "none"};
    margin-bottom: ${({level, inTrail, isCurrent}) => level === 0 ? (isCurrent ? "-6px" : (inTrail ? "-6px" : "-6px")) : ""};

    &:hover, &:focus {
      color: #2e2d29;
      border-left: ${({level}) => level != 0 ? "6px solid #2e2d29" : "none"};
    }
  }
`

const NoLink = styled.span<{ level?: number }>`
  color: #fff;
  font-weight: 600;
  text-decoration: none;
  padding: 16px 0 16px 16px;

  @media (min-width: 992px) {
    color: #b1040e;
    padding: ${({level}) => level != 0 ? "16px 0 16px 16px" : "16px 0"};
  }
`

const MenuList = styled.ul<{ open?: boolean, level?: number }>`
  display: ${props => props.open ? "block" : "none"};
  z-index: ${props => props.level + 1};
  list-style: none;
  padding: 0;
  margin: 0;
  border-top: 1px solid #53565a;
  min-width: 300px;

  @media (min-width: 992px) {
    box-shadow: ${props => props.level === 0 ? "0 10px 20px rgba(0,0,0,.15),0 6px 6px rgba(0,0,0,.2)" : ""};
    position: ${props => props.level === 0 ? "absolute" : "relative"};
    top: 100%;
    background: #fff;
    border-top: 1px solid #d9d9d9;
    right: 0;
  }
`

const ListItem = styled.li<{ level?: number }>`
  position: relative;
  border-bottom: 1px solid #53565a;
  padding: ${props => props.level > 0 ? "0 0 0 10px" : "0"};
  margin: 0;

  &:last-child {
    border-bottom: none;
  }

  @media (min-width: 992px) {
    border-bottom: ${props => props.level === 0 ? "none" : "1px solid #d9d9d9"};
    padding: ${props => props.level > 0 ? "0 10px" : "0"};
  }
`

const MenuItemDivider = styled.div`
  width: 1px;
  height: 20px;
  margin: 0 6px;
  background: #766253;
  display: none;
  flex-shrink: 0;

  @media (min-width: 992px) {
    display: block;
  }
`

const MenuItem = ({id, title, url, items, expanded, level = 0}: {
  title: string,
  url: string,
  items?: MenuContentItem[],
  expanded: boolean,
  level?: number
}) => {
  const buttonRef = useRef<HTMLButtonElement | null>(null)
  const [submenuOpen, setSubmenuOpen] = useState(false)
  const menuItemRef = useRef<HTMLLIElement | null>(null);
  useOutsideClick(menuItemRef, () => setSubmenuOpen(false));

  const handleEscape = (event: KeyboardEvent) => {
    if (event.key === "Escape" && submenuOpen) {
      setSubmenuOpen(false);
      if (level === 0) buttonRef.current?.focus();
    }
  };

  useEventListener("keydown", handleEscape);

  let linkUrl: URL;
  let isNoLink = true;
  let isCurrent, inTrail = false;

  if (url) {
    isNoLink = false;
    linkUrl = new URL(url.startsWith('/') ? `${window.location.origin}${url}` : url);
    isCurrent = linkUrl.pathname === window.location.pathname && linkUrl.host === window.location.host;
    inTrail = linkUrl.host === window.location.host && url != '/' && window.location.pathname.startsWith(linkUrl.pathname) && !isCurrent;
  }

  return (
    <ListItem
      ref={menuItemRef}
      level={level}
    >
      <MenuItemContainer level={level}>
        {!isNoLink &&
          <MenuLink
            id={id}
            href={url}
            aria-current={isCurrent ? "page" : undefined}
            level={level}
            isCurrent={isCurrent}
            inTrail={inTrail}
          >
            {title}
          </MenuLink>
        }
        {isNoLink &&
          <NoLink>{title}</NoLink>
        }

        {(items && expanded) &&
          <>
            {level === 0 &&
              <MenuItemDivider/>
            }
            <Button
              ref={buttonRef}
              onClick={() => setSubmenuOpen(!submenuOpen)}
              aria-expanded={submenuOpen}
              aria-labelledby={id}
            >
              <Caret style={{
                transform: submenuOpen ? "rotate(180deg)" : "",
                transition: "transform 0.2s ease-in-out",
                width: "16px",
              }}
              />
            </Button>
          </>
        }
      </MenuItemContainer>

      {(items && expanded) &&
        <MenuList open={submenuOpen} level={level}>

          {items.sort((a, b) => a.weight < b.weight ? -1 : 1).map(item =>
            <MenuItem key={item.id} {...item} level={level + 1}/>
          )}
        </MenuList>
      }
    </ListItem>

  )
}


const island = createIslandWebComponent(islandName, MainMenu)
island.render({
  selector: `[data-island="${islandName}"]`,
})

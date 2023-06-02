import styled from "styled-components";
import {useWebComponentEvents} from "./hooks/useWebComponentEvents";
import {createIslandWebComponent} from 'preact-island'
import {useState, useEffect} from 'preact/hooks';
import {deserialize} from "./tools/deserialize";
import {buildMenuTree, MenuContentItem} from "./tools/build-menu-tree";
import {DRUPAL_DOMAIN} from './config/env'
import OutsideClickHandler from "./components/outside-click-handler";
import Caret from "./components/caret";
import Hamburger from "./components/hamburger";
import Close from "./components/close";

const islandName = 'main-menu-island'

const TopList = styled.ul<{ open?: boolean }>`
  display: ${props => props.open ? "block" : "none"};
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  flex-wrap: wrap;
  justify-content: flex-start;
  list-style: none;
  margin: 0;
  background: #2e2d29;
  padding: 24px;

  @media (min-width: 991px) {
    display: flex;
    background: transparent;
    padding: 0;
    position: relative;
  }
`

const MobileMenuButton = styled.button`
  position: absolute;
  top: -70px;
  right: 10px;
  box-shadow: none;
  background: transparent;
  border: 0;
  border-bottom: 2px solid transparent;
  color: #000000;
  padding: 0;
  display: flex;
  flex-direction: column;
  align-items: center;

  &:hover, &:focus {
    border-bottom: 2px solid #000000;
    background: transparent;
    color: #000000;
    box-shadow: none;
  }

  @media (min-width: 991px) {
    display: none;
  }
`

export const MainMenu = ({}) => {
  useWebComponentEvents(islandName)

  const [menuItems, setMenuItems] = useState<MenuContentItem[]>([]);
  const [menuOpen, setMenuOpen] = useState<boolean>(false);

  useEffect(() => {
    fetch(DRUPAL_DOMAIN + '/jsonapi/menu_items/main')
      .then(res => res.json())
      .then(data => setMenuItems(deserialize(data)))
      .catch(err => console.error(err));
  }, [])
  const menuTree = buildMenuTree(menuItems);
  if (!menuTree.items || menuTree.items?.length === 0) return <div/>;

  // Remove the default menu.
  const existingMenu = document.getElementsByClassName('su-multi-menu');
  if (existingMenu.length > 0) {
    existingMenu[0].remove();
  }

  return (
    <nav style={{position: "relative"}}>
      <MobileMenuButton onClick={() => setMenuOpen(!menuOpen)} aria-expanded={menuOpen}>
        {menuOpen ? <Close/> : <Hamburger/>}
        {menuOpen ? "Close" : "Menu"}
      </MobileMenuButton>

      <TopList open={menuOpen}>
        {menuTree.items.map(item => <MenuItem key={item.id} {...item}/>)}
      </TopList>
    </nav>
  )
}

const Button = styled.button`
  color: #ffffff;
  background: transparent;
  border: none;
  border-bottom: 2px solid transparent;
  padding: 0;
  margin: 0;
  margin-bottom: -2px;
  box-shadow: none;

  &:hover, &:focus {
    box-shadow: none;
    border-bottom: 2px solid #ffffff;
    background: transparent;
  }

  @media (min-width: 991px) {
    color: #b1040e;

    &:hover, &:focus {
      border-bottom: 2px solid #000000;
      color: #000000;
    }
  }
`

const MenuItemContainer = styled.div<{ level?: number }>`
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-right: ${props => props.level === 0 ? "32px" : "0"};
  width: 100%;

  @media (min-width: 991px) {
    width: ${props => props.level === 0 ? "fit-content" : "100%"};
  }
`

const MenuLink = styled.a<{ inTrail?: boolean, level?: number }>`
  color: #ffffff;
  font-weight: 600;
  text-decoration: none;
  padding: 16px 0;

  &:hover, &:focus {
    text-decoration: underline;
    color: #ffffff;
  }

  @media (min-width: 991px) {
    color: #b1040e;

    &:hover, &:focus {
      color: #2e2d29;
    }
  }
`

const MenuList = styled.ul<{ open?: boolean, level?: number }>`
  display: ${props => props.open ? "block" : "none"};
  z-index: ${props => props.level + 1};
  list-style: none;
  padding: 0;
  margin: 0;
  border-top: 1px solid #d9d9d9;

  @media (min-width: 991px) {
    box-shadow: ${props => props.level === 0 ? "0 10px 20px rgba(0,0,0,.15),0 6px 6px rgba(0,0,0,.2)" : ""};
    position: ${props => props.level === 0 ? "absolute" : "relative"};
    top: 100%;
    background: white;
  }
`

const ListItem = styled.li<{ level?: number }>`
  border-bottom: 1px solid #d9d9d9;
  padding: ${props => props.level > 0 ? "0 22px" : "0"};

  &:last-child {
    border-bottom: none;
  }

  @media (min-width: 991px) {
    border-bottom: ${props => props.level === 0 ? "none" : "1px solid #d9d9d9"};
  }
`

const MenuItemDivider = styled.div`
  width: 1px;
  height: 20px;
  margin: 0 12px;
  background: #766253;
  display: none;

  @media (min-width: 991px) {
    display: block;
  }
`

const MenuItem = ({title, url, items, level = 0}: { title: string, url: string, items?: MenuContentItem[], level?: number }) => {
  const [submenuOpen, setSubmenuOpen] = useState(false)
  const basePath = window.location.protocol + "//" + window.location.host;
  const linkUrl = new URL(url.startsWith('/') ? `${basePath}${url}` : url);
  const isCurrent = linkUrl.pathname === window.location.pathname;
  const inTrail = window.location.pathname.startsWith(linkUrl.pathname);
  return (
    <OutsideClickHandler
      onOutsideFocus={() => setSubmenuOpen(false)}
      component={ListItem}
      level={level}
    >
      <MenuItemContainer level={level}>
        <MenuLink href={url} aria-current={isCurrent ? "page" : undefined} inTrail={inTrail}>
          {title}
        </MenuLink>

        {items &&
          <>
            {level === 0 &&
              <MenuItemDivider/>
            }
            <Button
              onClick={() => setSubmenuOpen(!submenuOpen)}
              aria-expanded={submenuOpen}
              aria-label={(submenuOpen ? "Close" : "Open") + `${title} Submenu`}
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

      {items &&
        <MenuList open={submenuOpen} level={level}>

          {items.map(item =>
            <MenuItem key={item.id} {...item} level={level + 1}/>
          )}
        </MenuList>
      }
    </OutsideClickHandler>

  )
}


const island = createIslandWebComponent(islandName, MainMenu)
island.render({
  selector: `[data-island="${islandName}"]`,
})

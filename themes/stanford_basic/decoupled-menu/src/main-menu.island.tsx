import {useWebComponentEvents} from "./hooks/useWebComponentEvents";
import {createIslandWebComponent} from 'preact-island'
import {useState, useEffect, useRef} from 'preact/hooks';
import {deserialize} from "./tools/deserialize";
import {buildMenuTree} from "./tools/build-menu-tree";
import {DRUPAL_DOMAIN} from './config/env'

const islandName = 'main-menu-island'

export const MainMenu = ({}) => {
  useWebComponentEvents(islandName)

  const [menuItems, setMenuItems] = useState([]);

  useEffect(() => {
    fetch(DRUPAL_DOMAIN + '/jsonapi/menu_items/main')
      .then(res => res.json())
      .then(data => setMenuItems(deserialize(data)))
      .catch(err => console.error(err));
  }, [])
  const menuTree = buildMenuTree(menuItems);
  if (menuTree.items.length === 0) return <div/>;

  // Remove the default menu.
  const existingMenu = document.getElementsByClassName('su-multi-menu');
  if (existingMenu.length > 0) {
    existingMenu[0].remove();
  }

  return (
    <nav>
      <ul style={{display: "flex"}}>
        {menuTree.items.map(item => <MenuItem key={item.id} {...item}/>)}
      </ul>
    </nav>
  )
}

const MenuItem = ({title, url, items = [], level = 0}) => {
  const [submenuOpen, setSubmenuOpen] = useState(false)

  return (
    <OutsideClickHandler
      onOutsideFocus={() => setSubmenuOpen(false)}
      style={{display: "flex", position: "relative"}}
      component="li"
    >
      <a href={url}>{title}</a>
      {items.length > 0 &&
        <>
          <button
            onclick={() => setSubmenuOpen(!submenuOpen)}
            aria-expanded={submenuOpen}
          >
            <span className="su-visually-hidden">Open {title} Submenu</span>
          </button>

          <ul style={{
            display: submenuOpen ? "block" : "none",
            position: "absolute",
            top: "100%",
            border: "1px solid black",
          }}>

            {items.map(item =>
              <MenuItem key={item.id} {...item} level={level + 1}/>
            )}
          </ul>
        </>
      }
    </OutsideClickHandler>
  )
}

const OutsideClickHandler = ({component, onOutsideFocus, children, ...props}) => {
  const clickCaptured = useRef(false)
  const focusCaptured = useRef(false)

  const documentClick = (event) => {
    if (!clickCaptured.current && onOutsideFocus) {
      onOutsideFocus(event);
    }
    clickCaptured.current = false;
  }

  const documentFocus = (event) => {
    if (!focusCaptured.current && onOutsideFocus) {
      onOutsideFocus(event);
    }
    focusCaptured.current = false;
  }

  useEffect(() => {
    document.addEventListener("mousedown", documentClick);
    document.addEventListener("focusin", documentFocus);
    document.addEventListener("touchstart", documentClick);
    return () => {
      document.removeEventListener("mousedown", documentClick);
      document.removeEventListener("focusin", documentFocus);
      document.removeEventListener("touchstart", documentClick);
    }
  }, [])

  const Element = component || "div"
  return (
    <Element
      onMouseDown={() => clickCaptured.current = true}
      onFocus={() => focusCaptured.current = true}
      onTouchStart={() => clickCaptured.current = true}
      {...props}
    >
      {children}
    </Element>
  )
}

const island = createIslandWebComponent(islandName, MainMenu)
island.render({
  selector: `[data-island="${islandName}"]`,
})

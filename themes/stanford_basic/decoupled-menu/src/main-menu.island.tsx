import {useWebComponentEvents} from "./hooks/useWebComponentEvents";
import {createIslandWebComponent} from 'preact-island'
import {useState, useEffect} from 'preact/hooks';
import {deserialize} from "./tools/deserialize";
import {buildMenuTree} from "./tools/build-menu-tree";
import {DRUPAL_DOMAIN} from './config/env'
import OutsideClickHandler from "./components/outside-click-handler";

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


const island = createIslandWebComponent(islandName, MainMenu)
island.render({
  selector: `[data-island="${islandName}"]`,
})

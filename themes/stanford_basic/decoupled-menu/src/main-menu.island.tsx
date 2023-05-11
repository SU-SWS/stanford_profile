import {useWebComponentEvents} from "./hooks/useWebComponentEvents";
import {createIslandWebComponent} from 'preact-island'
import {useState, useEffect} from 'preact/hooks';
import {deserialize} from "./tools/deserialize";
import {buildMenuTree} from "./tools/build-menu-tree";
import {DRUPAL_DOMAIN} from './config/env'
import OutsideClickHandler from "./components/outside-click-handler";
import Caret from "./components/caret";

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
      <ul style={{display: "flex", flexWrap: "wrap", justifyContent: "flex-end"}}>
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
      style={{
        display: "flex",
        position: "relative",
        marginRight: level === 0 ? "43px" : ""
    }}
      component="li"
    >
      <a href={url} style={{color: "#b1040e"}}>
        {title}
      </a>

      {items.length > 0 &&
        <>
          <div style={{width: "1px", margin: "0 5px", background: "#766253"}}/>
          <button
            onclick={() => setSubmenuOpen(!submenuOpen)}
            aria-expanded={submenuOpen}
            style={{background: "transparent"}}
          >
            <Caret style={{
              transform: submenuOpen ? "rotate(180deg)": "",
              transition: "transform 0.2s ease-in-out",
            }}
            />
            <span className="visually-hidden">{submenuOpen ? "Close" : "Open"} {title} Submenu</span>
          </button>

          <ul style={{
            display: submenuOpen ? "block" : "none",
            position: "absolute",
            top: "100%",
            background: "white",
            boxShadow: "0 10px 20px rgba(0,0,0,.15),0 6px 6px rgba(0,0,0,.2)",
            zIndex: level + 1,
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

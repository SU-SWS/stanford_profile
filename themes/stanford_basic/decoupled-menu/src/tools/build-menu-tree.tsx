export interface MenuContentItem {
  id: string
  title: string
  url: string
  parent: string
  items?: MenuContentItem[]
}

export const buildMenuTree = (links: MenuContentItem[], parent = ""): { items?: MenuContentItem[] } => {
  if (!links?.length) {
    return {
      items: [],
    }
  }

  const children = links.filter((link) => link.parent === parent)

  return children.length
    ? {
      items: children.map((link) => ({
        ...link,
        ...buildMenuTree(links, link.id),
      })),
    }
    : {}
}

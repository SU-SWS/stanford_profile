import Jsona from "jsona";

const dataFormatter = new Jsona()

export const deserialize = (body, options?) => {
  if (!body) return null
  return dataFormatter.deserialize(body, options)
}

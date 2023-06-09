export const isString = (x: unknown): x is string =>
  typeof x === 'string' || x instanceof String

const requiredString = (x: unknown) => {
  if (!isString(x)) {
    throw new Error(`Expected string, got ${typeof x}`)
  }
  return x
}

export const DRUPAL_DOMAIN = requiredString(LOCAL_DRUPAL ?? '')

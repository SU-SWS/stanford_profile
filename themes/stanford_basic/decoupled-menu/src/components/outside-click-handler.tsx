import {useEffect, useRef} from 'preact/hooks';

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
export default OutsideClickHandler;

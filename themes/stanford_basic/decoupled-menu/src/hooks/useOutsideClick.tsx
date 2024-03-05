import { useOnClickOutside} from "usehooks-ts";
import {RefObject} from "preact";

const useOutsideClick = (ref: RefObject<any>, onClickOutside: () => void) => {
  useOnClickOutside(ref, onClickOutside, "mousedown")
  useOnClickOutside(ref, onClickOutside, "touchstart")

  // @ts-ignore Focus in event works the same way as mousedown.
  // @see https://github.com/juliencrn/usehooks-ts/discussions/522
  useOnClickOutside(ref, onClickOutside, "focusin")
}

export default useOutsideClick;

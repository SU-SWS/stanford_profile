import {
  useClearRefinements,
  useRefinementList
} from "react-instantsearch";
import {CheckboxLabel} from "./styled-components";
import {useEffect} from "preact/hooks";
import {useBoolean, useScrollLock, useWindowSize} from 'usehooks-ts'
import {clsx} from "clsx";
import ReactFocusLock from "react-focus-lock"
import {useRef} from "preact/compat";

const RefinementSidebar = () => {
  const topButtonRef = useRef<HTMLButtonElement>(null)
  const {width = 0} = useWindowSize()

  const {refine, canRefine} = useClearRefinements();
  const {
    items: sites,
    refine: refineSites,
  } = useRefinementList({
    attribute: "site_name",
    limit: 100,
    showMore: false,
    sortBy: ["name"]
  })

  const {
    value: isModalOpen,
    setTrue: openModal,
    setFalse: closeModal
  } = useBoolean(false)

  const {
    lock: lockScroll,
    unlock: unlockScroll
  } = useScrollLock({autoLock: false})

  useEffect(() => {
    if (isModalOpen) {
      lockScroll()
      topButtonRef.current?.focus()
    }
    if (!isModalOpen) unlockScroll()
  }, [isModalOpen]);

  useEffect(() => {
    if (width > 768) closeModal()
  }, [width]);

  const isMobile = width <= 768;

  const totalCount = sites.reduce((n, {count}) => n + count, 0)
  const resultCount = sites.filter(site => site.isRefined)
    .reduce((n, {count}) => n + count, 0)

  return (
    <div className="federated-search-facets">
      <button
        className={clsx({hidden: !isMobile})}
        onClick={openModal}>
        Filters
      </button>
      <div className="filter-by">
        <h2 className={clsx({hidden: isMobile})}>Filter By</h2>
        <button
          onClick={refine}
          disabled={!canRefine}
          className={clsx({hidden: isMobile})}
        >
          Reset filters
        </button>
      </div>

      <ReactFocusLock
        returnFocus
        as={isMobile ? "dialog" : "div"}
        disabled={!isModalOpen}
        lockProps={{open: isModalOpen}}
        className={clsx({
          hidden: isMobile && !isModalOpen,
        })}
      >
        <div>
          <button
            ref={topButtonRef}
            onClick={closeModal}
            className={clsx("top-button", {hidden: !isMobile})}
          >
            <i class="fa-solid fa-arrow-left"/>
            Sites ({resultCount === 0 ? totalCount : resultCount})
          </button>

          <fieldset>
            <legend className={clsx({"visually-hidden": isMobile})}>Sites
            </legend>

            {sites.map(site =>
              <CheckboxLabel key={site.label}>
                <input type="checkbox" onChange={() => refineSites(site.value)}
                       checked={site.isRefined}/>
                <span className="checkbox">
                  <i class="fa-solid fa-check"></i>
                </span>
                <span
                  className="label-display">{site.label} ({site.count})</span>
              </CheckboxLabel>
            )}
          </fieldset>
        </div>


        <div className={clsx("mobile-actions", {hidden: !isMobile})}>
          <button onClick={refine} disabled={!canRefine}>Clear all</button>
          <button onClick={closeModal}>View results
          </button>
        </div>

      </ReactFocusLock>
    </div>
  )
}


export default RefinementSidebar;

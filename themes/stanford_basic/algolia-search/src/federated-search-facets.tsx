  import {
    useClearRefinements,
    useRefinementList
  } from "react-instantsearch";
  import {CheckboxLabel} from "./styled-components";
  import { useEffect, useState } from "preact/hooks";

    function useWindowSize() {
      const [windowSize, setWindowSize] = useState({
        width: typeof window !== 'undefined' ? window.innerWidth : 0,
        height: typeof window !== 'undefined' ? window.innerHeight : 0,
      });

      useEffect(() => {
        function handleResize() {
          setWindowSize({
            width: window.innerWidth,
            height: window.innerHeight,
          });
        }

        if (typeof window !== 'undefined') {
          window.addEventListener('resize', handleResize);
          return () => window.removeEventListener('resize', handleResize);
        }
      }, []);

      return windowSize;
    }

    const ClearFilters = () => {
      const { refine } = useClearRefinements();

      return (
        <a
          type="link"
          onClick={refine}
          className="clear-filters-link"
        >
          Reset filters
        </a>
      );
    };

    const RefinementSidebar = () => {
      const {
        items: sites,
        refine: refineSites
      } = useRefinementList({
        attribute: "site_name",
        limit: 100,
        showMore: false,
        sortBy: ["name"]
      })

      const { width } = useWindowSize();
      const isMobile = width <= 768;
      const [isVisible, setIsVisible] = useState(false);
      const toggleVisibility = (e) => {
        e.preventDefault();
        setIsVisible(!isVisible);
      };

      return (
        <>
          <div className={isMobile ? "mobile-federated-search-facets": "federated-search-facets"}>
            <fieldset>
              {isMobile ? (
                <>
                  <a href="" onClick={toggleVisibility} className="filter-link"><h2>Filter </h2>
                    <i class="fa-solid fa-angle-right"></i>
                  </a>
                  <div className="legend-title">
                    <p><i class="fa-solid fa-arrow-left"></i></p>
                      <legend>Sites</legend>
                    </div>
                </>
                ) : (
                <>
                  <h2>Filter by</h2>
                  <legend className="sr-only">Sites</legend>
                  <ClearFilters/>
                </>
                )}

              {sites.map(site =>
                <CheckboxLabel key={site.label}>
                <input type="checkbox" onChange={() => refineSites(site.value)}
                      checked={site.isRefined}/>
                <span className="checkbox">
                  <i class="fa-solid fa-check"></i>
                </span>
                <span className="label-display">{site.label} ({site.count})</span>
                </CheckboxLabel>
              )}
              {isMobile &&
                <div className="mobile-footer-facets">
                  <ClearFilters/>
                  <button className="view-results">View Results</button>
                </div>
              }
            </fieldset>
          </div>
        </>
      );
  }

  export default RefinementSidebar;
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

      return (
        <div>
          {isMobile ? (
            <div className="mobile-federated-search-facets">
              <a  className="filter-link"><h2>Filter By </h2>
                <i class="fa-solid fa-angle-right"></i>
              </a>


            <fieldset>
              <legend>Sites</legend>
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
            </fieldset>
            <ClearFilters/>
          </div>
          ) : (
            <div className="federated-search-facets">
              <h2>Filter by</h2>
              <ClearFilters/>
              <fieldset>
                <legend>Sites</legend>
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
              </fieldset>
            </div>
          )}
        </div>
      );
    }

    export default RefinementSidebar;
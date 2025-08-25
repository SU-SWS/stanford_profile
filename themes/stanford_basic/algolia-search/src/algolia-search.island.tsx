import {liteClient} from 'algoliasearch/lite';
import {createIslandWebComponent} from 'preact-island'
import {
  InstantSearch,
  useHits,
  usePagination,
  useCurrentRefinements
} from 'react-instantsearch';
import {Hit as HitType} from "instantsearch.js/es/types/results";
import SearchBox from "./search-box";
import DefaultHit from "./hits/default-hit";
import {StanfordHit} from "./hits/hit.types";
import {AlgoliaSearchContainer, PaginationList} from "./styled-components";
import ChipsContainer from "./components/chips-container";
import CustomChips from "./components/custom-chips";

const islandName = 'algolia-search'

/* global window */
const appId = window.drupalSettings?.stanfordAlgolia.appId || process.env.ALGOLIA_APP_ID
const key = window.drupalSettings?.stanfordAlgolia.searchKey || process.env.ALGOLIA_KEY

const searchClient = liteClient(appId, key);

const Hit = ({hit, ...props}: {
  hit: HitType<StanfordHit>
  federatedSearch?: boolean
}) => {
  // Customize display based on the hit type.
  return <DefaultHit {...props} hit={hit}/>
}

const CustomHits = ({federatedSearch, ...props}: {
  federatedSearch?: boolean
}) => {
  const {items: hits} = useHits<StanfordHit>(props);
  const {
    currentRefinement: currentPage,
    pages,
    nbPages,
    nbHits,
    refine: goToPage
  } = usePagination({padding: 2})

  if (hits.length === 0) return (
    <div
      className={federatedSearch ? "search-results federated-search" : "search-results"}
    >
      <p>
        <span aria-live="polite" aria-atomic>
          No results for your search.
        </span> Please try another search.
      </p>
    </div>
  )

  const {canRefine} = useCurrentRefinements();

  return (
    <div
      className={federatedSearch ? "search-results federated-search" : "search-results"}
    >
      <h2 className="visually-hidden">Search Results</h2>
      {canRefine && (
        <ChipsContainer>
          <CustomChips/>
        </ChipsContainer>
      )}
      <p className="search-results-count" aria-live="polite" aria-atomic>
        {nbHits} results
      </p>
      <ul className="results">
        {hits.map(hit =>
          <li key={hit.objectID}>
            <Hit hit={hit} federatedSearch={federatedSearch}/>
          </li>
        )}
      </ul>
      Current Page: {currentPage}
      Total Pages: {nbPages}

      {pages.length > 1 && (
        <nav aria-label="Search results pager">
          <PaginationList>

            {currentPage > 0 &&
              <li>
                <button onClick={() => goToPage(currentPage - 1)}>
                  <span className="visually-hidden">Go to previous page</span>
                  Previous
                </button>
              </li>
            }

            {pages.map(pageNum => (
              <li
                key={`page-${pageNum}`}
                aria-current={currentPage === pageNum}
              >
                <button
                  className="page-number"
                  onClick={() => goToPage(pageNum)}
                >
                  {pageNum + 1}
                </button>
              </li>
            ))}

            {currentPage != nbPages - 1 &&
              <li>
                <button onClick={() => goToPage(currentPage + 1)}>
                  <span className="visually-hidden">Go to next page</span>
                  Next
                </button>
              </li>
            }
          </PaginationList>
        </nav>
      )}
    </div>
  )
}

const Search = () => {
  const currentUrl = new URL(window.location.href);
  const initialSearch = currentUrl.searchParams.get('key');
  const searchIndex = window.drupalSettings?.stanfordAlgolia.index || process.env.ALGOLIA_INDEX;
  const federatedSearch = !!window.drupalSettings?.stanfordAlgolia.federatedSearch;

  return (
    <InstantSearch
      searchClient={searchClient}
      indexName={searchIndex}
      insights={true}
      initialUiState={{
        [searchIndex]: {query: initialSearch},
      }}
    >
      <AlgoliaSearchContainer>
        <SearchBox federatedSearch={federatedSearch}/>
        <CustomHits federatedSearch={federatedSearch}/>
      </AlgoliaSearchContainer>
    </InstantSearch>
  )
}

const island = createIslandWebComponent(islandName, Search)
island.render({
  selector: `${islandName}, #${islandName}`,
})

import {liteClient} from 'algoliasearch/lite';
import {createIslandWebComponent} from 'preact-island'
import {
  InstantSearch,
  useHits,
  usePagination
} from 'react-instantsearch';
import {Hit as HitType} from "instantsearch.js/es/types/results";
import SearchBox from "./search-box";
import DefaultHit from "./hits/default-hit";
import {StanfordHit} from "./hits/hit.types";
import {AlgoliaSearchContainer, PaginationList} from "./styled-components";

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
    <p>No results for your search. Please try another search.</p>
  )

  return (
    <div
      className={federatedSearch ? "search-results federated-search" : "search-results"}>
      <h2 className="visually-hidden">Search Results</h2>
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

      {pages.length > 1 && (
        <nav aria-label="Search results pager">
          <PaginationList>
            {pages[0] > 0 && (
              <li className="previous">
                <button onClick={() => goToPage(0)}>
                  <span className="visually-hidden">Go to first page</span>
                  Previous
                </button>
              </li>
            )}

            {pages.map(pageNum => (
              <li
                key={`page-${pageNum}`}
                aria-current={currentPage === pageNum}
              >
                <button className="page-number" onClick={() => goToPage(pageNum)}>
                  {pageNum + 1}
                </button>
              </li>
            ))}

            {pages[pages.length - 1] !== nbPages && (
              <li className="next">
                <button onClick={() => goToPage(nbPages - 1)}>
                  <span className="visually-hidden">Go to last page</span>
                  Next
                </button>
              </li>
            )}
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

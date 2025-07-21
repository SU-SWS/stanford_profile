import {liteClient} from 'algoliasearch/lite';
import {createIslandWebComponent} from 'preact-island'
import {HitsProps, InstantSearch, useHits} from 'react-instantsearch';
import SearchBox from "./search-box";
import EventHit from "./hits/events";
import NewsHit from "./hits/news";
import DefaultHit from "./hits/default-hit";
import styled from "styled-components";
import {StanfordHit} from "./hits/hit.types";

const islandName = 'algolia-search'

/* global window */
const appId = window.drupalSettings?.stanfordAlgolia.appId || process.env.ALGOLIA_APP_ID
const key = window.drupalSettings?.stanfordAlgolia.searchKey || process.env.ALGOLIA_KEY

const searchClient = liteClient(appId, key);

const Hit = ({hit, ...props}: HitsProps<StanfordHit> & {federatedSearch?: boolean}) => {

  if (hit.type === 'Event') return <EventHit {...props} hit={hit}/>
  if (hit.type === 'News') return <NewsHit {...props} hit={hit}/>

  return <DefaultHit {...props} hit={hit}/>
}

const Container = styled.div`
  .search-results {
    margin: 0;
    padding: 0;
    list-style: none;

    &.federated-search {
      float: right;
      width: 60%;
    }
  }

  .federated-search-facets {
    float: left;
    width: 30%;
  }

  li {
    margin-bottom: 30px;
    border-bottom: 1px solid black;

    &:last-child {
      border-bottom: none;
    }
  }
`

const CustomHits = ({federatedSearch, ...props}) => {
  const {items: hits} = useHits(props);
  if (hits.length === 0) return (
    <p>No results for your search. Please try another search.</p>
  )

  return (
    <ul className={federatedSearch ? "search-results federated-search" : "search-results"}>
      {hits.map(hit =>
        <li key={hit.objectID}>
          <Hit hit={hit} federatedSearch={federatedSearch}/>
        </li>
      )}
    </ul>
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
      initialUiState={{
        [searchIndex]: {query: initialSearch},
      }}
    >
      <Container>
        <SearchBox federatedSearch={federatedSearch}/>
        <CustomHits federatedSearch={federatedSearch}/>
      </Container>
    </InstantSearch>
  )
}


const island = createIslandWebComponent(islandName, Search)
island.render({
  selector: `${islandName}, #${islandName}`,
})

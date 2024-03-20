import algoliasearch from 'algoliasearch/lite';
import {createIslandWebComponent} from 'preact-island'
import {
  CurrentRefinements,
  HitsProps,
  InstantSearch, useCurrentRefinements,
  useHits, useRefinementList
} from 'react-instantsearch';
import SearchBox from "./search-box";
import EventHit from "./hits/events";
import NewsHit from "./hits/news";
import DefaultHit from "./hits/default-hit";
import styled from "styled-components";
import {StanfordHit} from "./hits/hit.types";

const islandName = 'algolia-search'

const appId = window.drupalSettings?.stanfordAlgolia.appId || process.env.ALGOLIA_APP_ID
const key = window.drupalSettings?.stanfordAlgolia.searchKey || process.env.ALGOLIA_KEY

const searchClient = algoliasearch(appId, key);

const Hit = ({hit}: HitsProps<StanfordHit>) => {
  if (hit.type === 'Event') return <EventHit hit={hit}/>
  if (hit.type === 'News') return <NewsHit hit={hit}/>

  return <DefaultHit hit={hit}/>
}

const Container = styled.div`
  li {
    margin-bottom: 30px;
    border-bottom: 1px solid black;

    &:last-child {
      border-bottom: none;
    }
  }
`

const CustomHits = (props) => {
  const {hits} = useHits(props);
  if (hits.length === 0) return (
    <p>No results for your search. Please try another search.</p>
  )

  return (
    <ul style={{listStyle: "none", padding: 0}}>
      {hits.map(hit =>
        <li key={hit.objectID}>
          <Hit hit={hit}/>
        </li>
      )}
    </ul>
  )
}

const CustomRefinementList = (props) => {
  const {items, refine, canRefine, createURL} = useRefinementList(props);

  const onChange = (item) => {
    refine(item.value)
  }
  return (
    <ul style={{listStyle: "none"}}>
      {items.sort((a, b) => a.value < b.value ? -1 : 1).map((item, i) =>
        <li key={i}>
          <label>
            <input
              type="checkbox"
              disabled={!canRefine}
              checked={item.isRefined}
              onChange={() => onChange(item)}
            />
            {item.value}
          </label>
        </li>
      )}
    </ul>
  )
}

const CustomCurrentRefinements = (props)=> {
  const { items, canRefine, refine } = useCurrentRefinements(props);

  return (
    <ul style={{listStyle:"none", display: "flex", flexWrap: "wrap", gap:"10px"}}>
      {items.map(refinement => {
       return refinement.refinements.map((item, i) =>
          <li key={`refinement-${i}`}>
            {item.value}
            <button disabled={!canRefine} onClick={() => refine(item)}>Clear</button>
          </li>
        )
      })}
    </ul>
  );
}

const Search = () => {
  const currentUrl = new URL(window.location.href);
  const initialSearch = currentUrl.searchParams.get('key');
  const searchIndex = window.drupalSettings?.stanfordAlgolia.index || process.env.ALGOLIA_INDEX;

  return (
    <InstantSearch
      searchClient={searchClient}
      indexName={searchIndex}
      initialUiState={{
        [searchIndex]: {query: initialSearch},
      }}
    >
      <Container>
        <SearchBox/>
        <CustomRefinementList attribute="news_type"/>
        <CustomCurrentRefinements/>
        <CustomHits/>
      </Container>
    </InstantSearch>
  )
}


const island = createIslandWebComponent(islandName, Search)
island.render({
  selector: `${islandName}, #${islandName}`,
})

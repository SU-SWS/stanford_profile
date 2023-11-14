import algoliasearch from 'algoliasearch/lite';
import styled from "styled-components";
import {createIslandWebComponent} from 'preact-island'
import {Highlight, Hits, InstantSearch, Snippet} from 'react-instantsearch';
import SearchBox from "./search-box";
import EventHit from "./hits/events";
import NewsHit from "./hits/news";

const islandName = 'algolia-search'
const appId = window.drupalSettings?.stanfordAlgolia.appId || process.env.ALGOLIA_APP_ID
const key = window.drupalSettings?.stanfordAlgolia.searchKey || process.env.ALGOLIA_SEARCH_KEY

const searchClient = algoliasearch(appId, key);

const HitContainer = styled.article`
  display: flex;
  flex-direction: column;
  gap: 40px;
  justify-content: left;
  padding: 20px;
  margin-bottom: 20px;

  @media (min-width: 768px) {
    flex-direction: row;
  }

  img {
    max-width: 300px
  }
`

const Hit = ({hit}) => {
  if (hit.type === 'Stanford Event') return <EventHit hit={hit}/>
  if (hit.type === 'Stanford News') return <NewsHit hit={hit}/>

  return (
    <HitContainer className="su-card">
      {hit.photo &&
        <img src={hit.photo} alt=""/>
      }
      <div>
        <h2>
          <a href={hit.url}>
            <Highlight hit={hit} attribute="title">
              {hit.title}
            </Highlight>
          </a>
        </h2>

        <Snippet hit={hit} attribute="rendered"/>
        {hit.updated &&
          <div>Updated: {new Date(hit.updated * 1000).toLocaleDateString('en-us', {month: "long", day: "numeric", year: "numeric"})}</div>
        }
      </div>
    </HitContainer>
  )
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
      <SearchBox/>
      <Hits hitComponent={Hit} classNames={{list: "su-list-unstyled"}}/>
    </InstantSearch>
  )
}


const island = createIslandWebComponent(islandName, Search)
island.render({
  selector: `${islandName}, #${islandName}`,
})

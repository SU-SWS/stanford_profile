import algoliasearch from 'algoliasearch/lite';
import styled from "styled-components";
import {createIslandWebComponent} from 'preact-island'
import {Hits, InstantSearch, Snippet, useInstantSearch, useSearchBox} from 'react-instantsearch';
import {useId, useRef, useState} from "preact/compat";

const islandName = 'algolia-search'
const searchClient = algoliasearch(window.drupalSettings.stanfordAlgolia.appId, window.drupalSettings.stanfordAlgolia.searchKey);

const HitContainer = styled.article`
  display: flex;
  gap: 40px;
  justify-content: left;
  padding: 20px;
  margin-bottom: 20px;

  img {
    max-width: 300px
  }
`

const Hit = ({hit}) => {

  return (
    <HitContainer className="su-card">
      {hit.photo &&
        <img src={hit.photo} alt=""/>
      }
      <div>
        <h2><a href={hit.url}>{hit.title}</a></h2>
        <Snippet hit={hit} attribute="rendered"/>
      </div>
    </HitContainer>
  )
}

const SearchBox = (props) => {
  const {query, refine} = useSearchBox(props);
  const {status} = useInstantSearch();
  const [inputValue, setInputValue] = useState(query);
  const inputRef = useRef(null);
  const inputId = useId();

  const isSearchStalled = status === 'stalled';

  function setQuery(newQuery) {
    setInputValue(newQuery);
    refine(newQuery);
  }

  return (
    <form
      action=""
      role="search"
      noValidate
      onSubmit={(event) => {
        event.preventDefault();
        event.stopPropagation();

        if (inputRef.current) {
          inputRef.current.blur();
        }
      }}
      onReset={(event) => {
        event.preventDefault();
        event.stopPropagation();

        setQuery('');

        if (inputRef.current) {
          inputRef.current.focus();
        }
      }}
      style={{marginBottom: "20px"}}
    >
      <div>
        <label htmlFor={inputId}>
          Keywords<span className="visually-hidden">&nbsp;Search</span>
        </label>
        <input
          id={inputId}
          ref={inputRef}
          autoComplete="off"
          autoCorrect="off"
          autoCapitalize="off"
          spellCheck={false}
          maxLength={512}
          type="search"
          value={inputValue}
          onChange={(event) => {
            setQuery(event.currentTarget.value);
          }}
          autoFocus
        />
      </div>
      <div style={{display: "flex", gap: "10px"}}>
        <button type="submit">Submit</button>
        <button
          type="reset"
          hidden={inputValue.length === 0 || isSearchStalled}
        >
          Reset
        </button>
      </div>
      <span hidden={!isSearchStalled}>Searching…</span>
    </form>
  );
}

const Search = () => {
  const currentUrl = new URL(window.location.href);
  const initialSearch = currentUrl.searchParams.get('key');

  return (
    <InstantSearch
      searchClient={searchClient}
      indexName={window.drupalSettings.stanfordAlgolia.index}
      initialUiState={{
        [window.drupalSettings.stanfordAlgolia.index]: {query: initialSearch},
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

import {
  useRefinementList,
  useSearchBox
} from "react-instantsearch";
import styled from "styled-components";
import {useRef} from "preact/compat";

const SearchBox = ({federatedSearch, ...props}) => {
  const {query, refine} = useSearchBox(props);
  const inputRef = useRef<HTMLInputElement>(null);
  return (
    <form
      className={federatedSearch ? "federated-search" : ""}
      action=""
      role="search"
      noValidate
      onSubmit={e => {
        e.preventDefault();
        e.stopPropagation();
        refine(inputRef.current?.value);
        window.history.replaceState(null, '', `?key=${inputRef.current?.value}`)
      }}
      onReset={e => {
        e.preventDefault();
        e.stopPropagation();
        refine('');
        inputRef.current.value = '';
        inputRef.current?.focus();
      }}
    >
      <div>
        <div>
          <label htmlFor="keyword-search-algolia" className="visually-hidden">
            Keywords Search
          </label>
          <input
            id="keyword-search-algolia"
            ref={inputRef}
            autoComplete="on"
            autoCorrect="on"
            autoCapitalize="off"
            maxLength={128}
            type="search"
            defaultValue={query}
            autoFocus
          />
        </div>
        <div style={{display: "flex", gap: "1rem", marginTop: "1rem"}}>
          <button type="submit">Submit</button>
          <button
            type="reset"
            hidden={query.length === 0}
          >
            Reset
          </button>
        </div>
      </div>

      {federatedSearch &&
        <RefinementSidebar/>
      }
    </form>
  );
}

const CheckboxLabel = styled.label`
  display: flex;
  gap: 10px;
  align-items: center;
  cursor: pointer;

  &:hover, &:focus {
    .label-display {
      text-decoration: underline;
    }
  }

  .checkbox {
    border: 2px solid #979694;
    border-radius: 3px;
    display: block;
    width: 20px;
    height: 20px;
  }

  .check {
    display: none;
  }

  input:checked ~ .checkbox {
    .check {
      display: block;
    }
  }
`

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
  return (
    <div>
      <h2>Filter by</h2>
      <fieldset className="federated-search-facets">
        <legend>Sites</legend>
        {sites.map(site =>
          <CheckboxLabel key={site.label}>
            <input type="checkbox" onChange={() => refineSites(site.value)}
                   checked={site.isRefined}/>
            <span className="checkbox">
              <CheckMark/>
            </span>
            <span className="label-display">{site.label} ({site.count})</span>
          </CheckboxLabel>
        )}
      </fieldset>
    </div>
  )
}

const CheckMark = () => {
  return (
    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="check">
      <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
    </svg>
  )
}

export default SearchBox;

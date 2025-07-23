import {
  useRefinementList,
  useSearchBox
} from "react-instantsearch";
import {useRef} from "preact/compat";
import {CheckboxLabel, SearchForm, SearchInput} from "./styled-components";

const SearchBox = ({federatedSearch}: { federatedSearch?: boolean }) => {
  const {query, refine} = useSearchBox();
  const inputRef = useRef<HTMLInputElement>(null);
  return (
    <SearchForm
      className={federatedSearch ? "federated-search" : ""}
      action=""
      role="search"
      noValidate
      onSubmit={e => {
        e.preventDefault();
        e.stopPropagation();
        refine(inputRef.current?.value || "");
        window.history.replaceState(null, '', `?key=${inputRef.current?.value}`)
      }}
      onReset={e => {
        e.preventDefault();
        e.stopPropagation();
        refine('');

        if (inputRef.current) {
          inputRef.current.value = '';
          inputRef.current.focus();
        }
      }}
    >
      <div>
        <SearchInput>
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
          <button type="submit">
            <i class="fa-solid fa-magnifying-glass"></i>
            <span className="visually-hidden">Submit</span>
          </button>
        </SearchInput>
        <button
          type="reset"
          hidden={query.length === 0}
        >
          Reset
        </button>
      </div>

      {federatedSearch &&
        <RefinementSidebar/>
      }
    </SearchForm>
  );
}

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
    <div className="federated-search-facets">
      <h2>Filter by</h2>
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
  )
}

export default SearchBox;

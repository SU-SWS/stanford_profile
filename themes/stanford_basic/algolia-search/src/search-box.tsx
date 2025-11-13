import {
  useSearchBox
} from "react-instantsearch";
import {useRef} from "preact/compat";
import {SearchForm, SearchInput} from "./styled-components";
import RefinementSidebar from "./federated-search-facets";

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
      <div className="search-input">
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
          <div class="search-buttons">

            <button type="submit">
              <i class="fa-solid fa-magnifying-glass"></i>
              <span className="visually-hidden">Submit search</span>
            </button>
            <span className="divider"/>
            <button
              type="reset"
              hidden={query.length === 0}
            >
              <i class="fa-solid fa-close"></i>
              <span className="visually-hidden">Clear search</span>
            </button>
          </div>
        </SearchInput>

      </div>

      {federatedSearch &&
        <RefinementSidebar/>
      }
    </SearchForm>
  );
}

<RefinementSidebar/>

export default SearchBox;

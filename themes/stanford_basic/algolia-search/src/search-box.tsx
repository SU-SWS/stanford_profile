import {useInstantSearch, useSearchBox} from "react-instantsearch";
import {useRef} from "preact/compat";

const SearchBox = (props) => {
  const {query, refine} = useSearchBox(props);
  const {status} = useInstantSearch();
  const inputRef = useRef<HTMLInputElement>(null);

  return (
    <form
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
      style={{marginBottom: "20px"}}
    >
      <div>
        <label htmlFor="keyword-search-algolia">
          Keywords<span className="visually-hidden">&nbsp;Search</span>
        </label>
        <input
          id="keyword-search-algolia"
          ref={inputRef}
          autoComplete="on"
          autoCorrect="on"
          autoCapitalize="off"
          spellCheck={true}
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
      <StatusMessage status={status} query={query}/>
    </form>
  );
}

const StatusMessage = ({status, query}) => {
  let message = status === 'loading' ? 'Loading' : null;
  if (status != 'loading' && query) {
    message = `Showing results for "${query}"`
  }
  return (
    <div className="visually-hidden" aria-live="polite" aria-atomic>{message}</div>
  )
}
export default SearchBox;

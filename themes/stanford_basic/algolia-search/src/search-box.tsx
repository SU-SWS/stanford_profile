import {useInstantSearch, useSearchBox} from "react-instantsearch";
import {useId, useRef, useState} from "preact/compat";

const SearchBox = (props) => {
  const {query, refine} = useSearchBox(props);
  const {status} = useInstantSearch();
  const [inputValue, setInputValue] = useState(query);
  const inputRef = useRef(null);
  const inputId = useId();

  const isSearchStalled = status === 'stalled';

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
        refine(inputValue);
      }}
      onReset={(event) => {
        event.preventDefault();
        event.stopPropagation();
        setInputValue('');
        refine('');

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
          autoComplete="on"
          autoCorrect="on"
          autoCapitalize="off"
          spellCheck={false}
          maxLength={512}
          type="search"
          value={inputValue}
          onChange={e => setInputValue(e.currentTarget.value)}
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
export default SearchBox;

import {
  useCurrentRefinements,
  useInstantSearch,
  useRefinementList,
  useSearchBox
} from "react-instantsearch";
import {useEffect, useRef, useState} from "preact/compat";

const SearchForm = (props) => {
  const ref = useRef(false)
  const windowSearchParams = new URLSearchParams(window.location.search)
  const {query, refine} = useSearchBox(props);
  const {items: newsTypeRefinements, refine: refineNewsType} = useRefinementList({attribute: "news_type"});
  const [chosenNewsTypes, setChosenNewsTypes] = useState<string[]>(windowSearchParams.get('news-types')?.split(',') || []);
  const {status} = useInstantSearch();
  const inputRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    if (ref.current) return
    ref.current = true;
    chosenNewsTypes.map(newsType => refineNewsType(newsType));
  }, [ref, chosenNewsTypes])

  return (
    <form
      action=""
      role="search"
      noValidate
      onSubmit={e => {
        e.preventDefault();
        e.stopPropagation();
        refine(inputRef.current?.value);

        const addRefinements = chosenNewsTypes.filter(newsType => !newsTypeRefinements.find(item => item.value === newsType).isRefined)
        const removeRefinements = newsTypeRefinements.filter(refinement => refinement.isRefined && !chosenNewsTypes.includes(refinement.value)).map(refinement => refinement.value);

        [...addRefinements, ...removeRefinements].map(newsType => refineNewsType(newsType))

        const searchParams = new URLSearchParams(window.location.search)
        inputRef.current?.value.length > 0 ? searchParams.set('key', inputRef.current?.value): searchParams.delete('key');
        chosenNewsTypes.length > 0 ? searchParams.set('news-types', chosenNewsTypes.join(',')) : searchParams.delete('news-types')

        window.history.replaceState(null, '', `?${searchParams.toString()}`)
      }}
      onReset={e => {
        e.preventDefault();
        e.stopPropagation();
        refine('');
        inputRef.current.value = '';
        inputRef.current?.focus();

        newsTypeRefinements.filter(refinement => refinement.isRefined).map(refinement => refineNewsType(refinement.value));
        setChosenNewsTypes([]);
        const searchParams = new URLSearchParams(window.location.search)
        searchParams.delete('key')
        searchParams.delete('news-types');
        window.history.replaceState(null, '', `?${searchParams.toString()}`)
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

      <ul style={{listStyle: "none"}}>
        {newsTypeRefinements.sort((a, b) => a.count < b.count ? 1 : (a.count === b.count ? (a.value < b.value ? -1 : 1) : -1)).map((item, i) =>
          <li key={i}>
            <label>
              <input
                type="checkbox"
                checked={chosenNewsTypes.findIndex(value => value === item.value) >= 0}
                onChange={(e) => {
                  setChosenNewsTypes(prevTypes => {
                    const newTypes = [...prevTypes];
                    if (e.currentTarget.checked) {
                      newTypes.push(item.value);
                    } else {
                      newTypes.splice(prevTypes.findIndex(value => value === item.value), 1)
                    }
                    return newTypes;
                  });
                }}
              />
              {item.value} ({item.count})
            </label>
          </li>
        )}
      </ul>

      <div style={{display: "flex", gap: "1rem", marginTop: "1rem"}}>
        <button type="submit">Submit</button>
        <button type="reset">
          Reset
        </button>
      </div>
      <StatusMessage status={status} query={query}/>
    </form>
  );
}

const CustomCurrentRefinements = (props) => {
  const {items, canRefine, refine} = useCurrentRefinements(props);

  return (
    <ul style={{
      listStyle: "none",
      display: "flex",
      flexWrap: "wrap",
      gap: "10px"
    }}>
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


const StatusMessage = ({status, query}) => {
  let message = status === 'loading' ? 'Loading' : null;
  if (status != 'loading' && query) {
    message = `Showing results for "${query}"`
  }
  return (
    <div className="visually-hidden" aria-live="polite" aria-atomic>{message}</div>
  )
}
export default SearchForm;

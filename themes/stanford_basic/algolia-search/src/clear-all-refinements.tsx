import { useClearRefinements } from "react-instantsearch";

const ClearFilters = () => {
  const { canRefine, refine } = useClearRefinements();

  return (
    <button
      type="button"
      onClick={refine}
      disabled={!canRefine}
      className="clear-filters-btn"
    >
      Reset filters
    </button>
  );
};




export default ClearFilters;

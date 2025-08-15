import { useCurrentRefinements } from "react-instantsearch";
import { type JSX } from 'preact';
import type { CurrentRefinementsConnectorParamsRefinement } from 'instantsearch.js/es/connectors/current-refinements/connectCurrentRefinements';
import ChipsButton from "./chips-button";
import ChipsItem from "./chips-item";

const CustomChips = () => {

  const { items, refine } = useCurrentRefinements();


  const handleRefine = (e: JSX.TargetedMouseEvent<HTMLButtonElement>, value: CurrentRefinementsConnectorParamsRefinement) => {
    e.preventDefault();
    refine(value);
  }

  const normalizedItems:CurrentRefinementsConnectorParamsRefinement[] = [];
  items.forEach((attribute) => {
    attribute.refinements.forEach((refinement) => {
      normalizedItems.push(refinement);
    });
  });

  return (
    <>
    {normalizedItems.map((item, i) => (
      <ChipsItem key={i}>
        <ChipsButton onClick={(e: JSX.TargetedMouseEvent<HTMLButtonElement>) => handleRefine(e, item)} aria-label={`Clear ${item.label} filter`}>{item.label}</ChipsButton>
      </ChipsItem>
    ))}
    </>

  )
}

export default CustomChips;
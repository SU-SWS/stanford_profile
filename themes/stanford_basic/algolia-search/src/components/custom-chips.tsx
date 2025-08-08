import { useCurrentRefinements } from "react-instantsearch";
import { type JSX } from 'preact';
import type { CurrentRefinementsConnectorParamsRefinement } from 'instantsearch.js/es/connectors/current-refinements/connectCurrentRefinements';
import MobileChipsButton from "./mobile-chips-button";
import MobileChipsItem from "./mobile-chips-item";
import ChipsButton from "./chips-button";
import ChipsItem from "./chips-item";

export type CustomChipsProps = {
  isMobile?: boolean;
}

const CustomChips = ({ isMobile = false }:CustomChipsProps) => {

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


  // Mobile Chips
  if (isMobile) {
    return (
      <>
      {normalizedItems.map((item, i) => (
        <MobileChipsItem key={i}>
          <MobileChipsButton onClick={(e: JSX.TargetedMouseEvent<HTMLButtonElement>) => handleRefine(e, item)} aria-label={`Clear ${item.label} filter`}>{item.label}</MobileChipsButton>
        </MobileChipsItem>
      ))}
      </>

    )
  }

  // Desktop Chips
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
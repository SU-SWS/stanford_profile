import {Highlight, Snippet} from "react-instantsearch";
import {StanfordHit} from "./hit.types";
import {
  DetailsContainer,
  HitContainer,
  ReverseVerticalDisplay
} from "../styled-components";
import {Hit} from "instantsearch.js/es/types/results";

type HitProps =  { federatedSearch?: boolean, hit: Hit<StanfordHit> }

const DefaultHit = ({hit, federatedSearch}: HitProps) => {

  return (
    <HitContainer aria-labelledby={hit.objectID}>
      <DetailsContainer>
        <ReverseVerticalDisplay>
          <h3 id={hit.objectID}>
            <a href={hit.url}>
              {hit.title}
            </a>
          </h3>

          {federatedSearch && (
            <div>
              <div>{hit.site_name}</div>
              <div>{new URL(hit.url).host}</div>
            </div>
          )}
        </ReverseVerticalDisplay>

        {hit.summary &&
          <p className="summary">
            <Highlight hit={hit} attribute="summary"/>
          </p>
        }

        {(!hit.summary && hit.html) &&
          <p>
            <Snippet hit={hit} attribute="html"/>
          </p>
        }

        {hit.updated &&
          <div>
            Last
            Updated: {new Date(hit.updated * 1000).toLocaleDateString('en-us', {
            month: "long",
            day: "numeric",
            year: "numeric"
          })}
          </div>
        }
      </DetailsContainer>
      {hit.photo &&
        <img src={hit.photo} alt=""/>
      }
    </HitContainer>
  )
}

export default DefaultHit;

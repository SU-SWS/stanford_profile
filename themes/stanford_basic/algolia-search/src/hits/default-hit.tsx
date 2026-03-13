import {Highlight, Snippet} from "react-instantsearch";
import {StanfordHit} from "./hit.types";
import {
  DetailsContainer,
  HitContainer,
  ReverseVerticalDisplay
} from "../styled-components";
import {Hit} from "instantsearch.js/es/types/results";

type HitProps = { federatedSearch?: boolean, hit: Hit<StanfordHit> }

const DefaultHit = ({hit, federatedSearch}: HitProps) => {
  const hitDomain = new URL(hit.url).host
  return (
    <HitContainer aria-labelledby={hit.objectID}>
      <DetailsContainer>
        <ReverseVerticalDisplay>
          <h3 id={hit.objectID}>
            <a href={hit.url}>
              {(federatedSearch && hitDomain != window.location.host) &&
                <i class="fa-solid fa-arrow-right"/>
              }
              {hit.title}
            </a>
          </h3>

          {federatedSearch && (
            <div className="federated-site">
              <div className="site-name">{hit.site_name}</div>
              <div className="site-domain">{hitDomain}</div>
            </div>
          )}
        </ReverseVerticalDisplay>

        {hit.summary &&
          <p className="summary">
            <Highlight hit={hit} attribute="summary" highlightedTagName="strong"/>
          </p>
        }

        {(!hit.summary && hit.html) &&
          <p>
            <Snippet hit={hit} attribute="html" highlightedTagName="strong"/>
          </p>
        }

        {hit.updated &&
          <div className="last-updated">
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

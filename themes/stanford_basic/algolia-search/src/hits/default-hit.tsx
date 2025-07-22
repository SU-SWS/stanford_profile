import {Highlight, HitsProps, Snippet} from "react-instantsearch";
import {StanfordHit} from "./hit.types";
import {DetailsContainer, HitContainer} from "../styled-components";

type HitProps = HitsProps<StanfordHit> & { federatedSearch?: boolean }

const DefaultHit = ({hit, federatedSearch}: HitProps) => {
  const hitUrl = new URL(hit.url);

  return (
    <HitContainer>
      <DetailsContainer>
        <div>

          <div style={{display: "flex", "flex-direction": "column-reverse"}}>
            <h2>
              <a href={hit.url.replace(hitUrl.origin, '')}>
                {hit.title}
              </a>
            </h2>

            {federatedSearch && (
              <div>
                <div>{hit.site_name}</div>
                <div>{new URL(hit.url).host}</div>
              </div>
            )}
          </div>

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
        </div>

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
        <img src={hit.photo.replace(hitUrl.origin, '')} alt=""/>
      }
    </HitContainer>
  )
}

export default DefaultHit;

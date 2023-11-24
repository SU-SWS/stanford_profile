import styled from "styled-components";
import {Highlight, Snippet} from "react-instantsearch";

const HitContainer = styled.article`
  display: flex;
  flex-direction: column;
  gap: 40px;
  justify-content: left;
  padding: 20px;
  margin-bottom: 20px;

  @media (min-width: 768px) {
    flex-direction: row;
  }

  img {
    max-width: 200px;
    max-height: 200px;
    object-fit: cover;
    aspect-ratio: 1;
  }
`

const DetailsContainer = styled.div`
  display: flex;
  flex-direction: column;
  justify-content: space-between;
`

const DefaultHit = ({hit}) => {
  const hitUrl = new URL(hit.url);

  return (
    <HitContainer className="su-card">
      {hit.photo &&
        <img src={hit.photo.replace(hitUrl.origin, '')} alt=""/>
      }
      <DetailsContainer>
        <div>
          <h2>
            <a href={hit.url.replace(hitUrl.origin, '')}>
              {hit.title}
            </a>
          </h2>

          <p>
            {hit.summary &&
              <Highlight hit={hit} attribute="summary"/>
            }

            {(!hit.summary && hit.html) &&
              <>
                ...<Snippet hit={hit} attribute="html"/>...
              </>
            }
          </p>
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
    </HitContainer>
  )
}

export default DefaultHit;

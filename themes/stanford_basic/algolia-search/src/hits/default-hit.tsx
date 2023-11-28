import styled from "styled-components";
import {Highlight, Snippet} from "react-instantsearch";

const HitContainer = styled.article`
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  gap: 4rem;
  padding: 2rem 2rem 2rem 0;
  margin-bottom: 2rem;

  @media (min-width: 768px) {
    flex-direction: row;
  }

  img {
    max-width: 300px;
    max-height: 300px;
    object-fit: cover;
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
    <HitContainer>
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
      {hit.photo &&
        <img src={hit.photo.replace(hitUrl.origin, '')} alt=""/>
      }
    </HitContainer>
  )
}

export default DefaultHit;

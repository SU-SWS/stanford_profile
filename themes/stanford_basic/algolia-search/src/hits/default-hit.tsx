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
  return (
    <HitContainer className="su-card">
      {hit.photo &&
        <img src={hit.photo} alt=""/>
      }
      <DetailsContainer>
        <div>
          <h2>
            <a href={hit.url}>
              {hit.title}
            </a>
          </h2>

          <p>
            {hit.summary &&
              <Highlight hit={hit} attribute="summary"/>
            }

            {!hit.summary &&
              <>
                ...<Snippet hit={hit} attribute="rendered"/>...
              </>
            }
          </p>
        </div>

        {hit.updated &&
          <div>Last
            Updated: {new Date(hit.updated * 1000).toLocaleDateString('en-us', {month: "long", day: "numeric", year: "numeric"})}</div>
        }
      </DetailsContainer>
    </HitContainer>
  )
}

export default DefaultHit;

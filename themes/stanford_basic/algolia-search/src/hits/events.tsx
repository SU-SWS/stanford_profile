import {Highlight, Snippet} from "react-instantsearch";
import styled from "styled-components";

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
    max-width: 300px
  }
`

const EventHit = ({hit}) => {

  return (
    <HitContainer className="su-card">
      {hit.photo &&
        <img src={hit.photo} alt=""/>
      }
      <div>
        <h2>
          <a href={hit.url}>
            <Highlight hit={hit} attribute="title">
              {hit.title}
            </Highlight>
          </a>
        </h2>

        <Snippet hit={hit} attribute="rendered"/>
        {hit.updated &&
          <div>Updated: {new Date(hit.updated * 1000).toLocaleDateString('en-us', {month: "long", day: "numeric", year: "numeric"})}</div>
        }
      </div>
    </HitContainer>
  )
}
export default EventHit;

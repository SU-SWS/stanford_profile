import styled from "styled-components";

export const AlgoliaSearchContainer = styled.div`
  .search-results {

    &.federated-search {
      float: right;
      width: 60%;
    }

    ul {
      margin: 0;
      padding: 0;
      list-style: none;
    }
  }

  .federated-search-facets {
    float: left;
    width: 30%;
  }

  li {
    margin-bottom: 30px;
    border-bottom: 1px solid black;

    &:last-child {
      border-bottom: none;
    }
  }
`
export const PaginationList = styled.ul`
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  justify-content: space-between;

  button {
    background: transparent;
    color: #B1040E;
    text-decoration: none;
    border: 0;

    &:hover, &:focus {
      text-decoration: underline;
    }
  }

  li[aria-current="true"] button {
    color: #2E2D29;
    border-bottom: 4px solid #2E2D29;
  }

  .arrow {
    color: #B1040E;
    width: 30px;

    &.left {
      transform: rotate(180deg);
    }
  }
`

export const CheckboxLabel = styled.label`
  display: flex;
  gap: 10px;
  align-items: center;
  cursor: pointer;

  &:hover, &:focus {
    .label-display {
      text-decoration: underline;
    }
  }

  .checkbox {
    border: 2px solid #979694;
    border-radius: 3px;
    display: block;
    width: 20px;
    height: 20px;
  }

  .check {
    display: none;
  }

  input:checked ~ .checkbox {
    .check {
      display: block;
    }
  }
`
export const HitContainer = styled.article`
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

export const DetailsContainer = styled.div`
  display: flex;
  flex-direction: column;
  justify-content: space-between;
`

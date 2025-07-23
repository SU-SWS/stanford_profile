import styled from "styled-components";

export const AlgoliaSearchContainer = styled.div`
  .search-results {
    &.federated-search {
      @media (min-width: 768px) {
        float: right;
        width: 60%;
      }
    }
  }

  .federated-search-facets {
    margin-bottom: 40px;
    
    h2 {
      padding-bottom: 45px;
      border-bottom: 1px solid #979694;
    }

    @media (min-width: 768px) {
      float: left;
      width: 30%;
    }

    fieldset {
      padding: 0;
      margin: 0;
    }
  }

  .results {
    margin: 0;
    padding: 0;
    list-style: none;

    li {
      margin-bottom: 30px;
      border-bottom: 1px solid black;

      &:last-child {
        border-bottom: none;
      }
    }
  }
`

export const SearchForm = styled.form`
  button[type="reset"] {
    margin: 0 auto 30px;
    display: block;
  }
`
export const SearchInput = styled.div`
  position: relative;
  margin: 0 auto 30px;
  max-width: 660px;

  @media (min-width: 768px) {
    width: 70%;
  }

  input {
    max-width: 100%;
    padding-right: 30px;
    border-radius: 50px;
    border: 2px solid #979694;
  }

  button {
    position: absolute;
    top: 0;
    right: 0;
    height: 100%;
    aspect-ratio: 1;
    background: transparent;
    color: #B1040E;
    border: 2px solid transparent;
    border-radius: 50px;

    &:hover, &:focus {
      background: transparent;
      color: #2E2D29;
      border: 2px solid #B1040E;
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

      i {
        border-bottom: 1px solid black;
      }
    }
  }

  li[aria-current="true"] button {
    color: #2E2D29;
    border-bottom: 4px solid #2E2D29;
  }

  i {
    color: #B1040E;
    width: 30px;
    border-bottom: 1px solid transparent;
  }
`

export const CheckboxLabel = styled.label`
  display: flex;
  gap: 10px;
  align-items: center;
  cursor: pointer;
  margin-left: 26px;

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

  .fa-check {
    display: none;
  }

  input:checked ~ .checkbox {
    .fa-check {
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

  .federated-site {
    margin-bottom: 5px;
    .site-name {

    }
    .site-domain {
      color: #585754;
    }
  }

  .fa-arrow-right {
    transform: rotate(-45deg);
    margin-right: 10px;
    font-size: 20px;
    position: relative;
    bottom: 2px;
  }
`

export const DetailsContainer = styled.div`
  display: flex;
  flex-direction: column;
  justify-content: space-between;
`
export const ReverseVerticalDisplay = styled.div`
  display: flex;
  flex-direction: column-reverse;
`

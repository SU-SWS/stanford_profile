import styled from "styled-components";

export const AlgoliaSearchContainer = styled.div`
  .search-results {
    &.federated-search {
      @media (min-width: 768px) {
        float: right;
        width: 60%;
      }

      margin-bottom: 162px;
    }
  }

  .federated-search-facets {
    margin-bottom: 40px;

    h2 {
      padding-bottom: 45px;
      border-bottom: 1px solid #979694;
      font-size: 23px;
    }

    @media (min-width: 768px) {
      float: left;
      width: 30%;
    }

    fieldset {
      padding: 0;
      margin: 0;
    }

    legend {
      font-size: 21px;
      font-weight: 600;
    }
  }

  .search-results-count {
    color: rgba(88, 87, 84, 1);
    font-size: 21px;
  }

  .results {
    margin: 0;
    padding: 0;
    list-style: none;

    li {
      margin-bottom: 30px;
      border-bottom: 1px solid rgba(213, 213, 212, 1);

      &:last-child {
        border-bottom: none;
      }
    }

    .last-updated {
      color: rgba(118, 118, 116, 1);
      font-size: 16px;
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
  justify-content: center;
  align-items: flex-start;

  button {
    background: transparent;
    color: #B1040E;
    text-decoration: none;
    border: 0;
    font-size: 20px;

    &.page-number {
      font-size: 26px;
      font-weight: 600;
      color: #B1040E;

      &:hover, &:focus {
        box-shadow: none;
        border-bottom: 4px solid #820000;

        i {
          border-bottom: 1px solid black;
        }
      }
    }
  }

  .next button,
  .previous button {
    &:hover, &:focus {
      text-decoration: underline;
      color: black;
      box-shadow: none;
      border-bottom: none;
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
    font-size: 16px;
    .site-name {
      color: rgba(46, 45, 41, 1)
    }
    .site-domain {
      color: rgba(88, 87, 84, 1);
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

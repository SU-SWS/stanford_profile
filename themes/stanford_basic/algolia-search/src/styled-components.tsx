import styled from "styled-components";

export const AlgoliaSearchContainer = styled.div`
  .search-results {
    &.federated-search {
      @media (min-width: 768px) {
        float: right;
        width: 60%;
      }

      margin-bottom: 162px;
      margin-top: 61px;
    }
  }

  @media (max-width: 768px) {
    form {
      &.federated-search {
        display: flex;
        flex-diretion: row;
        justify-content: space-between;
        gap: 30px;
        flex: 2 1 auto;
      }
      .search-input {
        flex-grow:2;
      }
    }
  }

  .federated-search-facets {
    margin-bottom: 40px;

    h2 {
      padding-bottom: 45px;
      font-size: 23px;
      margin-bottom: 0;
    }

    .clear-filters-link {
      color: #006CB8;
      font-size: 18px;
      font-weight: 400;
      padding-bottom: 18px;
      margin-bottom: 19px;
      border-bottom: 1px solid #979694;
      display: block;
      text-decoration: none;
      cursor: pointer;

      &:hover,
      &:focus,
      &:active {
        color: #2e2d29;
        text-decoration: underline;
      }
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

  .mobile-federated-search-facets {
    .filter-link {
      display: flex;
      padding: 16px 22px 16px 28px;
      justify-content: space-between;
      align-items: center;
      border-radius: 40px;
      color: #2E2D29;
      border: 1px solid  #C0C0BF;
      background: #FFF;
      cursor: pointer;
      text-decoration: none;

      &:hover,
      &:focus,
      &:active {
        border-radius: 40px;
        border: 1px solid #C0C0BF;
        background: #F4F4F4;
        box-shadow: 0 3px 6px 0 rgba(0, 0, 0, 0.10);
      }

      h2 {
        text-align: center;
        font-size: 21px;
        font-style: normal;
        font-weight: 400;
        margin: 0;
        padding-right: 8px;
      }

      i {
        color: #B1040E;
        text-decoration: none;
      }
    }

    fieldset {
      .legend-title {
        display: flex;
        flex-direction: row;
      }
      i {
        color: #6d6c69;
      }
      legend {
        font-size: 18px;
        font-weight: 400;
        margin: 0 auto;
      }

    }
    .mobile-footer-facets {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      border-top: 1px solid #D5D5D4;
      background: #f4f4f4;
      padding: 26px;
      margin-top: 18px;

      .clear-filters-link {
        color:#767674;
        text-align: center;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        padding: 9px 15px 11px 15px;
        border-radius: 3px;
        border: 1px solid #D5D5D4;
        background: #fff;
        text-decoration: none;
        cursor: pointer;

        &:hover,
        &:focus,
        &:active {
          border-radius: 3px;
          border: 1px solid #85CCFF;
          background: #FFF;
          box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.25);
        }
      }

      .view-results {
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        padding: 9px 13px 11px 13px;
        border-radius: 3px;
        border: 1px solid #85CCFF;
        background-color: #006CB8;
        box-shadow: 0 3px 6px 0 rgba(0, 0, 0, 0.10);

        &:hover,
        &:focus,
        &:active {
          border-color: #006CB8;
          background-color: #00548F;
          text-decoration: underline;
        }
      }
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
    color: #767674;
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

  .search-buttons {
    display: flex;
    flex-direction: row;
    align-items: center;
    position: absolute;
    top: 0;
    right: 0;
    height: 100%;
  }

  button {
    aspect-ratio: 1;
    background: transparent;
    color: #B1040E;
    border: transparent;

    &:hover, &:focus {
      background: transparent;
      color: #e50808;
      border: none;
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
    border-color: #85CCFF;

    .fa-check {
      display: block;
      background-color: #006CB8;
      color: #fff;
      height: 16px;
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

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
  margin-bottom: 40px;

  @media (min-width: 360px) {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
  }

  @media (min-width: 768px) {
    display: block;
    margin-bottom:0;
  }

  .search-input {
    flex-grow: 1;
  }

  .federated-search-facets {
    .filter-by {
      @media (min-width: 768px) {
        padding-bottom: 25px;
        border-bottom: 1px solid #979694;

        button {
          color: #006CB8;
          background-color: transparent;
          text-decoration: none;

          &:hover, &:focus {
            background: transparent;
            color: #2E2D29;
            text-decoration: underline;
            box-shadow: none;
            outline: none;
          }

          &:disabled {
            color: #767674;

            &:hover, &:focus {
              text-decoration: none;
              cursor: default;
            }
          }
        }
      }
    }

    .open-modal {
      color: #2E2D29;
      font-size: 21px;
      font-weight: 400;
      text-decoration: none;
      border-radius: 40px;
      border: 1px solid #C0C0BF;
      background: #fff;
      display: flex;
      flex-direction: row;
      padding: .8rem 2rem 1rem;

      &:hover, &:focus {
        background: transparent;
        color: #2E2D29;
        text-decoration: underline;
        box-shadow: none;
        outline: none;
      }

      i {
        align-self: center;
        padding-left: 5px;
        color: #B1040E;
      }
    }

    @media (min-width: 768px) {
      float: left;
      width: 30%;
      margin-bottom: 40px;
    }

    fieldset {
      padding: 0;
      margin: 0;
    }
  }

  dialog {
    position: fixed;
    top: 0;
    left: 0;
    background: #fff;
    width: 100vw;
    height: 100dvh;
    z-index: 999;
    padding: 0;
    display: flex;
    flex-direction: column;
    justify-content: space-between;

    &.hidden {
      display: none;
    }

    fieldset {
      padding: 20px;
    }

    .top-button {
      display: block;
      width: 100%;
      position: relative;

      i {
        position: absolute;
        left: 30px;
      }
    }

    .mobile-actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 30px;
      border-top: 1px solid #979694;
      background: #D5D5D4;

      .clear-all {
        color: #767674;
        font-size: 16px;
        font-weight: 400;
        border-radius: 3px;
        border: 1px solid #85CCFF;
        background-color: #ffffff;

        &:hover, &:focus {
          border-radius: 3px;
          border: 1px solid #00548F;
          background: #ffffff;
          box-shadow: 0 4px 7px 0 rgba(0, 0, 0, 0.14);
        }

        &:disabled {
          border-radius: 3px;
          border: 1px solid #D5D5D4;
          background-color: #ffffff;
          cursor: default;

          &:hover, &:focus {
            text-decoration: none;
            box-shadow: none;
          }
        }
      }
      .close-modal {
        border-radius: 3px;
        border: 1px solid #85CCFF;
        background-color: #006CB8;
        box-shadow: 0 3px 6px 0 rgba(0, 0, 0, 0.10);
        color: #ffffff;
        font-size: 16px;
        font-weight: 400;

        &:hover, &:focus {
          border: 1px solid #006CB8;
          background: #00548F;
          box-shadow: 0 4px 7px 0 rgba(0, 0, 0, 0.14);
        }
      }
    }
  }
`
export const SearchInput = styled.div`
  position: relative;
  margin: 0 auto;
  max-width: 660px;

  @media (min-width: 768px) {
    width: 70%;
    margin-bottom: 30px;
  }

  input {
    max-width: 100%;
    padding-right: 30px;
    border-radius: 50px;
    border: 2px solid #979694;
  }

  .search-buttons {
    position: absolute;
    top: 0;
    right: 0;
    height: 100%;
    display: flex;
    flex-direction: row-reverse;
    align-items: center;

    button {
      background: transparent;
      color: #B1040E;
      border: 2px solid transparent;
      position: relative;

      &:hover, &:focus {
        background: transparent;
        color: #2E2D29;
        border: 2px solid #B1040E;
        box-shadow: none;
        outline: none;
      }

      &[type="reset"] {
        color: #767674;
      }

    }
    .divider {
      display: block;
      background: #C0C0BF;
      width: 1px;
      height: 60%;
      top: 20%;
      right: -1px;
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
    transition: background-color .25s ease-in-out, color .25s ease-in-out
  }

  .fa-check {
    display: none;
    margin: 2px;
    font-size: 14px;
  }

  input:checked ~ .checkbox {
    background: #006CB8;
    color: #ffffff;
    border: 2px solid #85CCFF;

    .fa-check {
      display: block;
    }
  }

  input:hover ~ .label-display,
  input:focus ~ .label-display {
    text-decoration: underline;
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

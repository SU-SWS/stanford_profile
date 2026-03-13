import styled from 'styled-components';
import preact from 'preact';
import { JSX } from 'preact';

export type ChipsButtonProps = {
  children?: preact.ComponentChildren;
  onClick?: ( e: JSX.TargetedMouseEvent<HTMLElement> ) => void;
};

const ChipsButton = styled.button<ChipsButtonProps>`
  color: #006CB8;
  background-color: transparent;
  font-size: 16px;
  padding: 0;

  &::after {
    content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 11 11' fill='none'%3E%3Cpath d='M6.59834 5.44179L10.32 1.75345C10.68 1.39679 10.6825 0.816786 10.3258 0.456786C9.96918 0.0967863 9.38918 0.0951197 9.02918 0.450953L5.29584 4.15095L1.56334 0.450953C1.20418 0.0951197 0.623342 0.0984529 0.266676 0.45762C-0.089991 0.816786 -0.087491 1.39679 0.272509 1.75345L3.99334 5.44179L0.271676 9.13012C-0.0883243 9.48679 -0.0908242 10.0668 0.265842 10.4268C0.445009 10.6076 0.680842 10.6976 0.916676 10.6976C1.15001 10.6976 1.38334 10.6101 1.56168 10.4326L5.29584 6.73262L9.02834 10.4326C9.20751 10.6101 9.44001 10.6976 9.67334 10.6976C9.90918 10.6976 10.145 10.6076 10.3242 10.4268C10.6808 10.0668 10.6783 9.48679 10.3183 9.13012L6.59834 5.44179Z' fill='%23006CB8'/%3E%3C/svg%3E");
    display: inline-block;
    margin-left: .6rem;
    background-color: transparent;
  }

  &:hover,
  &:focus,
  &:active {
    background-color: transparent;
    color: #00548F;
    box-shadow: none;

    &::after {
      content: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 11 11' fill='none'%3E%3Cpath d='M6.59834 5.44179L10.32 1.75345C10.68 1.39679 10.6825 0.816786 10.3258 0.456786C9.96918 0.0967863 9.38918 0.0951197 9.02918 0.450953L5.29584 4.15095L1.56334 0.450953C1.20418 0.0951197 0.623342 0.0984529 0.266676 0.45762C-0.089991 0.816786 -0.087491 1.39679 0.272509 1.75345L3.99334 5.44179L0.271676 9.13012C-0.0883243 9.48679 -0.0908242 10.0668 0.265842 10.4268C0.445009 10.6076 0.680842 10.6976 0.916676 10.6976C1.15001 10.6976 1.38334 10.6101 1.56168 10.4326L5.29584 6.73262L9.02834 10.4326C9.20751 10.6101 9.44001 10.6976 9.67334 10.6976C9.90918 10.6976 10.145 10.6076 10.3242 10.4268C10.6808 10.0668 10.6783 9.48679 10.3183 9.13012L6.59834 5.44179Z' fill='%2300548F'/%3E%3C/svg%3E");
      background-color: transparent;
    }
  }
`;

export default ChipsButton;
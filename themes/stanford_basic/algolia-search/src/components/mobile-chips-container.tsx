import styled from 'styled-components';

export type MobileChipsContainerProps = {
  children?: preact.ComponentChildren;
};

const MobileChipsContainer = styled.ul<MobileChipsContainerProps>`
  list-style: none;
  display: flex;
  flex-wrap: wrap;
  margin: 0;
  padding: 1.4rem;
  padding-bottom: 0.4rem;
  border-bottom: 1px solid #ccc;
`;

export default MobileChipsContainer;
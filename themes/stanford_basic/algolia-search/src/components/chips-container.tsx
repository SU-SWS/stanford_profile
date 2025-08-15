import styled from 'styled-components';

type ChipsContainerProps = {
  children?: preact.ComponentChildren;
};
const ChipsContainer = styled.ul<ChipsContainerProps>`
  margin-top: 1.5rem;
  margin-bottom: 1.5rem;
  list-style: none;
  padding-left: 0;
  display: flex;
  flex-wrap: wrap;
`;

export default ChipsContainer;
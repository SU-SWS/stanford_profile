import styled from 'styled-components';

export type ChipsItemProps = {
  children?: preact.ComponentChildren;
};

const ChipsItem = styled.li<ChipsItemProps>`
  margin-right: 1.2rem;
  margin-bottom: 1.2rem;
`;

export default ChipsItem;
import styled from 'styled-components';

export type MobileChipsItemProps = {
  children?: preact.ComponentChildren;
};

const MobileChipsItem = styled.li<MobileChipsItemProps>`
  margin-right: 1.2rem;
  margin-bottom: 1.2rem;
`;

export default MobileChipsItem;
import React from 'react';
import styled from 'styled-components';

const Wrapper = styled.div`

`;

const SlugWrapper = styled.div`

`;

export const Slugs = ({filters, terms}) => {

  return (
    <Wrapper>
      <h2>Showing Results For:</h2>
      {Object.keys(filters).map(fieldName => {
        if (typeof terms[fieldName] !== 'undefined') {
          return filters[fieldName].map(tid => (
            <SlugWrapper key={tid}>
              {terms[fieldName].find(item => parseInt(item.id) === parseInt(tid)).label}
            </SlugWrapper>
          ))
        }
      })}
    </Wrapper>
  )
}



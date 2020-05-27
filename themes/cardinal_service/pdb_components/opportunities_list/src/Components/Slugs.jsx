import React from 'react';
import styled from 'styled-components';

const Wrapper = styled.div``;

const SlugWrapper = styled.div`
  border: 1px solid #7a7b7e;
  border-radius: 30px;
  display: inline-block;
  margin-bottom: 10px;
  margin-right: 18px;
  padding: 6px 20px;
  width: auto;
`;

export const Slugs = ({ filters, terms }) => {
  return (
    <Wrapper>
      <h2>Showing Results For:</h2>
      {Object.keys(filters).map((fieldName) => {
        if (typeof terms[fieldName] !== 'undefined') {
          return filters[fieldName].map((tid) => (
            <SlugWrapper key={tid}>
              {
                terms[fieldName].find(
                  (item) => parseInt(item.id) === parseInt(tid)
                ).label
              }
            </SlugWrapper>
          ));
        }
      })}
    </Wrapper>
  );
};

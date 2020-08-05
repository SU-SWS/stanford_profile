import React from 'react';
import ReactDOM from 'react-dom';
import {Filters} from '../../Components/Filters';
import './styles.scss';

const nodeBundle = 'stanford_page';
const nodeFields = [
  {field: 'su_page_resource_audience', label: 'For', multiple: true},
  {field: 'su_page_resource_dimension', label: 'Programs', multiple: true},
  {field: 'su_page_resource_type', label: 'Type', multiple: true},
];

ReactDOM.render(
  <Filters
    bundle={nodeBundle}
    mainFiltersCount={3}
    fields={nodeFields}
  />,
  document.getElementById('resources-filters')
);

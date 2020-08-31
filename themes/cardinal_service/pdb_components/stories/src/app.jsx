import React from 'react';
import ReactDOM from 'react-dom';
import {Filters} from '../../Components/Filters';
import './styles.scss';

const nodeBundle = 'su_spotlight';
const nodeFields = [
  {field: 'su_spotlight_person_type', label: 'By', multiple: true},
  {field: 'su_opp_service_theme', label: 'Service Theme', multiple: true},
  {field: 'su_opp_dimension', label: 'Programs', multiple: true},
  {field: 'su_opp_pathway', label: 'Pathway', multiple: true}
];

ReactDOM.render(
  <Filters
    bundle={nodeBundle}
    mainFiltersCount={4}
    fields={nodeFields}
    header={<h2>Search by</h2>}
  />,
  document.getElementById('stories-filters')
);

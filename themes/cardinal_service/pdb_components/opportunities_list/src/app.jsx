import React from 'react';
import ReactDOM from 'react-dom';
import {Filters} from '../../Components/Filters';

const nodeBundle = 'su_opportunity';
const nodeFields = [
  {field: 'su_opp_type', label: 'Type of Opportunity', multiple: true},
  {field: 'su_opp_open_to', label: 'Open To', multiple: true},
  {field: 'su_opp_service_theme', label: 'Service Theme', multiple: true},
  {field: 'su_opp_deadline_time', label: 'Application Deadline', multiple: true},
  {field: 'su_opp_location', label: 'Location', multiple: true},
  {field: 'su_opp_dimension', label: 'Programs', multiple: true},
  {field: 'su_opp_commitment', label: 'Time Commitment', multiple: true}
];

ReactDOM.render(
  <Filters
    showMoreFilters
    useGrid
    bundle={nodeBundle}
    mainFiltersCount={3}
    fields={nodeFields}
    header={<h2>Search by</h2>}
  />,
  document.getElementById('opportunities-filter-list')
);

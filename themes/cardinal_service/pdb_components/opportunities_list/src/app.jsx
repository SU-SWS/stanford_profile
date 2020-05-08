import React from 'react';
import ReactDOM from 'react-dom';
import {Filters} from "./Components/Filters";

const nodeBundle = 'su_opportunity';
const nodeFields = [
  {field: 'su_opp_type', label: 'Type of Opportunity', multiple: false},
  {field: 'su_opp_open_to', label: 'Open To', multiple: false},
  {field: 'su_opp_service_theme', label: 'Service Theme', multiple: true},
  {field: 'su_opp_time_year', label: 'When', multiple: false,},
  {field: 'su_opp_location', label: 'Location', multiple: true},
  {field: 'su_opp_dimension', label: 'Dimension', multiple: true}
];

ReactDOM.render(
  <Filters bundle={nodeBundle} mainFiltersCount={3} fields={nodeFields}/>,
  document.getElementById('opportunities-filter-list')
);

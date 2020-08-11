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

const getSortUrl = (field, direction = 'ASC') => {
  let currentHref = window.location.href.replace(/sort.*?$/, '').replace(/&+$/, '');
  const separator = currentHref.indexOf('?') === -1 ? '?' : '&';
  return `${currentHref}${separator}sort_by=${field}&sort_order=${direction}`
}

ReactDOM.render(
  <Filters
    showMoreFilters
    useGrid
    bundle={nodeBundle}
    mainFiltersCount={3}
    fields={nodeFields}
    header={<h2>Search by</h2>}
  >
    <div className="centered-container">
      Sort By: <a href={getSortUrl('su_opp_application_deadline_value')}><span className="visually-hidden">Sort By </span>Earliest Deadline</a>
      &nbsp;|&nbsp;
      <a href={getSortUrl('su_opp_application_deadline_value', 'DESC')}><span className="visually-hidden">Sort By </span>Latest Deadline</a>
      &nbsp;|&nbsp;
      <a href={getSortUrl('title')}><span
        className="visually-hidden">Sort By Title </span> A to Z
      </a>
      &nbsp;|&nbsp;
      <a href={getSortUrl('title', 'DESC')}><span
        className="visually-hidden">Sort By Title </span> Z to A
      </a>
      <a style={{float: 'right'}} href="/user/opportunities">
        View Saved Items
      </a>
    </div>
  </Filters>,
  document.getElementById('opportunities-filter-list')
);

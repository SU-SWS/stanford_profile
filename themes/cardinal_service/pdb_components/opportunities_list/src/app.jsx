import React from 'react';
import ReactDOM from 'react-dom';
import {Filters} from '../../Components/Filters';
import './styles.scss';

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

const getSortUrl = (field) => {
  let currentHref = window.location.href.replace(/sort.*?$/, '').replace(/&+$/, '');
  const separator = currentHref.indexOf('?') === -1 ? '?' : '&';
  const direction = sortOrderIsAsc(field) ? 'DESC' : 'ASC';
  return `${currentHref}${separator}sort_by=${field}&sort_order=${direction}`
}

const sortedByField = (field) => {
  return window.location.href.indexOf('sort_by=' + field) > 0;
}

const sortOrderIsAsc = (field) => {
  return sortedByField(field) && window.location.href.indexOf('sort_order=ASC') > 0
}

ReactDOM.render(
  <Filters
    showMoreFilters
    useGrid
    bundle={nodeBundle}
    mainFiltersCount={7}
    fields={nodeFields}
    header={<h2>Search by</h2>}
    wrapperAttributes={{className: "flex-10-of-12"}}
  >
    <div className="flex-10-of-12 sort-links">
      <a
        href={getSortUrl('su_opp_application_deadline_value')}
        className={sortedByField('su_opp_application_deadline_value') ? 'active':''}
      >
        <span className="visually-hidden">Sort By </span>
        Date
        <i style={{marginLeft: '10px'}} className={sortOrderIsAsc('su_opp_application_deadline_value') ? 'fas fa-chevron-up' : 'fas fa-chevron-down'} />
      </a>
      &nbsp;|&nbsp;
      <a
        href={getSortUrl('title')}
        className={sortedByField('title') ? 'active' : ''}
      >
        <span className="visually-hidden">Sort By Title: </span>
        {sortOrderIsAsc('title') ? 'Z to A' : 'A to Z'}
        <i className={sortOrderIsAsc('title') ? 'fas fa-chevron-up' : 'fas fa-chevron-down'} />
      </a>
      <a style={{float: 'right'}} href="/user/opportunities">
        View Saved Items
      </a>
    </div>
  </Filters>,
  document.getElementById('opportunities-filter-list')
);

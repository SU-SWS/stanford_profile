import React, {Component} from 'react';
import {SelectList} from "./Components/SelectList";

const _ = require('lodash');
const lodashUuid = require('lodash-uuid');
const queryString = require('query-string');

class OpportunitiesFilter extends Component {

  fields = [
    {field: 'su_opp_type', label: 'Type of Opportunity', multiple: false},
    {field: 'su_opp_time_year', label: 'When', multiple: false,},
    {field: 'su_opp_open_to', label: 'Open To', multiple: false},
    {field: 'su_opp_location', label: 'Location', multiple: true},
    {field: 'su_opp_dimension', label: 'Dimension', multiple: true},
    {field: 'su_opp_pathway', label: 'Pathway', multiple: true},
    {field: 'su_opp_placement_type', label: 'Placement Type', multiple: true},
    {field: 'su_opp_service_theme', label: 'Service Theme', multiple: true}
  ];

  constructor(props) {
    super(props);
    this.state = {
      allItems: {},
      activeItems: {},
      filters: {},
      showMoreFilters: false
    };
    this.onSelectChange = this.onSelectChange.bind(this);
    this.onFormSubmit = this.onFormSubmit.bind(this);

    if (window.location.search.length > 0) {
      this.state.filters = queryString.parse(window.location.search, {arrayFormat: 'bracket'});
    }
  }

  /**
   * Fetch the API data.
   */
  componentDidMount() {
    const that = this;
    fetch('/api/opportunties')
      .then(response => response.json())
      .then(jsonData => {
        that.setState({
          allItems: _.cloneDeep(jsonData),
          activeItems: _.cloneDeep(jsonData)
        }, this.onSelectChange)
      });
  }

  /**
   * On submit, build the url and send the user there.
   */
  onFormSubmit(e) {
    e.preventDefault();
    if (window.location.search.length === 0 && Object.keys(this.state.filters).length === 0) {
      return;
    }

    let newLocation = window.location.pathname;
    newLocation += '?';
    Object.keys(this.state.filters).map(fieldName => {
      this.state.filters[fieldName].map(tid => {
        newLocation += `&${fieldName}[]=${tid}`;
      })
    });
    newLocation = newLocation.replace('?&', '?');
    window.location = newLocation;
  }

  /**
   * Select list event action passed down to the select component.
   */
  onSelectChange(fieldName, selectedValues) {

    const newState = {...this.state};

    newState.activeItems = _.cloneDeep(newState.allItems);
    // First remove the old selected values from the filters.
    delete newState.filters[fieldName];
    // Populate the filters with the newly chosen values.
    if (selectedValues !== undefined && selectedValues.length > 0) {
      newState.filters[fieldName] = selectedValues;
    }

    // Loop through the items to figure out how many results each option will
    // produce. This will look at each field, then check the other fields for
    // similar entity IDs. It does like if we are on `Field B`: Field A (option
    // 1 OR option 2) AND Field D (option 6).
    Object.keys(newState.activeItems).map(fieldName => {

      // This will be the list of entity IDs that match all of the filtering
      // criteria.
      let validEntities = [];

      // For each field we are adjusting, we need to look at the filters to
      // know what values are selected.
      Object.keys(newState.filters).map(filterFieldName => {
        // For each field we want to consider the other filters, not this filter
        // within this field.
        if (fieldName === filterFieldName) {
          return;
        }

        let filterGroup = [];
        // Each filter we build a long list of entities that are available for
        // each option. So this is a collection of several entities.
        newState.filters[filterFieldName].map(selectedTid => {
          const selectedItem = newState.allItems[filterFieldName].find(a => parseInt(a.id) == parseInt(selectedTid));
          // This concats the valid entity ids and deduplicates it in one shot.
          filterGroup = [...new Set(filterGroup.concat(selectedItem.items))];
        })

        // On the first filter, set the valid entities for the next filter.
        if (validEntities.length === 0) {
          validEntities = [...filterGroup];
        }
        else {
          // Filter out entities that aren't similar to the previous filters.
          validEntities = validEntities.filter(x => filterGroup.includes(x));
        }
      })

      // Now that we know what entity IDs are selected by the other filtered
      // fields, we can change the current field available entity IDs so that
      // we can display the count later.
      if (validEntities.length > 0) {
        newState.activeItems[fieldName].map(term => {
          term.items = validEntities.filter(x => term.items.includes(x));
        })
      }
    })

    this.setState(newState);
  }

  getSelectElement(field) {
    return (
      <SelectList
        key={field.field}
        label={field.label}
        field={field.field}
        onChange={this.onSelectChange}
        options={this.state.activeItems[field.field]}
        multiple={field.multiple}
        defaultValue={this.state.filters[field.field]}
      />
    )
  }

  /**
   * Show or hide the "More Filters" button.
   */
  showHideMoreFilters() {
    const newState = {...this.state};
    newState.showMoreFilters = !this.state.showMoreFilters;
    if (!newState.showMoreFilters) {
      // When a user hides the more filters, clear out those values to prevent
      // confusion.
      const availableFields = this.fields.filter(field => typeof this.state.allItems[field.field] !== 'undefined' && this.state.allItems[field.field].length)
      const moreFilters = availableFields.slice(4);
      moreFilters.map(field => {
        delete newState.filters[field.field];
      })
    }
    this.setState(newState);
  }

  render() {
    // List of fields with available options.
    const availableFields = this.fields.filter(field => typeof this.state.allItems[field.field] !== 'undefined' && this.state.allItems[field.field].length)

    const mainFilters = availableFields.slice(0, 4);
    const moreFilters = availableFields.slice(4);

    // Show the more filter if any of the more filters have values.
    const showMoreFilter = this.state.showMoreFilters || (moreFilters.length > 0 && moreFilters.filter(field => typeof this.state.filters[field.field] !== 'undefined').length > 0);
    const moreFiltersId = lodashUuid.uuid();

    return (
      <div style={{margin: '20px'}}>
        <form onSubmit={this.onFormSubmit}>
          <div style={{
            display: 'flex',
            flexWrap: 'nowrap',
            justifyContent: 'space-between'
          }}>
            {mainFilters.map(field => this.getSelectElement(field))}

            {moreFilters.length > 0 &&
            <button
              type="button"
              aria-controls={moreFiltersId}
              aria-expanded={showMoreFilter}
              onClick={this.showHideMoreFilters.bind(this)}>
              <span
                className="visually-hidden">Show </span>{showMoreFilter ? 'Less' : 'More'} Filters
            </button>
            }

          </div>
          <div
            id={moreFiltersId}
            role="region"
            style={{
              justifyContent: 'space-between',
              display: showMoreFilter ? 'flex' : 'none'
            }}>
            {moreFilters.map(field => this.getSelectElement(field))}
          </div>
          <input type="submit" value="Apply Filters"/>
          <input
            style={{marginLeft: '20px'}}
            type="submit"
            value="Reset"
            onClick={() => this.setState({filters: {}})}
          />
        </form>
      </div>
    )
  }
}

ReactDOM.render(
  <OpportunitiesFilter/>,
  document.getElementById('opportunities-filter-list')
);

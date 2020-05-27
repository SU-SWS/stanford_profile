import React, {Component} from 'react';
import {SelectList} from './SelectList';
import {Slugs} from './Slugs';
import styled from 'styled-components';

const _ = require('lodash');
const lodashUuid = require('lodash-uuid');
const queryString = require('query-string');

const FilterContainer = styled.div`
  background-color: #f4f4f4;
  border-bottom: 1px solid #e5e5e5;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.25);
  margin-bottom: 72px;
  padding: 36px;

  .flex-md-2-of-12 {
    display: flex;
    justify-content: center;
  }

  h2 {
    margin-bottom: 36px;
  }
`;

const FilterWrapper = styled.div`
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 18px;
  margin-bottom: 36px;

  a {
    width: 100%;
  }

  @media (max-width: 768px) {
    grid-template-columns: 1fr;
  }
`;

const FilterOptions = styled.div`
  input {
    display: block;
  }
`;

const MoreFilterWrap = styled.div`
  margin-bottom: 18px;

  .fa-sliders-h {
    font-size: 1.6rem;
    margin-right: 10px;
  }
`;

const ResultsContainer = styled.div`
  margin-bottom: 58px;
`;

export class Filters extends Component {
  initialFilters = {};
  moreFiltersId = lodashUuid.uuid();

  constructor(props) {
    super(props);
    this.state = {
      allItems: {},
      activeItems: {},
      filters: {},
      showMoreFilters: false,
      disabledSearch: true,
    };
    this.onSelectChange = this.onSelectChange.bind(this);
    this.onFormSubmit = this.onFormSubmit.bind(this);

    if (window.location.search.length > 0) {
      const pageParams = queryString.parse(window.location.search, {
        arrayFormat: 'bracket',
      });

      Object.keys(pageParams).map((paramKey) => {
        if (this.props.fields.find((field) => field.field === paramKey)) {
          this.state.filters[paramKey] = pageParams[paramKey];
          this.initialFilters[paramKey] = pageParams[paramKey];
        }
      });
    }
  }

  /**
   * Fetch the API data.
   */
  componentDidMount() {
    const that = this;

    fetch('/api/opportunities')
      .then((response) => response.json())
      .then((jsonData) => {
        that.setState(
          {
            allItems: _.cloneDeep(jsonData),
            activeItems: _.cloneDeep(jsonData),
          },
          this.onSelectChange
        );
      });
  }

  /**
   * On submit, build the url and send the user there.
   */
  onFormSubmit(e) {
    e.preventDefault();
    if (
      window.location.search.length === 0 &&
      Object.keys(this.state.filters).length === 0
    ) {
      return;
    }
    window.location =
      window.location.pathname +
      '?' +
      queryString.stringify(this.state.filters, {arrayFormat: 'bracket'});
  }

  /**
   * Select list event action passed down to the select component.
   */
  onSelectChange(fieldName, selectedValues) {
    const newState = {...this.state};
    if (fieldName !== undefined) {
      newState.disabledSearch = false;
    }

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
    Object.keys(newState.activeItems).map((fieldName) => {
      // This will be the list of entity IDs that match all of the filtering
      // criteria.
      let validEntities = [];

      // For each field we are adjusting, we need to look at the filters to
      // know what values are selected.
      Object.keys(newState.filters).map((filterFieldName) => {
        // For each field we want to consider the other filters, not this filter
        // within this field.
        if (fieldName === filterFieldName) {
          return;
        }

        let filterGroup = [];
        // Each filter we build a long list of entities that are available for
        // each option. So this is a collection of several entities.
        newState.filters[filterFieldName].map((selectedTid) => {
          const selectedItem = newState.allItems[filterFieldName].find(
            (a) => parseInt(a.id) == parseInt(selectedTid)
          );
          // This concats the valid entity ids and deduplicates it in one shot.
          filterGroup = [...new Set(filterGroup.concat(selectedItem.items))];
        });

        // On the first filter, set the valid entities for the next filter.
        if (validEntities.length === 0) {
          validEntities = [...filterGroup];
        }
        else {
          // Filter out entities that aren't similar to the previous filters.
          validEntities = validEntities.filter((x) => filterGroup.includes(x));
        }
      });

      // Now that we know what entity IDs are selected by the other filtered
      // fields, we can change the current field available entity IDs so that
      // we can display the count later.
      if (validEntities.length > 0) {
        newState.activeItems[fieldName].map((term) => {
          term.items = validEntities.filter((x) => term.items.includes(x));
        });
      }
    });

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
    );
  }

  /**
   * Show or hide the "Show More Filters" button.
   */
  showHideMoreFilters(e) {
    e.preventDefault();
    const newState = {...this.state};
    newState.showMoreFilters = !this.state.showMoreFilters;
    if (!newState.showMoreFilters) {
      // When a user hides the more filters, clear out those values to prevent
      // confusion.
      const availableFields = this.props.fields.filter(
        (field) =>
          typeof this.state.allItems[field.field] !== 'undefined' &&
          this.state.allItems[field.field].length
      );
      const moreFilters = availableFields.slice(this.props.mainFiltersCount);
      moreFilters.map((field) => {
        delete newState.filters[field.field];
      });
    }
    this.setState(newState);
  }

  render() {
    // List of fields with available options.
    const availableFields = this.props.fields.filter(
      (field) =>
        typeof this.state.allItems[field.field] !== 'undefined' &&
        this.state.allItems[field.field].length
    );

    const mainFilters = availableFields.slice(0, this.props.mainFiltersCount);
    const moreFilters = availableFields.slice(this.props.mainFiltersCount);

    // Show the more filter if any of the more filters have values.
    const showMoreFilter =
      this.state.showMoreFilters ||
      (moreFilters.length > 0 &&
        moreFilters.filter(
          (field) => typeof this.state.filters[field.field] !== 'undefined'
        ).length > 0);

    return (
      <div>
        <FilterContainer>
          <div class="centered-container">
            <h2>Search by</h2>
            <form onSubmit={this.onFormSubmit}>
              <div class="flex-container">
                <div class="flex-md-10-of-12">
                  <FilterWrapper>
                    {mainFilters.map((field) => this.getSelectElement(field))}
                  </FilterWrapper>
                  <MoreFilterWrap>
                    {moreFilters.length > 0 && (
                      <a
                        href="#"
                        aria-controls={this.moreFiltersId}
                        aria-expanded={showMoreFilter}
                        onClick={this.showHideMoreFilters.bind(this)}
                      >
                        <i class="fas fa-sliders-h"></i>
                        {showMoreFilter ? 'Hide' : 'Show'} More Filters
                      </a>
                    )}
                  </MoreFilterWrap>
                  <FilterWrapper
                    id={this.moreFiltersId}
                    role="region"
                    style={{
                      display: showMoreFilter ? 'grid' : 'none',
                    }}
                  >
                    {moreFilters.map((field) => this.getSelectElement(field))}
                  </FilterWrapper>
                </div>
                <div class="flex-md-2-of-12">
                  <FilterOptions>
                    <input
                      type="submit"
                      value="Search"
                      disabled={this.state.disabledSearch}
                    />

                    {Object.keys(this.initialFilters).length > 0 &&
                    <a href={window.location.pathname}>Clear Filters</a>
                    }
                  </FilterOptions>
                </div>
              </div>
            </form>
          </div>
        </FilterContainer>
        <ResultsContainer>
          <div class="centered-container">
            {Object.keys(this.initialFilters).length > 0 && (
              <Slugs
                filters={this.initialFilters}
                terms={this.state.allItems}
              />
            )}
          </div>
        </ResultsContainer>
      </div>
    );
  }
}

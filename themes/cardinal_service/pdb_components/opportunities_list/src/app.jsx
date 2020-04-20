import React, {Component} from 'react';
import {SelectList} from "./Components/SelectList";

var _ = require('lodash');

class OpportunitiesFilter extends Component {

  fields = [
    {field: 'su_opp_type', label: 'Type of Opportunity'},
    {field: 'su_opp_time_year', label: 'When'},
    {field: 'su_opp_open_to', label: 'Open To'},
    {field: 'su_opp_location', label: 'Location'}
  ];
  multipleSelect = false;

  constructor(props) {
    super(props);
    this.state = {allItems: {}, activeItems: {}, filters: {}};
    this.onSelectChange = this.onSelectChange.bind(this);
    this.onFormSubmit = this.onFormSubmit.bind(this);

    const queryParams = window.location.search;
    if (!queryParams.length) {
      return;
    }

    queryParams.replace('?', '').split('&').map(param => {
      const [key, value] = param.split('=');
      const field = key.replace('[]', '');

      if (this.fields.find(availableField => availableField.field === field)) {
        if (typeof this.state.filters[field] === 'undefined') {
          this.state.filters[field] = [];
        }

        this.state.filters[field].push(value);
      }
    })
  }

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

  onFormSubmit(e) {
    e.preventDefault();
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

  onSelectChange(fieldName, selectedValues) {

    const newState = {...this.state};

    newState.activeItems = _.cloneDeep(newState.allItems);
    delete newState.filters[fieldName];
    if (selectedValues !== undefined && selectedValues.length > 0) {
      newState.filters[fieldName] = selectedValues;
    }

    Object.keys(newState.activeItems).map(fieldName => {
      let validEntities = [];

      Object.keys(newState.filters).map(filterFieldName => {
        if (fieldName === filterFieldName) {
          return;
        }

        let filterGroup = [];
        newState.filters[filterFieldName].map(selectedTid => {
          const selectedItem = newState.allItems[filterFieldName].find(a => parseInt(a.id) == parseInt(selectedTid));
          filterGroup = [...new Set(filterGroup.concat(selectedItem.items))];
        })

        if (validEntities.length === 0) {
          validEntities = [...filterGroup];
        }
        else {
          validEntities = validEntities.filter(x => filterGroup.includes(x));
        }
      })

      if (validEntities.length > 0) {
        newState.activeItems[fieldName].map(term => {
          term.items = validEntities.filter(x => term.items.includes(x));
        })
      }
    })

    this.setState(newState);
  }

  render() {

    return (
      <form onSubmit={this.onFormSubmit}>
        <div style={{
          display: 'flex',
          flexWrap: 'nowrap',
          justifyContent: 'space-between'
        }}>
          {this.fields.map(field => (
            <SelectList
              key={field.field}
              label={field.label}
              field={field.field}
              onChange={this.onSelectChange}
              options={this.state.activeItems[field.field]}
              multiple={this.multipleSelect}
              defaultValue={this.state.filters[field.field]}
            />
          ))}
        </div>
        <input type="submit" value="Apply Filters"/>
        <input
          style={{marginLeft: '20px'}}
          type="submit"
          value="Reset"
          onClick={() => this.setState({filters: {}})}
        />
      </form>
    )
  }
}

ReactDOM.render(
  <OpportunitiesFilter/>,
  document.getElementById('opportunities-filter-list')
);

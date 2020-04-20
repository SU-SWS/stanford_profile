import React, {Component} from 'react';
import Select from 'react-select';

var _ = require('lodash-uuid');

export class SelectList extends Component {

  constructor(props) {
    super(props);
    this.uuid = _.uuid();
    this.state = {selectedItems: []};
    this.onChange = this.onChange.bind(this);
  }

  onChange(selectedOptions) {
    if (selectedOptions === null) {
      this.props.onChange(this.props.field, []);
      return;
    }

    const selections = this.props.multiple ? selectedOptions.map(item => item.value) : [selectedOptions.value];
    this.props.onChange(this.props.field, selections);
  }

  render() {
    if (this.props.options === undefined) {
      return <React.Fragment/>
    }

    const options = this.props.options.map(option => ({
      value: option.id,
      label: <div>
        {option.label} ({option.items.length}
        <span className="visually-hidden">Stuff</span>)
      </div>,
      resultCount: option.items.length
    }))

    let value = [];
    if (this.props.defaultValue !== undefined) {
      this.props.defaultValue.map(tid => {
        const option = options.find(opt => parseInt(opt.value) === parseInt(tid));
        value.push(option)
      })
    }

    return (
      <div style={{width: 'calc(25% - 20px)'}}>
        <label
          htmlFor={this.uuid}
          className="visually-hidden">
          {this.props.label}
        </label>
        <Select
          isClearable
          placeholder={this.props.label}
          inputId={this.uuid}
          options={options}
          isMulti={this.props.multiple}
          isSearchable={options.length > 5}
          onChange={this.onChange}
          isOptionDisabled={option => option.resultCount === 0}
          value={value}
        />
      </div>
    )
  }
}

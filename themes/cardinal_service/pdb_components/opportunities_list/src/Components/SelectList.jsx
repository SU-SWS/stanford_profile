import React, {Component} from 'react';

var _ = require('lodash-uuid');

export class SelectList extends Component {
  constructor(props) {
    super(props);
    this.uuid = _.uuid();
    this.state = {selectedItems: []};
    this.onChange = this.onChange.bind(this);
  }

  onChange(e) {
    var options = e.target.options;
    var value = [];
    for (var i = 0, l = options.length; i < l; i++) {
      if (options[i].selected) {
        value.push(options[i].value);
      }
    }

    this.props.onChange(this.props.field, value.filter(item => item.length));
  }

  render() {
    if (this.props.options === undefined) {
      return <React.Fragment/>
    }

    return (
      <div style={{width: 'calc(25% - 20px)'}}>
        <label htmlFor={this.uuid}>{this.props.label}</label>
        <select
          style={{height: 'auto'}}
          id={this.uuid}
          onChange={this.onChange}
          placeholder={this.props.label}
          multiple={this.props.multiple}
          size={this.props.multiple ? 5 : 1}
        >
          {!this.props.multiple && <option value="">- Choose -</option>}
          {this.props.options.map(optionItem =>
            <option
              key={optionItem.id}
              value={optionItem.id}
              disabled={optionItem.items.length === 0}
              selected={this.props.defaultValue !== undefined && this.props.defaultValue.includes(optionItem.id)}
            >
              {optionItem.label} ({optionItem.items.length})
            </option>
          )}
        </select>
      </div>
    )
  }
}

import React, {Component} from 'react';
import Checkbox from '@material-ui/core/Checkbox';
import TextField from '@material-ui/core/TextField';
import Autocomplete from '@material-ui/lab/Autocomplete';
import CheckBoxOutlineBlankIcon from '@material-ui/icons/CheckBoxOutlineBlank';
import CheckBoxIcon from '@material-ui/icons/CheckBox';

const icon = <CheckBoxOutlineBlankIcon fontSize="small"/>;
const checkedIcon = <CheckBoxIcon fontSize="small"/>;

export const SelectList = ({defaultValue, field, label, multiple, onChange, options}) => {
  let defaultOptions = multiple ? [] : null;
  if (defaultValue !== undefined) {
    defaultOptions = defaultValue.map(tid => {
      return options.find(option => option.id === tid);
    });
    defaultOptions = multiple ? defaultOptions : defaultOptions[0]
  }

  const onSelectionChange = (e, selectedItems) => {
    if (selectedItems === null) {
      onChange(field, []);
      return;
    }

    if (!multiple) {
      onChange(field, [selectedItems.id])
      return;
    }

    const selectedOptions = selectedItems.map(item => item.id);
    onChange(field, selectedOptions)
  }

  return (
    <Autocomplete
      disableCloseOnSelect
      id={field}
      options={options}
      getOptionLabel={option => option.label + ' (' + option.items.length + ')'}
      getOptionDisabled={option => option.items.length <= 0}
      style={{width: 300}}
      multiple={multiple}
      onChange={onSelectionChange}
      getOptionSelected={(option, value) => option.id === value.id}
      value={defaultOptions}
      renderOption={(option, {selected}) => (
        <React.Fragment>
          <Checkbox
            icon={icon}
            checkedIcon={checkedIcon}
            style={{marginRight: 8}}
            checked={selected}
          />
          {option.label + ' (' + option.items.length + ')'}
        </React.Fragment>
      )}
      renderInput={(params) => (
        <TextField {...params} label={label} variant="outlined"
                   InputLabelProps={{style: {marginTop: 0}}}/>
      )}
    />
  )

}

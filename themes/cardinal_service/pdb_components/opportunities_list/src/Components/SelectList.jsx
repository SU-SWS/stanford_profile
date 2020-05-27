import React from 'react';
import Checkbox from '@material-ui/core/Checkbox';
import TextField from '@material-ui/core/TextField';
import Autocomplete from '@material-ui/lab/Autocomplete';
import Chip from '@material-ui/core/Chip';
import CheckBoxOutlineBlankIcon from '@material-ui/icons/CheckBoxOutlineBlank';
import CheckBoxIcon from '@material-ui/icons/CheckBox';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore';
import CloseIcon from '@material-ui/icons/Close';
import styled from 'styled-components';

const icon = <CheckBoxOutlineBlankIcon style={{ width: 18, height: 18 }} />;
const checkedIcon = <CheckBoxIcon style={{ width: 18, height: 18 }} />;
const arrowIcon = <ExpandMoreIcon style={{ fontSize: 30 }} />;

const Container = styled.div`
  .MuiAutocomplete-root {
    width: 100%;
  }

  // Autocomplete Label
  .MuiFormLabel-root {
    font-weight: 400;
    color: #4d4f53;
  }

  .MuiFormLabel-root.MuiInputLabel-shrink,
  .MuiFormLabel-root.Mui-focused {
    font-weight: 600;
    color: #4d4f53;
  }

  .MuiButtonBase-root {
    color: #4d4f53;
  }

  // Autocomplete SelectList Input Slug
  .MuiInputBase-input,
  .MuiChip-root {
    font-size: 16px;
    line-height: normal;
    margin: 5px 0;
  }

  .MuiChip-deleteIcon,
  .MuiChip-deleteIcon:hover {
    background: transparent;
    color: #4d4f53;
  }

  // Autocomplete border-bottom
  .MuiFilledInput-underline {
    &:before,
    &:after {
      border-bottom-color: #2e2d29;
    }
  }

  // Remove Autocomplete Rounded corners
  .MuiFilledInput-root {
    border: 1px solid #d2d3d4;
    border-top-left-radius: unset;
    border-top-right-radius: unset;
  }

  // Autocomplete dropdown arrow
  .MuiAutocomplete-endAdornment {
    top: 10px;
  }

  //Autocomplete Clear X
  .MuiAutocomplete-clearIndicator {
    display: none;
  }

  // Overwrite TextField styles
  input,
  .MuiFilledInput-root,
  .MuiAutocomplete-root .MuiFilledInput-root.Mui-focused {
    background-color: #fff;
    box-shadow: none;

    &:hover,
    &:active,
    &:focus {
      background-color: #fff;
      box-shadow: none;
    }
  }

  // Autocomplete Height
  .MuiAutocomplete-root {
    margin: 4px 0;

    .MuiAutocomplete-inputRoot {
      padding-top: 0;
    }

    &.MuiAutocomplete-hasPopupIcon .MuiAutocomplete-inputRoot {
      &[class*='MuiFilledInput-adornedStart'],
      &[class*='Mui-focused'] {
        padding-top: 24px;
      }
    }
  }
`;

export const SelectList = ({
  defaultValue,
  field,
  label,
  multiple,
  onChange,
  options,
}) => {
  let defaultOptions = multiple ? [] : null;
  if (defaultValue !== undefined) {
    defaultOptions = defaultValue.map((tid) => {
      return options.find((option) => option.id === tid);
    });
    defaultOptions = multiple ? defaultOptions : defaultOptions[0];
  }

  const onSelectionChange = (e, selectedItems) => {
    if (selectedItems === null) {
      onChange(field, []);
      return;
    }

    if (!multiple) {
      onChange(field, [selectedItems.id]);
      return;
    }

    const selectedOptions = selectedItems.map((item) => item.id);
    onChange(field, selectedOptions);
  };

  return (
    <Container>
      <Autocomplete
        // open
        disableCloseOnSelect
        popupIcon={arrowIcon}
        ListboxProps={{ style: { fontSize: '18px' } }}
        className={field + '-select'}
        id={field}
        options={options}
        getOptionLabel={(option) =>
          option.label + ' (' + option.items.length + ')'
        }
        getOptionDisabled={(option) => option.items.length <= 0}
        multiple={multiple}
        onChange={onSelectionChange}
        getOptionSelected={(option, value) => option.id === value.id}
        value={defaultOptions}
        renderOption={(option, { selected }) => (
          <React.Fragment>
            <Checkbox
              className={'checkbox'}
              icon={icon}
              checkedIcon={checkedIcon}
              style={{ marginRight: 8 }}
              checked={selected}
            />
            {option.label + ' (' + option.items.length + ')'}
          </React.Fragment>
        )}
        renderInput={(params) => (
          <TextField
            {...params}
            label={label}
            variant="filled"
            InputLabelProps={{ style: { marginTop: 0, fontSize: '20px' } }}
          />
        )}
      />
    </Container>
  );
};

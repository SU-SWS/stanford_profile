import React from "react";

const NavigationButton = ({previous, double}) => {
  let icon = '›';
  if (previous) {
    icon = double ? '«' : '‹';
  }
  else {
    icon = double ? '»' : '›';
  }

  return (
    <div>
      <span className="visually-hidden">
        {previous ? 'Previous' : 'Next'} {double ? 'Year':'Month'}
      </span>
      <span aria-hidden={true}>
        {icon}
      </span>
    </div>
  )
}

export default NavigationButton;

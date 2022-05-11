import Moment from "moment";
import React from "react";

/**
 * Individual event card component.
 */
const EventCard = ({title, path, su_event_date_time}) => {

  const startTime = Moment(su_event_date_time.value).format('h:mm A');
  const endTime = Moment(su_event_date_time.end_value).format('h:mm A');
  // Smart date stores the values that are all day as midnight and 11:59 PM, so
  // check for those values and provide a string with the duration.
  const duration = startTime === '12:00 AM' && endTime === '11:59 PM' ? 'All Day' : `${startTime} to ${endTime}`

  return (
    <li className="popover-item">
      <a href={path.alias}>{title}</a>
      <div className="duration">{duration}</div>
    </li>
  )
}

export default EventCard;

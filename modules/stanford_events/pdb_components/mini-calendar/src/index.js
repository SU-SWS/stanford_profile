import ReactDOM from 'react-dom';
import React, {useState, useEffect} from 'react';
import {Popover} from "@mui/material";
import Calendar from 'react-calendar';
import qs from 'qs';
import axios from 'axios';
import NavigationButton from "./components/NavigationButton";
import EventCard from "./components/EventCard";
import './styles.scss';

ReactDOM.render(
  <React.StrictMode>
    <App/>
  </React.StrictMode>,
  document.getElementById('event-mini-cal')
);

function App() {
  const apiUrl = '/jsonapi/node/stanford_event';
  const currentDate = new Date();

  const [anchorEl, setAnchorEl] = useState(null);
  const [chosenDate, setChosenDate] = useState('');
  const [events, setEvents] = useState([]);
  const [fetchedUrls, setFetchedUrls] = useState([]);
  const [minDate, setMinDate] = useState(currentDate);
  const [maxDate, setMaxDate] = useState(currentDate);

  useEffect(() => {
    fetchMonthEvents(currentDate.getMonth(), currentDate.getFullYear());

    const params = {
      filters: {status: 1},
      sort: {date: {path: 'su_event_date_time.value', direction: 'ASC'}},
      page: {limit: 1},
    };

    const minUrl = apiUrl + '?' + qs.stringify(params, {encodeValuesOnly: true});
    params.sort.date.direction = 'DESC';
    const maxUrl = apiUrl + '?' + qs.stringify(params, {encodeValuesOnly: true});

    axios.get(minUrl).then(res => {
      if (res.data.data.length >= 1) {
        const min = new Date(res.data.data[0].attributes.su_event_date_time.value)
        // Set the minimum date to the first of the month for the given event.
        min.setDate(1);
        setMinDate(min);
      }
    })
    axios.get(maxUrl).then(res => {
      if (res.data.data.length >= 1) {
        setMaxDate(new Date(res.data.data[0].attributes.su_event_date_time.value));
      }
    })
  }, []);

  /**
   * Call the JSON API and get all the events between the start and end dates.
   *
   * @param url
   *   API URL.
   * @param startDate
   *   Start timestamp for filtering.
   * @param endDate
   *   End timestamp for filtering.
   * @returns {Promise<void>}
   */
  async function getEvents(url, startDate, endDate) {
    const filters = {status: 1};
    if (startDate !== undefined) {
      filters.startDate = {
        condition: {
          path: 'su_event_date_time.value',
          operator: '>=',
          value: startDate
        }
      }
    }
    if (endDate !== undefined) {
      filters.endDate = {
        condition: {
          path: 'su_event_date_time.value',
          operator: '<',
          value: endDate
        }
      }
    }

    // Append the query string to the url.
    if (url.indexOf('?') === -1) {
      url += '?' + qs.stringify({
        filter: filters,
        sort: 'su_event_date_time.value',
      }, {encodeValuesOnly: true});
    }

    // We've already fetched this url, we don't want to fetch it again.
    if (fetchedUrls.indexOf(url) >= 0) {
      return;
    }
    // Make sure we don't fetch this url again in the future.
    setFetchedUrls(previousFetched => [...previousFetched, url]);

    // Fetch the url data, set the state and follow any paginated urls.
    await axios.get(url).then(res => {
      // Set the events state after sorting by the start date & time.
      setEvents(events => [...events, ...res.data.data.map(item => item.attributes)]);

      // Follow the api data to gather all the events possible.
      if (typeof res.data.links.next === 'object') {
        getEvents(decodeURI(res.data.links.next.href));
      }
    })
  }

  /**
   * Fetch events from the api for the given month and year.
   * @param month
   *   Month of the year.
   * @param year
   *   Four digit year.
   */
  const fetchMonthEvents = (month, year) => {
    const firstDay = new Date(year, month, 1).getTime() / 1000;
    const lastDay = new Date(year, month + 1, 1).getTime() / 1000;
    getEvents(apiUrl, firstDay, lastDay);
  }

  /**
   * Grab all events that occur on the given date string.
   *
   * @param date
   *   Date string.
   *
   * @returns array
   *   Array of events.
   */
  const getEventsForDate = (date) => {
    const givenDate = new Date(date);
    return events.filter((event) => {
      const eventDate = new Date(event.su_event_date_time.value);
      // Compare only the date, not the time.
      return givenDate.toLocaleDateString() === eventDate.toLocaleDateString();
    });
  }

  return (
    <div className="events-mini-calender">
      <Calendar
        minDetail="month"
        minDate={minDate}
        maxDate={maxDate}
        nextLabel={<NavigationButton/>}
        next2Label={<NavigationButton double/>}
        prevLabel={<NavigationButton previous/>}
        prev2Label={<NavigationButton previous double/>}
        navigationLabel={({label}) => <span aria-live="polite">{label}</span>}
        tileDisabled={({date, view}) => view === 'month' && !getEventsForDate(date).length}
        onActiveStartDateChange={({activeStartDate})=> fetchMonthEvents(activeStartDate.getMonth(), activeStartDate.getFullYear())}
        onClickDay={(value, event) => {
          setAnchorEl(event.currentTarget);
          setChosenDate(value.toLocaleDateString())
        }}
        tileContent={({date}) => {
          return date.toLocaleDateString() === currentDate.toLocaleDateString() ?
            <span className="visually-hidden"> (Current day)</span> : null
        }}
      />
      <Popover
        className="calender-popover"
        open={Boolean(anchorEl)}
        anchorEl={anchorEl}
        onClose={() => setAnchorEl(null)}
        anchorOrigin={{
          vertical: 'bottom',
          horizontal: 'left',
        }}
        PaperProps={{role: 'dialog', 'aria-label': 'List of Events'}}
      >
        <button
          className="far fa-window-close close-button"
          onClick={() => setAnchorEl(null)}
        >
          <span className="visually-hidden">Close</span>
        </button>

        <ul className="popover-list">
          {getEventsForDate(chosenDate).map(event =>
            <EventCard key={event.drupal_internal__nid} {...event}/>
          )}
        </ul>
      </Popover>
    </div>
  );
}

export type DefaultHit = {
  type: 'Basic Page' | 'Course' | 'Event' | 'Event Series' | 'News' | 'Person' | 'Policy' | 'Publication'
  url: string
  person_full_title?: string
  person_short_title?: string
  photo?: string
  html: string
  summary?: string
  created: number
  status: boolean
  title: string
  updated: number
}

export type NewsHit = DefaultHit & {
  byline?: string
  dek?: string
}

export type PersonHit = DefaultHit & {
  type: 'Person'
  person_full_title?: string
  person_short_title?: string
  email?: string
  phone?: number
}

export type EventHit = DefaultHit & {
  type: 'Event'
  event_end: number
  event_start: number
  dek?: string
  email?: string
  subheadline?: string
}
export type EventSeries = DefaultHit & {
  type: 'Event Series'
  dek?: string
  subheadline?: string
}

export type StanfordHit = EventHit | PersonHit | NewsHit | EventSeries| DefaultHit;

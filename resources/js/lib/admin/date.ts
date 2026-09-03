/** Formats compact dates displayed in Admin listings using the Peruvian locale. */
export const dateFormatter = new Intl.DateTimeFormat('es-PE', {
  day: '2-digit',
  month: 'short',
  year: 'numeric',
})

/**
 * Dashboard date helpers.
 *
 * `formatPeruDashboardDate` and `getPeruDashboardGreeting` use the same fixed
 * time zone so the displayed date and greeting always remain consistent.
 */
const PERU_TIME_ZONE = 'America/Lima'

/** Finds a formatted date part used to compose the dashboard date label. */
function getDatePart(parts: Intl.DateTimeFormatPart[], type: Intl.DateTimeFormatPart['type']): string {
  return parts.find((part) => part.type === type)?.value ?? ''
}

/** Formats the complete date shown in the Admin dashboard header. */
export function formatPeruDashboardDate(date: Date): string {
  const formatter = new Intl.DateTimeFormat('es-PE', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    weekday: 'long',
    timeZone: PERU_TIME_ZONE,
  })

  const parts = formatter.formatToParts(date)

  return `${getDatePart(parts, 'weekday').toLocaleUpperCase('es-PE')} ${getDatePart(parts, 'day')} DE ${getDatePart(parts, 'month').toLocaleUpperCase('es-PE')} DE ${getDatePart(parts, 'year')}`
}

/** Returns the dashboard greeting that corresponds to the current hour in Peru. */
export function getPeruDashboardGreeting(date: Date): string {
  const formatter = new Intl.DateTimeFormat('en-US', {
    hour: 'numeric',
    hourCycle: 'h23',
    timeZone: PERU_TIME_ZONE,
  })

  const hourValue = formatter.formatToParts(date).find((part) => part.type === 'hour')?.value ?? '0'
  const hour = Number.parseInt(hourValue, 10)

  if (hour >= 12 && hour < 19) {
    return 'Buenas tardes'
  }

  if (hour >= 19 || hour < 6) {
    return 'Buenas noches'
  }

  return 'Buenos días'
}

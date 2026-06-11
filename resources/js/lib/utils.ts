import type { InertiaLinkProps } from '@inertiajs/react'
import { clsx } from 'clsx'
import type { ClassValue } from 'clsx'
import { twMerge } from 'tailwind-merge'

export const dateFormatter = new Intl.DateTimeFormat('es-PE', {
  day: '2-digit',
  month: 'short',
  year: 'numeric',
})

const PERU_TIME_ZONE = 'America/Lima'

function getDatePart(parts: Intl.DateTimeFormatPart[], type: Intl.DateTimeFormatPart['type']): string {
  return parts.find((part) => part.type === type)?.value ?? ''
}

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

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

export function toUrl(url: NonNullable<InertiaLinkProps['href']>): string {
  return typeof url === 'string' ? url : url.url
}

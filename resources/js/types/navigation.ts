import type { IconSvgElement } from '@hugeicons/react'
import type { InertiaLinkProps } from '@inertiajs/react'

export type BreadcrumbItem = {
  title: string
  href: NonNullable<InertiaLinkProps['href']>
}

export type NavGroup = {
  title: string
  items: NavItem[]
}

export type NavItem = {
  title: string
  href: NonNullable<InertiaLinkProps['href']>
  icon?: IconSvgElement | null
  isActive?: boolean
}

import type { ReactNode } from 'react'
import type { BreadcrumbItem } from '@/types/admin/navigation'

export type AppLayoutProps = {
  children: ReactNode
  breadcrumbs?: BreadcrumbItem[]
}

export type AppVariant = 'header' | 'sidebar'

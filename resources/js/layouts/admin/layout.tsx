import type { PropsWithChildren } from 'react'
import { AppContent } from '@/components/app-content'
import { AppShell } from '@/components/app-shell'
import { AppSidebar } from '@/components/app-sidebar'
import { AppSidebarHeader } from '@/components/app-sidebar-header'
import type { BreadcrumbItem } from '@/types'

interface LayoutProps extends PropsWithChildren {
  breadcrumbs?: BreadcrumbItem[]
}

const Layout = ({ breadcrumbs = [], children }: LayoutProps) => (
  <AppShell variant="sidebar">
    <AppSidebar />
    <AppContent
      variant="sidebar"
      className="overflow-x-hidden"
    >
      <AppSidebarHeader breadcrumbs={breadcrumbs} />
      {children}
    </AppContent>
  </AppShell>
)

export default Layout

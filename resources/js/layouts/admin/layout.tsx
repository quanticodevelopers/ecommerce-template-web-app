import type { PropsWithChildren } from 'react'
import { AppContent } from '@/components/admin/app-content'
import { AppShell } from '@/components/admin/app-shell'
import { AppSidebar } from '@/components/admin/app-sidebar'
import { AppSidebarHeader } from '@/components/admin/app-sidebar-header'
import AdminProviders from '@/components/admin/providers'
import type { BreadcrumbItem } from '@/types'

interface LayoutProps extends PropsWithChildren {
  breadcrumbs?: BreadcrumbItem[]
}

const Layout = ({ breadcrumbs = [], children }: LayoutProps) => (
  <AdminProviders>
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
  </AdminProviders>
)

export default Layout

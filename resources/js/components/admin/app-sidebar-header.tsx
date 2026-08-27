import { Breadcrumbs } from '@/components/admin/breadcrumbs'
import HeaderUserMenu from '@/components/admin/header-user-menu'
import MessagesDropdown from '@/components/admin/messages-dropdown'
import NotificationsDropdown from '@/components/admin/notifications-dropdown'
import { Separator } from '@/components/admin/ui/separator'
import { SidebarTrigger } from '@/components/admin/ui/sidebar'
import type { BreadcrumbItem as BreadcrumbItemType } from '@/types'

export function AppSidebarHeader({ breadcrumbs = [] }: { breadcrumbs?: BreadcrumbItemType[] }) {
  return (
    <header className="border-sidebar-border/50 group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 flex h-16 shrink-0 items-center justify-between gap-2 border-b px-6 transition-[width,height] ease-linear md:px-4">
      <div className="flex h-6 items-center gap-4">
        <SidebarTrigger className="-ml-1" />
        <Separator orientation="vertical" />
        <Breadcrumbs breadcrumbs={breadcrumbs} />
      </div>
      <div className="hidden items-center gap-2 md:flex">
        <MessagesDropdown />
        <NotificationsDropdown />
        <Separator orientation="vertical" />
        <HeaderUserMenu />
      </div>
    </header>
  )
}

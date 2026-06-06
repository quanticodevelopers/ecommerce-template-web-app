import { Link, usePage } from '@inertiajs/react'
import { LayoutGrid, SquareArrowOutUpRightIcon } from 'lucide-react'
import { useMemo } from 'react'
import AppLogo from '@/components/app-logo'
import { NavFooter } from '@/components/nav-footer'
import { NavMain } from '@/components/nav-main'
import { NavUser } from '@/components/nav-user'
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar'
import { dashboard } from '@/routes'
import type { NavGroup, NavItem } from '@/types'

const mainNavItems: NavGroup[] = [
  {
    title: 'Plataforma',
    items: [
      {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
      },
    ],
  },
]

export function AppSidebar() {
  const { url } = usePage().props

  const footerNavItems: NavItem[] = useMemo(
    () => [
      {
        title: 'Ir a la web',
        href: url,
        icon: SquareArrowOutUpRightIcon,
      },
    ],
    [url],
  )

  return (
    <Sidebar
      collapsible="icon"
      variant="inset"
    >
      <SidebarHeader>
        <SidebarMenu>
          <SidebarMenuItem>
            <SidebarMenuButton
              size="lg"
              asChild
            >
              <Link
                href={dashboard()}
                prefetch
              >
                <AppLogo />
              </Link>
            </SidebarMenuButton>
          </SidebarMenuItem>
        </SidebarMenu>
      </SidebarHeader>

      <SidebarContent>
        <NavMain group={mainNavItems} />
      </SidebarContent>

      <SidebarFooter>
        <NavFooter
          items={footerNavItems}
          className="mt-auto"
        />
        <NavUser />
      </SidebarFooter>
    </Sidebar>
  )
}

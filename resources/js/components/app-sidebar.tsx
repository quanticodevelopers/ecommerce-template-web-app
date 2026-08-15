import { DashboardBrowsingIcon, GridViewIcon, Settings01Icon, ShoppingBag01Icon, SquareArrowUpRightIcon, Tag01Icon, UserMultiple02Icon } from '@hugeicons/core-free-icons'
import { Link, usePage } from '@inertiajs/react'
import { useMemo } from 'react'
import AppLogo from '@/components/app-logo'
import { NavFooter } from '@/components/nav-footer'
import { NavMain } from '@/components/nav-main'
import { NavUser } from '@/components/nav-user'
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar'
import { dashboard } from '@/routes/admin'
import { index as adminBrands } from '@/routes/admin/brands'
import { index as adminCategories } from '@/routes/admin/categories'
import { index as adminCustomers } from '@/routes/admin/customers'
import { edit as editInformation } from '@/routes/admin/information'
import { index as adminUsers } from '@/routes/admin/users'
import type { NavGroup, NavItem } from '@/types'

const mainNavItems: NavGroup[] = [
  {
    title: 'Plataforma',
    items: [
      {
        title: 'Dashboard',
        href: dashboard(),
        icon: DashboardBrowsingIcon,
      },
    ],
  },
  {
    title: 'Catálogo',
    items: [
      {
        title: 'Categorías',
        href: adminCategories(),
        icon: GridViewIcon,
      },
      {
        title: 'Marcas',
        href: adminBrands(),
        icon: Tag01Icon,
      },
    ],
  },
  {
    title: 'Tienda',
    items: [
      {
        title: 'Clientes',
        href: adminCustomers(),
        icon: ShoppingBag01Icon,
      },
    ],
  },
  {
    title: 'Administración',
    items: [
      {
        title: 'Usuarios',
        href: adminUsers(),
        icon: UserMultiple02Icon,
      },
      {
        title: 'Configuración',
        href: editInformation(),
        icon: Settings01Icon,
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
        icon: SquareArrowUpRightIcon,
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

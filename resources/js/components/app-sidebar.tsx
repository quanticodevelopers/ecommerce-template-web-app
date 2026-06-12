import { Link, usePage } from '@inertiajs/react'
import { BlocksIcon, LayoutDashboardIcon, SettingsIcon, ShoppingBagIcon, SquareArrowOutUpRightIcon, TagIcon, UsersIcon } from 'lucide-react'
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
        icon: LayoutDashboardIcon,
      },
    ],
  },
  {
    title: 'Catálogo',
    items: [
      {
        title: 'Categorías',
        href: adminCategories(),
        icon: BlocksIcon,
      },
      {
        title: 'Marcas',
        href: adminBrands(),
        icon: TagIcon,
      },
    ],
  },
  {
    title: 'Tienda',
    items: [
      {
        title: 'Clientes',
        href: adminCustomers(),
        icon: ShoppingBagIcon,
      },
    ],
  },
  {
    title: 'Administración',
    items: [
      {
        title: 'Usuarios',
        href: adminUsers(),
        icon: UsersIcon,
      },
      {
        title: 'Configuración',
        href: editInformation(),
        icon: SettingsIcon,
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

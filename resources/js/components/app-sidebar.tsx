import { DashboardBrowsingIcon, GridViewIcon, Package01Icon, Settings01Icon, ShoppingBag01Icon, Tag01Icon, UserMultiple02Icon } from '@hugeicons/core-free-icons'
import { Link } from '@inertiajs/react'
import AppLogo from '@/components/app-logo'
import NavFooter from '@/components/nav-footer'
import { NavMain } from '@/components/nav-main'
import { NavUser } from '@/components/nav-user'
import SidebarLogoutButton from '@/components/sidebar-logout-button'
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar'
import { dashboard } from '@/routes/admin'
import { index as adminBrands } from '@/routes/admin/brands'
import { index as adminCategories } from '@/routes/admin/categories'
import { index as adminCustomers } from '@/routes/admin/customers'
import { edit as editInformation } from '@/routes/admin/information'
import { index as adminIndexProducts } from '@/routes/admin/products'
import { create as adminCreateProducts } from '@/routes/admin/products'
import { index as adminUsers } from '@/routes/admin/users'
import type { NavGroup } from '@/types'

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
        title: 'Productos',
        href: '#',
        icon: Package01Icon,
        subItems: [
          {
            title: 'Todos los productos',
            href: adminIndexProducts(),
          },
          {
            title: 'Nuevo producto',
            href: adminCreateProducts(),
          },
        ],
      },
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
        <NavFooter className="mt-auto" />
        <SidebarLogoutButton className="hidden lg:block" />
        <NavUser className="block md:hidden" />
      </SidebarFooter>
    </Sidebar>
  )
}

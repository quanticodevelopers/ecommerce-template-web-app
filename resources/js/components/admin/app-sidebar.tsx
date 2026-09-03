import { DashboardBrowsingIcon, GridViewIcon, Package01Icon, Settings01Icon, ShoppingBag01Icon, Tag01Icon, UserMultiple02Icon } from '@hugeicons/core-free-icons'
import { Link } from '@inertiajs/react'
import AppLogo from '@/components/admin/app-logo'
import NavFooter from '@/components/admin/nav-footer'
import { NavMain } from '@/components/admin/nav-main'
import { NavUser } from '@/components/admin/nav-user'
import SidebarLogoutButton from '@/components/admin/sidebar-logout-button'
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/admin/ui/sidebar'
import { dashboard } from '@/routes/admin'
import { index as adminAdministrators } from '@/routes/admin/admins'
import { index as adminBrands } from '@/routes/admin/brands'
import { index as adminCategories } from '@/routes/admin/categories'
import { index as adminCustomers } from '@/routes/admin/customers'
import { edit as editInformation } from '@/routes/admin/information'
import { index as adminIndexProducts } from '@/routes/admin/products'
import { create as adminCreateProducts } from '@/routes/admin/products'
import type { NavGroup } from '@/types/admin'

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
        title: 'Administradores',
        href: adminAdministrators(),
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

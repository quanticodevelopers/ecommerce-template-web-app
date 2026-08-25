import { Logout01Icon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Link } from '@inertiajs/react'
import { SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar'
import { useLogoutCleanup } from '@/hooks/use-logout-cleanup'
import { logout } from '@/routes/admin/auth'

interface SidebarLogoutButtonProps {
  className?: string
}

function SidebarLogoutButton({ className = '' }: SidebarLogoutButtonProps) {
  const handleLogout = useLogoutCleanup()

  return (
    <SidebarMenu className={className}>
      <SidebarMenuItem>
        <SidebarMenuButton
          asChild
          tooltip="Cerrar sesión"
        >
          <Link
            href={logout()}
            as="button"
            onClick={handleLogout}
            data-test="logout-button"
          >
            <HugeiconsIcon
              icon={Logout01Icon}
              strokeWidth={1.5}
            />
            <span>Cerrar sesión</span>
          </Link>
        </SidebarMenuButton>
      </SidebarMenuItem>
    </SidebarMenu>
  )
}

export default SidebarLogoutButton

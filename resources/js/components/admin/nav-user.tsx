import { UnfoldMoreIcon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/admin/ui/dropdown-menu'
import { SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from '@/components/admin/ui/sidebar'
import { UserInfo } from '@/components/admin/user-info'
import { UserMenuContent } from '@/components/admin/user-menu-content'
import { useAuthenticatedAdministrator } from '@/hooks/admin/use-authenticated-administrator'
import { useIsMobile } from '@/hooks/use-mobile'

interface NavUserProps {
  className?: string
}

export function NavUser({ className = '' }: NavUserProps) {
  const user = useAuthenticatedAdministrator()
  const { state } = useSidebar()
  const isMobile = useIsMobile()

  return (
    <SidebarMenu className={className}>
      <SidebarMenuItem>
        <DropdownMenu>
          <DropdownMenuTrigger asChild>
            <SidebarMenuButton
              size="lg"
              className="text-sidebar-accent-foreground data-[state=open]:bg-sidebar-accent group"
              data-test="sidebar-menu-button"
            >
              <UserInfo
                user={user}
                showRole
              />
              <HugeiconsIcon
                icon={UnfoldMoreIcon}
                className="ml-auto"
                strokeWidth={1.5}
              />
            </SidebarMenuButton>
          </DropdownMenuTrigger>
          <DropdownMenuContent
            className="w-(--radix-dropdown-menu-trigger-width) min-w-56 rounded-lg"
            align="end"
            side={isMobile ? 'bottom' : state === 'collapsed' ? 'left' : 'bottom'}
          >
            <UserMenuContent user={user} />
          </DropdownMenuContent>
        </DropdownMenu>
      </SidebarMenuItem>
    </SidebarMenu>
  )
}

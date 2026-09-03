import { UnfoldMoreIcon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Button } from '@/components/admin/ui/button'
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/admin/ui/dropdown-menu'
import { UserInfo } from '@/components/admin/user-info'
import { UserMenuContent } from '@/components/admin/user-menu-content'
import { useAuthenticatedAdministrator } from '@/hooks/admin/use-authenticated-administrator'

interface HeaderUserMenuProps {
  className?: string
}

function HeaderUserMenu({ className }: HeaderUserMenuProps) {
  const user = useAuthenticatedAdministrator()

  return (
    <div className={className}>
      <DropdownMenu>
        <DropdownMenuTrigger asChild>
          <Button
            variant="ghost"
            size="lg"
          >
            <UserInfo
              user={user}
              showRole={true}
            />
            <HugeiconsIcon
              icon={UnfoldMoreIcon}
              className="size-4"
              strokeWidth={1.5}
            />
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent
          className="w-(--radix-dropdown-menu-trigger-width) min-w-56"
          align="end"
        >
          <UserMenuContent user={user} />
        </DropdownMenuContent>
      </DropdownMenu>
    </div>
  )
}

export default HeaderUserMenu

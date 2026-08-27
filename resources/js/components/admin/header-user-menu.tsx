import { UnfoldMoreIcon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { usePage } from '@inertiajs/react'
import { Button } from '@/components/admin/ui/button'
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/admin/ui/dropdown-menu'
import { UserInfo } from '@/components/admin/user-info'
import { UserMenuContent } from '@/components/admin/user-menu-content'

interface HeaderUserMenuProps {
  className?: string
}

function HeaderUserMenu({ className }: HeaderUserMenuProps) {
  const { auth } = usePage().props

  if (!auth.user) {
    return null
  }

  return (
    <div className={className}>
      <DropdownMenu>
        <DropdownMenuTrigger asChild>
          <Button
            variant="ghost"
            size="lg"
          >
            <UserInfo
              user={auth.user}
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
          <UserMenuContent user={auth.user} />
        </DropdownMenuContent>
      </DropdownMenu>
    </div>
  )
}

export default HeaderUserMenu

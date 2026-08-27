import { BellIcon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Button } from '@/components/admin/ui/button'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/admin/ui/dropdown-menu'

interface NotificationsDropdownProps {
  className?: string
}

function NotificationsDropdown({ className = '' }: NotificationsDropdownProps) {
  return (
    <div className={className}>
      <DropdownMenu>
        <DropdownMenuTrigger asChild>
          <Button
            variant="ghost"
            size="icon"
            aria-label="Visualizar notificaciones"
            title="Visualizar notificaciones"
          >
            <HugeiconsIcon
              icon={BellIcon}
              strokeWidth={1.5}
            />
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent
          className="w-(--radix-dropdown-menu-trigger-width) min-w-48"
          align="end"
        >
          <DropdownMenuItem>Sin notificaciones.</DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </div>
  )
}

export default NotificationsDropdown

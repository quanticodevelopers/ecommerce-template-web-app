import { SquareArrowUpRightIcon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { usePage } from '@inertiajs/react'
import AppearanceDropdown from '@/components/appearance-dropdown'
import { Button } from '@/components/ui/button'
import { SidebarMenu, SidebarMenuItem } from '@/components/ui/sidebar'
import { toUrl } from '@/lib/utils'

interface SidebarFooterActionsProps {
  className?: string
}

function SidebarFooterActions({ className = '' }: SidebarFooterActionsProps) {
  const { url } = usePage().props

  return (
    <SidebarMenu className={className}>
      <SidebarMenuItem>
        <div className="grid grid-cols-2 gap-1 group-data-[collapsible=icon]:grid-cols-1">
          <Button
            variant="ghost"
            className="font-normal group-data-[collapsible=icon]:size-8 group-data-[collapsible=icon]:px-0"
            asChild
          >
            <a
              href={toUrl(url)}
              target="_blank"
              rel="noopener noreferrer"
              aria-label="Ir a la web"
              title="Ir a la web"
            >
              <HugeiconsIcon icon={SquareArrowUpRightIcon} />
              <span className="group-data-[collapsible=icon]:hidden">Ir a la web</span>
            </a>
          </Button>
          <AppearanceDropdown />
        </div>
      </SidebarMenuItem>
    </SidebarMenu>
  )
}

export default SidebarFooterActions

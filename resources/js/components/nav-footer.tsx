import { SquareArrowUpRightIcon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { usePage } from '@inertiajs/react'
import type { ComponentPropsWithoutRef } from 'react'
import AppearanceDropdown from '@/components/appearance-dropdown'
import { SidebarGroup, SidebarGroupContent, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar'
import { cn, toUrl } from '@/lib/utils'

type NavFooterProps = ComponentPropsWithoutRef<typeof SidebarGroup>

function NavFooter({ className, ...props }: NavFooterProps) {
  const { url } = usePage().props

  return (
    <SidebarGroup
      {...props}
      className={cn('group-data-[collapsible=icon]:p-0', className)}
    >
      <SidebarGroupContent>
        <SidebarMenu>
          <SidebarMenuItem>
            <AppearanceDropdown />
          </SidebarMenuItem>

          <SidebarMenuItem>
            <SidebarMenuButton
              asChild
              className="text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100"
            >
              <a
                href={toUrl(url)}
                target="_blank"
                rel="noopener noreferrer"
                aria-label="Ir a la web"
                title="Ir a la web"
              >
                <HugeiconsIcon
                  icon={SquareArrowUpRightIcon}
                  className="size-5"
                />
                <span>Ir a la web</span>
              </a>
            </SidebarMenuButton>
          </SidebarMenuItem>
        </SidebarMenu>
      </SidebarGroupContent>
    </SidebarGroup>
  )
}

export default NavFooter

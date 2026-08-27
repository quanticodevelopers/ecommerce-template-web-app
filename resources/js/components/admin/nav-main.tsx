import { ChevronRightIcon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Link } from '@inertiajs/react'
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/admin/ui/collapsible'
import {
  SidebarGroup,
  SidebarGroupContent,
  SidebarGroupLabel,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarMenuSub,
  SidebarMenuSubButton,
  SidebarMenuSubItem,
} from '@/components/admin/ui/sidebar'
import { useCurrentUrl } from '@/hooks/use-current-url'
import type { NavGroup } from '@/types'

export function NavMain({ group = [] }: { group: NavGroup[] }) {
  const { isCurrentUrl } = useCurrentUrl()

  return group.map(({ title, items }) => (
    <SidebarGroup key={title}>
      <SidebarGroupLabel>{title}</SidebarGroupLabel>
      <SidebarGroupContent>
        <SidebarMenu>
          {items.map((item) => {
            if (item.subItems && item.subItems.length > 0) {
              return (
                <Collapsible
                  key={item.title}
                  asChild
                  defaultOpen
                  className="group/collapsible"
                >
                  <SidebarMenuItem>
                    <CollapsibleTrigger asChild>
                      <SidebarMenuButton tooltip={item.title}>
                        {item.icon && (
                          <HugeiconsIcon
                            icon={item.icon}
                            strokeWidth={1.5}
                          />
                        )}
                        <span>{item.title}</span>
                        <HugeiconsIcon
                          icon={ChevronRightIcon}
                          className="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                          strokeWidth={1.5}
                        />
                      </SidebarMenuButton>
                    </CollapsibleTrigger>
                    <CollapsibleContent>
                      <SidebarMenuSub>
                        {item.subItems?.map((subItem) => (
                          <SidebarMenuSubItem key={subItem.title}>
                            <SidebarMenuSubButton
                              isActive={isCurrentUrl(subItem.href)}
                              asChild
                            >
                              <Link href={subItem.href}>
                                <span>{subItem.title}</span>
                              </Link>
                            </SidebarMenuSubButton>
                          </SidebarMenuSubItem>
                        ))}
                      </SidebarMenuSub>
                    </CollapsibleContent>
                  </SidebarMenuItem>
                </Collapsible>
              )
            }

            return (
              <SidebarMenuItem key={item.title}>
                <SidebarMenuButton
                  asChild
                  isActive={isCurrentUrl(item.href)}
                  tooltip={{ children: item.title }}
                >
                  <Link
                    href={item.href}
                    prefetch
                  >
                    <HugeiconsIcon
                      icon={item.icon}
                      strokeWidth={1.5}
                    />
                    <span>{item.title}</span>
                  </Link>
                </SidebarMenuButton>
              </SidebarMenuItem>
            )
          })}
        </SidebarMenu>
      </SidebarGroupContent>
    </SidebarGroup>
  ))
}

import { ComputerIcon, Moon02Icon, Sun01Icon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import type { IconSvgElement } from '@hugeicons/react'
import { useMemo } from 'react'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/admin/ui/dropdown-menu'
import { SidebarMenuButton } from '@/components/admin/ui/sidebar'
import { useAppearance } from '@/hooks/use-appearance'
import type { Appearance } from '@/hooks/use-appearance'

function AppearanceDropdown() {
  const { appearance, updateAppearance } = useAppearance()

  const tabs = useMemo(() => {
    return [
      { value: 'light', icon: Sun01Icon, label: 'Claro' },
      { value: 'dark', icon: Moon02Icon, label: 'Oscuro' },
      { value: 'system', icon: ComputerIcon, label: 'Sistema' },
    ] as { value: Appearance; icon: IconSvgElement; label: string }[]
  }, [])

  const currenteAppearance = useMemo(() => {
    const found = tabs.find((tab) => tab.value === appearance)

    return found ?? tabs[0]
  }, [appearance, tabs])

  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <SidebarMenuButton
          className="text-neutral-600 hover:text-neutral-800 dark:text-neutral-300 dark:hover:text-neutral-100"
          aria-label="Cambiar apariencia"
          title="Cambiar apariencia"
        >
          <HugeiconsIcon
            icon={currenteAppearance.icon}
            strokeWidth={1.5}
          />
          <span>{currenteAppearance.label}</span>
        </SidebarMenuButton>
      </DropdownMenuTrigger>
      <DropdownMenuContent
        side="top"
        align="end"
      >
        {tabs.map(({ value, icon, label }) => (
          <DropdownMenuItem
            key={value}
            onClick={() => updateAppearance(value)}
            aria-current={appearance === value}
          >
            <span className="flex items-center gap-2">
              <HugeiconsIcon
                icon={icon}
                strokeWidth={1.5}
              />
              {label}
            </span>
          </DropdownMenuItem>
        ))}
      </DropdownMenuContent>
    </DropdownMenu>
  )
}

export default AppearanceDropdown

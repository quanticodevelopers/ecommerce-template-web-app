import { ComputerIcon, Moon02Icon, Sun01Icon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import type { IconSvgElement } from '@hugeicons/react'
import { useMemo } from 'react'
import { Button } from '@/components/ui/button'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
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
        <Button
          variant="ghost"
          aria-label="Cambiar apariencia"
          title="Cambiar apariencia"
          className="font-normal group-data-[collapsible=icon]:size-8 group-data-[collapsible=icon]:px-0"
        >
          <HugeiconsIcon icon={currenteAppearance.icon} />
          <span className="group-data-[collapsible=icon]:hidden">{currenteAppearance.label}</span>
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end">
        {tabs.map(({ value, icon, label }) => (
          <DropdownMenuItem
            key={value}
            onClick={() => updateAppearance(value)}
            aria-current={appearance === value}
          >
            <span className="flex items-center gap-2">
              <HugeiconsIcon
                icon={icon}
                className="h-5 w-5"
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

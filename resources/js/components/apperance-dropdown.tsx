import { ComputerIcon, Moon02Icon, Sun01Icon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import type { IconSvgElement } from '@hugeicons/react'
import { useMemo } from 'react'
import type { HTMLAttributes } from 'react'
import { Button } from '@/components/ui/button'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import { useAppearance } from '@/hooks/use-appearance'
import type { Appearance } from '@/hooks/use-appearance'

const AppearanceDropdown = ({ className = '', ...props }: HTMLAttributes<HTMLDivElement>) => {
  const { appearance, updateAppearance } = useAppearance()

  const tabs = useMemo(() => {
    return [
      { value: 'light', icon: Sun01Icon, label: 'Claro' },
      { value: 'dark', icon: Moon02Icon, label: 'Oscuro' },
      { value: 'system', icon: ComputerIcon, label: 'Sistema' },
    ] as { value: Appearance; icon: IconSvgElement; label: string }[]
  }, [])

  const currentIcon = useMemo(() => {
    const found = tabs.find((tab) => tab.value === appearance)?.icon

    return found ?? Sun01Icon
  }, [appearance, tabs])

  return (
    <div
      className={className}
      {...props}
    >
      <DropdownMenu>
        <DropdownMenuTrigger asChild>
          <Button
            variant="ghost"
            size="icon"
            className="h-9 w-9 rounded-md"
            aria-label="Cambiar apariencia"
            title="Cambiar apariencia"
          >
            <HugeiconsIcon
              icon={currentIcon}
              className="h-5 w-5"
            />
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
    </div>
  )
}

export default AppearanceDropdown

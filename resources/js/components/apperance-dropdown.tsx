import { MonitorIcon, MoonIcon, SunIcon } from 'lucide-react'
import type { LucideIcon } from 'lucide-react'
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
      { value: 'light', icon: SunIcon, label: 'Claro' },
      { value: 'dark', icon: MoonIcon, label: 'Oscuro' },
      { value: 'system', icon: MonitorIcon, label: 'Sistema' },
    ] as { value: Appearance; icon: LucideIcon; label: string }[]
  }, [])

  const CurrentIcon = useMemo(() => {
    const found = tabs.find((tab) => tab.value === appearance)?.icon as LucideIcon | undefined

    return found ?? SunIcon
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
            <CurrentIcon className="h-5 w-5" />
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
          {tabs.map(({ value, icon: Icon, label }) => (
            <DropdownMenuItem
              key={value}
              onClick={() => updateAppearance(value)}
              aria-current={appearance === value}
            >
              <span className="flex items-center gap-2">
                <Icon className="h-5 w-5" />
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

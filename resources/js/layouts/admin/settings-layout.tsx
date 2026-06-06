import { Link } from '@inertiajs/react'
import type { PropsWithChildren } from 'react'
import Heading from '@/components/heading'
import { Button } from '@/components/ui/button'
import { Separator } from '@/components/ui/separator'
import { useCurrentUrl } from '@/hooks/use-current-url'
import { cn, toUrl } from '@/lib/utils'
import { edit as editAppearance } from '@/routes/admin/appearance'
import { edit as editProfile } from '@/routes/admin/profile'
import { edit as editSecurity } from '@/routes/admin/security'
import type { NavItem } from '@/types'

const sidebarNavItems: NavItem[] = [
  {
    title: 'Perfil',
    href: editProfile(),
    icon: null,
  },
  {
    title: 'Seguridad',
    href: editSecurity(),
    icon: null,
  },
  {
    title: 'Apariencia',
    href: editAppearance(),
    icon: null,
  },
]

const SettingsLayout = ({ children }: PropsWithChildren) => {
  const { isCurrentOrParentUrl } = useCurrentUrl()

  return (
    <div className="grid gap-6 p-8">
      <Heading
        title="Ajustes"
        description="Administra tu perfil y la configuracion de tu cuenta"
      />

      <div className="flex flex-col lg:flex-row lg:space-x-12">
        <aside className="w-full max-w-xl lg:w-48">
          <nav
            className="flex flex-col space-y-1 space-x-0"
            aria-label="Ajustes"
          >
            {sidebarNavItems.map((item, index) => (
              <Button
                key={`${toUrl(item.href)}-${index}`}
                size="sm"
                variant="ghost"
                asChild
                className={cn('w-full justify-start', {
                  'bg-muted': isCurrentOrParentUrl(item.href),
                })}
              >
                <Link href={item.href}>
                  {item.icon && <item.icon className="h-4 w-4" />}
                  {item.title}
                </Link>
              </Button>
            ))}
          </nav>
        </aside>

        <Separator className="my-6 lg:hidden" />

        <div className="flex-1 md:max-w-2xl">
          <section className="max-w-xl space-y-12">{children}</section>
        </div>
      </div>
    </div>
  )
}

export default SettingsLayout

import { Link } from '@inertiajs/react'
import { BookIcon, LockKeyholeIcon, SettingsIcon, SunIcon, UserPenIcon } from 'lucide-react'
import type { PropsWithChildren } from 'react'
import Heading from '@/components/heading'
import { Button } from '@/components/ui/button'
import { useCurrentUrl } from '@/hooks/use-current-url'
import { cn, toUrl } from '@/lib/utils'
import { edit as editAppearance } from '@/routes/admin/appearance'
import { edit as editInformation } from '@/routes/admin/information'
import { edit as editProfile } from '@/routes/admin/profile'
import { edit as editSecurity } from '@/routes/admin/security'
import type { NavItem } from '@/types'

const sidebarNavItems: NavItem[] = [
  {
    title: 'Información del sitio',
    href: editInformation(),
    icon: BookIcon,
  },
  {
    title: 'Perfil',
    href: editProfile(),
    icon: UserPenIcon,
  },
  {
    title: 'Seguridad',
    href: editSecurity(),
    icon: LockKeyholeIcon,
  },
  {
    title: 'Apariencia',
    href: editAppearance(),
    icon: SunIcon,
  },
]

const SettingsLayout = ({ children }: PropsWithChildren) => {
  const { isCurrentOrParentUrl } = useCurrentUrl()

  return (
    <div className="grid gap-6 p-8">
      <Heading
        title="Configuración"
        description="Administra tu perfil y la configuracion de tu cuenta"
        badgeIcon={SettingsIcon}
        badgeLabel="Administración"
      />

      <div className="flex flex-col items-start space-y-4 lg:flex-row lg:space-y-0 lg:space-x-4">
        <aside className="w-full max-w-xl rounded-xl border p-2 lg:w-80">
          <nav
            className="flex flex-col space-y-1 space-x-0"
            aria-label="Configuración"
          >
            {sidebarNavItems.map((item, index) => (
              <Button
                key={`${toUrl(item.href)}-${index}`}
                size="lg"
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

        <section className="flex-1 space-y-12 rounded-xl border p-6 md:max-w-2xl">{children}</section>
      </div>
    </div>
  )
}

export default SettingsLayout

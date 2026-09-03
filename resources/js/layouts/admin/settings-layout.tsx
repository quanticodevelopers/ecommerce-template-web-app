import { Book02Icon, LockKeyIcon, Settings01Icon, Sun01Icon, UserEdit01Icon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Link } from '@inertiajs/react'
import type { PropsWithChildren } from 'react'
import Heading from '@/components/admin/heading'
import { Button } from '@/components/admin/ui/button'
import { useCurrentUrl } from '@/hooks/use-current-url'
import { cn, toUrl } from '@/lib/utils'
import { edit as editAppearance } from '@/routes/admin/appearance'
import { edit as editInformation } from '@/routes/admin/information'
import { edit as editProfile } from '@/routes/admin/profile'
import { edit as editSecurity } from '@/routes/admin/security'
import type { NavItem } from '@/types/admin'

const sidebarNavItems: NavItem[] = [
  {
    title: 'Información del sitio',
    href: editInformation(),
    icon: Book02Icon,
  },
  {
    title: 'Perfil',
    href: editProfile(),
    icon: UserEdit01Icon,
  },
  {
    title: 'Seguridad',
    href: editSecurity(),
    icon: LockKeyIcon,
  },
  {
    title: 'Apariencia',
    href: editAppearance(),
    icon: Sun01Icon,
  },
]

const SettingsLayout = ({ children }: PropsWithChildren) => {
  const { isCurrentOrParentUrl } = useCurrentUrl()

  return (
    <div className="grid gap-6 p-8">
      <Heading
        title="Configuración"
        description="Administra tu perfil y la configuracion de tu cuenta"
        badgeIcon={Settings01Icon}
        badgeLabel="Administración"
      />

      <div className="flex flex-col items-start space-y-4 lg:flex-row lg:space-x-4 lg:space-y-0">
        <aside className="w-full max-w-xl rounded-xl border p-2 lg:w-80">
          <nav
            className="flex flex-col space-x-0 space-y-1"
            aria-label="Configuración"
          >
            {sidebarNavItems.map((item, index) => (
              <Button
                key={`${toUrl(item.href)}-${index}`}
                type="button"
                size="default"
                variant="ghost"
                asChild
                className={cn('w-full justify-start', {
                  'bg-muted': isCurrentOrParentUrl(item.href),
                })}
              >
                <Link href={item.href}>
                  <HugeiconsIcon
                    icon={item.icon}
                    strokeWidth={1.5}
                  />
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

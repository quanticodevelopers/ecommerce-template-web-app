import { FilterHorizontalIcon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Head } from '@inertiajs/react'
import { Button } from '@/components/admin/ui/button'
import { PlaceholderPattern } from '@/components/admin/ui/placeholder-pattern'
import { useAuthenticatedAdministrator } from '@/hooks/admin/use-authenticated-administrator'
import { formatPeruDashboardDate, getPeruDashboardGreeting } from '@/lib/admin/date'
import { dashboard } from '@/routes/admin'

export default function DashboardIndex() {
  const user = useAuthenticatedAdministrator()
  const currentDate = new Date()
  const greeting = getPeruDashboardGreeting(currentDate)
  const formattedDate = formatPeruDashboardDate(currentDate)
  const userName = `${user.name}`.trim()

  return (
    <>
      <Head title="Dashboard" />
      <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4 md:p-8">
        <div className="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
          <div className="space-y-3">
            <time
              className="text-muted-foreground inline-block w-fit text-sm font-medium"
              dateTime={currentDate.toISOString()}
            >
              {formattedDate}
            </time>

            <div className="space-y-2">
              <h1 className="flex items-center gap-2 text-3xl font-semibold tracking-tight">
                <span>
                  {greeting}, <span className="text-primary">{userName}</span>
                </span>{' '}
              </h1>
              <p className="text-muted-foreground max-w-2xl text-sm">Resumen de la información de tu tienda.</p>
            </div>
          </div>

          <div className="flex w-full flex-wrap items-center justify-start gap-3 md:w-auto md:justify-end">
            <div
              className="h-10 w-full"
              aria-hidden="true"
            >
              <Button variant="outline">
                <HugeiconsIcon
                  icon={FilterHorizontalIcon}
                  strokeWidth={1.5}
                />{' '}
                Este mes
              </Button>
            </div>
          </div>
        </div>
        <div className="xs:grid-cols-2 grid auto-rows-min gap-4 md:grid-cols-4">
          <div className="border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border">
            <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
          </div>
          <div className="border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border">
            <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
          </div>
          <div className="border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border">
            <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
          </div>
          <div className="border-sidebar-border/70 dark:border-sidebar-border relative aspect-video overflow-hidden rounded-xl border">
            <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
          </div>
        </div>
        <div className="border-sidebar-border/70 dark:border-sidebar-border relative min-h-[100vh] flex-1 overflow-hidden rounded-xl border md:min-h-min">
          <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
        </div>
      </div>
    </>
  )
}

DashboardIndex.layout = {
  breadcrumbs: [
    {
      title: 'Dashboard',
      href: dashboard(),
    },
  ],
}

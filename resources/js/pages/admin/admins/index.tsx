import { UserMultiple02Icon } from '@hugeicons/core-free-icons'
import { Head } from '@inertiajs/react'
import Heading from '@/components/heading'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Card, CardContent } from '@/components/ui/card'
import { useInitials } from '@/hooks/use-initials'
import { dateFormatter } from '@/lib/utils'
import AdminRowActions from '@/pages/admin/admins/components/admin-row-actions'
import CreateAdminModal from '@/pages/admin/admins/components/create-admin-modal'
import CreatedAdministratorCredentialsModal from '@/pages/admin/admins/components/created-administrator-credentials-modal'
import { index as adminsIndex } from '@/routes/admin/admins'
import type { AdministratorListItem, CreatedAdministratorCredentials, SelectOption } from '@/types'

type AdminsIndexProps = {
  admins: AdministratorListItem[]
  document_type_options: SelectOption[]
  created_administrator_credentials: CreatedAdministratorCredentials | null
}

function formatDate(value: string | null): string {
  if (!value) {
    return '—'
  }

  return dateFormatter.format(new Date(value))
}

function formatFullName(administrator: AdministratorListItem): string {
  return `${administrator.name} ${administrator.last_name}`.trim()
}

function formatDocument(administrator: AdministratorListItem): string {
  return `${administrator.document_type.label} ${administrator.document_number}`
}

function formatPhone(phone: string): string {
  return phone
}

export default function AdminsIndex({ admins, document_type_options, created_administrator_credentials }: AdminsIndexProps) {
  const createdCredentialsModalKey =
    created_administrator_credentials === null
      ? 'created-administrator-credentials-empty'
      : `${created_administrator_credentials.email}-${created_administrator_credentials.password}`
  const getInitials = useInitials()

  return (
    <>
      <Head title="Administradores" />

      <div className="flex flex-col gap-8 p-4 lg:p-8">
        <div className="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
          <Heading
            title="Administradores"
            description="Gestiona las identidades con acceso al panel administrativo y revisa su información principal en un solo lugar."
            badgeIcon={UserMultiple02Icon}
            badgeLabel="Administración"
          />

          <div className="flex w-full flex-wrap items-center justify-start gap-3 md:w-auto md:justify-end">
            <CreateAdminModal
              documentTypeOptions={document_type_options}
              triggerClassName="w-full md:w-auto"
            />
          </div>
        </div>

        <Card className="gap-0 border-sidebar-border/70 p-0 shadow-none dark:border-sidebar-border">
          <CardContent className="px-0">
            <div className="overflow-x-auto">
              <table className="min-w-full table-fixed divide-y divide-border text-sm">
                <colgroup>
                  <col />
                  <col />
                  <col />
                  <col className="w-35" />
                  <col className="w-40" />
                  <col className="w-35" />
                  <col className="w-15" />
                </colgroup>
                <thead className="bg-muted/40 text-muted-foreground">
                  <tr>
                    <th className="px-6 py-3.5 text-left font-medium">Administrador</th>
                    <th className="px-6 py-3.5 text-left font-medium">Correo electrónico</th>
                    <th className="px-6 py-3.5 text-center font-medium">Doc. de Id.</th>
                    <th className="px-6 py-3.5 text-center font-medium">Celular</th>
                    <th className="px-6 py-3.5 text-center font-medium">Rol</th>
                    <th className="px-6 py-3.5 text-center font-medium">Registro</th>
                    <th className="px-6 py-3.5 text-right font-medium">Acciones</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-border bg-background">
                  {admins.length === 0 ? (
                    <tr>
                      <td
                        className="px-6 py-10 text-center text-muted-foreground"
                        colSpan={7}
                      >
                        No hay usuarios administradores registrados.
                      </td>
                    </tr>
                  ) : (
                    admins.map((administrator) => (
                      <tr
                        key={administrator.id}
                        className="align-middle transition-colors hover:bg-muted/30"
                      >
                        <td className="px-6 py-4">
                          <div className="flex items-center gap-3">
                            <Avatar>
                              <AvatarFallback>{getInitials(administrator.name, administrator.last_name)}</AvatarFallback>
                            </Avatar>
                            <p className="font-medium text-nowrap text-foreground">{formatFullName(administrator)}</p>
                          </div>
                        </td>
                        <td className="px-6 py-4 text-foreground">{administrator.email}</td>
                        <td className="px-6 py-4 text-center text-muted-foreground">{formatDocument(administrator)}</td>
                        <td className="px-6 py-4 text-center text-muted-foreground">{formatPhone(administrator.phone)}</td>
                        <td className="px-6 py-4 text-center">
                          <Badge variant="outline">{administrator.role.label}</Badge>
                        </td>
                        <td className="px-6 py-4 text-center text-muted-foreground">{formatDate(administrator.created_at)}</td>
                        <td className="px-6 py-4 text-right">
                          <AdminRowActions administrator={administrator} />
                        </td>
                      </tr>
                    ))
                  )}
                </tbody>
              </table>
            </div>
          </CardContent>
        </Card>
      </div>
      <CreatedAdministratorCredentialsModal
        key={createdCredentialsModalKey}
        createdAdministratorCredentials={created_administrator_credentials}
      />
    </>
  )
}

AdminsIndex.layout = {
  breadcrumbs: [
    {
      title: 'Administradores',
      href: adminsIndex(),
    },
  ],
}

import { Head } from '@inertiajs/react'
import { UsersIcon } from 'lucide-react'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { useInitials } from '@/hooks/use-initials'
import { dateFormatter } from '@/lib/utils'
import AdminUserRowActions from '@/pages/admin/users/components/admin-user-row-actions'
import CreateAdminUserModal from '@/pages/admin/users/components/create-admin-user-modal'
import CreatedUserCredentialsModal from '@/pages/admin/users/components/created-user-credentials-modal'
import { index as usersIndex } from '@/routes/admin/users'
import type { CreatedUserCredentials, SelectOption, UserListItem } from '@/types'

type UsersIndexProps = {
  users: UserListItem[]
  current_user_id: string | null
  document_type_options: SelectOption[]
  created_user_credentials: CreatedUserCredentials | null
}

function formatDate(value: string | null): string {
  if (!value) {
    return '—'
  }

  return dateFormatter.format(new Date(value))
}

function formatFullName(user: UserListItem): string {
  return `${user.name} ${user.last_name}`.trim()
}

function formatDocument(user: UserListItem): string {
  return `${user.document_type.label} ${user.document_number}`
}

function formatPhone(phone: string): string {
  return phone
}

export default function UsersIndex({ users, current_user_id, document_type_options, created_user_credentials }: UsersIndexProps) {
  const createdCredentialsModalKey = created_user_credentials === null ? 'created-user-credentials-empty' : `${created_user_credentials.email}-${created_user_credentials.password}`
  const getInitials = useInitials()

  return (
    <>
      <Head title="Usuarios" />

      <div className="flex flex-col gap-8 p-8">
        <div className="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
          <div className="space-y-3">
            <Badge
              variant="secondary"
              className="w-fit gap-1.5"
            >
              <UsersIcon className="size-3.5" />
              Administración
            </Badge>

            <div className="space-y-2">
              <h1 className="text-3xl font-semibold tracking-tight">Usuarios</h1>
              <p className="max-w-2xl text-sm text-muted-foreground">
                Gestiona los usuarios con acceso al panel administrativo y revisa su información principal en un solo lugar.
              </p>
            </div>
          </div>

          <div className="flex w-full flex-wrap items-center justify-start gap-3 md:w-auto md:justify-end">
            <CreateAdminUserModal
              documentTypeOptions={document_type_options}
              triggerClassName="w-full md:w-auto"
            />
          </div>
        </div>

        <Card className="gap-0 border-sidebar-border/70 pt-4 shadow-none dark:border-sidebar-border">
          <CardHeader className="border-b border-border/60 pb-4">
            <div className="flex items-center justify-between gap-2">
              <CardTitle>Listado de usuarios</CardTitle>
              <div className="rounded-lg border border-border/60 bg-muted/40 px-3 py-1.5 text-xs text-muted-foreground sm:text-sm">
                {users.length} usuario{users.length === 1 ? '' : 's'}
              </div>
            </div>
          </CardHeader>

          <CardContent className="p-0">
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-border text-sm">
                <thead className="bg-muted/40 text-muted-foreground">
                  <tr>
                    <th className="px-6 py-3.5 text-left font-medium">Usuario</th>
                    <th className="px-6 py-3.5 text-left font-medium">Correo electrónico</th>
                    <th className="px-6 py-3.5 text-center font-medium">Doc. de Id.</th>
                    <th className="w-35 px-6 py-3.5 text-center font-medium">Celular</th>
                    <th className="w-40 px-6 py-3.5 text-center font-medium">Rol</th>
                    <th className="w-25 px-6 py-3.5 text-center font-medium">Estado</th>
                    <th className="w-35 px-6 py-3.5 text-center font-medium">Registro</th>
                    <th className="w-15 px-6 py-3.5 text-right font-medium">Acciones</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-border bg-background">
                  {users.length === 0 ? (
                    <tr>
                      <td
                        className="px-6 py-10 text-center text-muted-foreground"
                        colSpan={8}
                      >
                        No hay usuarios administradores registrados.
                      </td>
                    </tr>
                  ) : (
                    users.map((user) => (
                      <tr
                        key={user.id}
                        className="align-middle transition-colors hover:bg-muted/30"
                      >
                        <td className="px-6 py-4">
                          <div className="flex items-center gap-3">
                            <Avatar>
                              <AvatarFallback>{getInitials(user.name, user.last_name)}</AvatarFallback>
                            </Avatar>
                            <p className="font-medium text-foreground">{formatFullName(user)}</p>
                          </div>
                        </td>
                        <td className="px-6 py-4 text-foreground">{user.email}</td>
                        <td className="px-6 py-4 text-center text-muted-foreground">{formatDocument(user)}</td>
                        <td className="px-6 py-4 text-center text-muted-foreground">{formatPhone(user.phone)}</td>
                        <td className="px-6 py-4 text-center">
                          <Badge variant="outline">{user.role.label}</Badge>
                        </td>
                        <td className="px-6 py-4 text-center">
                          <Badge variant={user.is_active ? 'default' : 'secondary'}>{user.is_active ? 'Activo' : 'Inactivo'}</Badge>
                        </td>
                        <td className="px-6 py-4 text-center text-muted-foreground">{formatDate(user.created_at)}</td>
                        <td className="px-6 py-4 text-right">
                          <AdminUserRowActions
                            user={user}
                            currentUserId={current_user_id}
                          />
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
      <CreatedUserCredentialsModal
        key={createdCredentialsModalKey}
        createdUserCredentials={created_user_credentials}
      />
    </>
  )
}

UsersIndex.layout = {
  breadcrumbs: [
    {
      title: 'Usuarios',
      href: usersIndex(),
    },
  ],
}

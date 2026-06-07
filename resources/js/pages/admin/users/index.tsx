import { Head } from '@inertiajs/react'
import { UsersIcon } from 'lucide-react'
import { Badge } from '@/components/ui/badge'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
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

  return (
    <>
      <Head title="Usuarios administradores" />

      <div className="flex flex-col gap-6 p-4">
        <div className="flex flex-col gap-4 rounded-xl border border-sidebar-border/70 bg-card p-6 shadow-sm dark:border-sidebar-border">
          <div className="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div className="space-y-2">
              <Badge
                variant="secondary"
                className="w-fit gap-1.5"
              >
                <UsersIcon className="size-3.5" />
                Administración
              </Badge>

              <div className="space-y-1">
                <h1 className="text-3xl font-semibold tracking-tight">Usuarios administradores</h1>
                <p className="max-w-2xl text-sm text-muted-foreground">
                  Gestiona los usuarios con acceso al panel administrativo y revisa su información principal en un solo lugar.
                </p>
              </div>
            </div>

            <div className="flex flex-col items-stretch gap-3 sm:items-end">
              <div className="rounded-xl border border-border/60 bg-muted/40 px-4 py-3 text-sm text-muted-foreground">
                {users.length} usuario{users.length === 1 ? '' : 's'} administrador{users.length === 1 ? '' : 'es'}
              </div>

              <CreateAdminUserModal documentTypeOptions={document_type_options} />
            </div>
          </div>
        </div>

        <Card className="gap-0 border-sidebar-border/70 dark:border-sidebar-border">
          <CardHeader className="border-b border-border/60 pb-6">
            <CardTitle>Listado de usuarios</CardTitle>
            <CardDescription>Se muestran únicamente los usuarios con rol administrador.</CardDescription>
          </CardHeader>

          <CardContent className="p-0">
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-border text-sm">
                <thead className="bg-muted/40 text-muted-foreground">
                  <tr>
                    <th className="px-6 py-3.5 text-left font-medium">Usuario</th>
                    <th className="px-6 py-3.5 text-left font-medium">Documento</th>
                    <th className="px-6 py-3.5 text-left font-medium">Contacto</th>
                    <th className="px-6 py-3.5 text-left font-medium">Rol</th>
                    <th className="px-6 py-3.5 text-left font-medium">Estado</th>
                    <th className="px-6 py-3.5 text-left font-medium">Registro</th>
                    <th className="px-6 py-3.5 text-right font-medium">Acciones</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-border bg-background">
                  {users.length === 0 ? (
                    <tr>
                      <td
                        className="px-6 py-10 text-center text-muted-foreground"
                        colSpan={7}
                      >
                        No hay usuarios administradores registrados.
                      </td>
                    </tr>
                  ) : (
                    users.map((user) => (
                      <tr
                        key={user.id}
                        className="align-top transition-colors hover:bg-muted/30"
                      >
                        <td className="px-6 py-4">
                          <p className="font-medium text-foreground">{formatFullName(user)}</p>
                        </td>
                        <td className="px-6 py-4 text-muted-foreground">{formatDocument(user)}</td>
                        <td className="px-6 py-4">
                          <div className="space-y-1">
                            <p className="text-foreground">{user.email}</p>
                            <p className="text-muted-foreground">{formatPhone(user.phone)}</p>
                          </div>
                        </td>
                        <td className="px-6 py-4">
                          <Badge variant="outline">{user.role.label}</Badge>
                        </td>
                        <td className="px-6 py-4">
                          <Badge variant={user.is_active ? 'default' : 'secondary'}>{user.is_active ? 'Activo' : 'Inactivo'}</Badge>
                        </td>
                        <td className="px-6 py-4 text-muted-foreground">{formatDate(user.created_at)}</td>
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

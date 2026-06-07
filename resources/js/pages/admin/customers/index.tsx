import { Head } from '@inertiajs/react'
import { ShoppingBagIcon } from 'lucide-react'
import { Badge } from '@/components/ui/badge'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { dateFormatter } from '@/lib/utils'
import CustomerRowActions from '@/pages/admin/customers/components/customer-row-actions'
import { index as customersIndex } from '@/routes/admin/customers'
import type { UserListItem } from '@/types'

type CustomersIndexProps = {
  customers: UserListItem[]
}

function formatDate(value: string | null): string {
  if (!value) {
    return '—'
  }

  return dateFormatter.format(new Date(value))
}

function formatFullName(customer: UserListItem): string {
  return `${customer.name} ${customer.last_name}`.trim()
}

function formatDocument(customer: UserListItem): string {
  return `${customer.document_type.label} ${customer.document_number}`
}

export default function CustomersIndex({ customers }: CustomersIndexProps) {
  return (
    <>
      <Head title="Clientes" />

      <div className="flex flex-col gap-6 p-4">
        <div className="flex flex-col gap-4 rounded-xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border">
          <div className="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div className="space-y-2">
              <Badge
                variant="secondary"
                className="w-fit gap-1.5"
              >
                <ShoppingBagIcon className="size-3.5" />
                Tienda
              </Badge>

              <div className="space-y-1">
                <h1 className="text-3xl font-semibold tracking-tight">Clientes</h1>
                <p className="max-w-2xl text-sm text-muted-foreground">Gestiona los usuarios clientes registrados en la tienda y revisa su información en un solo lugar.</p>
              </div>
            </div>

            <div className="flex flex-col items-stretch gap-3 sm:items-end">
              <div className="rounded-xl border border-border/60 bg-muted/40 px-4 py-3 text-sm text-muted-foreground">
                {customers.length} cliente{customers.length === 1 ? '' : 's'} registrado{customers.length === 1 ? '' : 's'}
              </div>
            </div>
          </div>
        </div>

        <Card className="gap-0 border-sidebar-border/70 shadow-none dark:border-sidebar-border">
          <CardHeader className="border-b border-border/60 pb-6">
            <CardTitle>Listado de clientes</CardTitle>
            <CardDescription>Se muestran únicamente los usuarios con rol cliente.</CardDescription>
          </CardHeader>

          <CardContent className="p-0">
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-border text-sm">
                <thead className="bg-muted/40 text-muted-foreground">
                  <tr>
                    <th className="px-6 py-3.5 text-left font-medium">Usuario</th>
                    <th className="w-25 px-6 py-3.5 text-left font-medium">Estado</th>
                    <th className="w-35 px-6 py-3.5 text-left font-medium">F. de registro</th>
                    <th className="w-20 px-6 py-3.5 text-right font-medium">Acciones</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-border bg-background">
                  {customers.length === 0 ? (
                    <tr>
                      <td
                        className="px-6 py-10 text-center text-muted-foreground"
                        colSpan={4}
                      >
                        No hay clientes registrados.
                      </td>
                    </tr>
                  ) : (
                    customers.map((customer) => (
                      <tr
                        key={customer.id}
                        className="align-middle transition-colors hover:bg-muted/30"
                      >
                        <td className="px-6 py-4">
                          <div className="flex items-center gap-3">
                            <div className="flex size-9 shrink-0 items-center justify-center rounded-full bg-muted text-sm font-semibold text-muted-foreground">
                              {customer.name.charAt(0).toUpperCase()}
                              {customer.last_name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                              <p className="font-medium text-foreground">{formatFullName(customer)}</p>
                              <p className="text-xs text-muted-foreground">{formatDocument(customer)}</p>
                            </div>
                          </div>
                        </td>
                        <td className="px-6 py-4">
                          <Badge variant={customer.is_active ? 'default' : 'secondary'}>{customer.is_active ? 'Activo' : 'Inactivo'}</Badge>
                        </td>
                        <td className="px-6 py-4 text-muted-foreground">{formatDate(customer.created_at)}</td>
                        <td className="px-6 py-4 text-right">
                          <CustomerRowActions customer={customer} />
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
    </>
  )
}

CustomersIndex.layout = {
  breadcrumbs: [
    {
      title: 'Clientes',
      href: customersIndex(),
    },
  ],
}

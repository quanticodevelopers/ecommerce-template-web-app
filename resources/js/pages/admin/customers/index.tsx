import { ShoppingBag01Icon } from '@hugeicons/core-free-icons'
import { Head } from '@inertiajs/react'
import Heading from '@/components/heading'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { useInitials } from '@/hooks/use-initials'
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
  const getInitials = useInitials()

  return (
    <>
      <Head title="Clientes" />

      <div className="p4 flex flex-col gap-8 lg:p-8">
        <div className="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
          <Heading
            title="Clientes"
            description="Gestiona los usuarios clientes registrados en la tienda y revisa su información en un solo lugar."
            badgeIcon={ShoppingBag01Icon}
            badgeLabel="Tienda"
          />

          <div className="flex w-full flex-wrap items-center justify-start gap-3 md:w-auto md:justify-end">
            <div
              className="h-10 w-full md:w-36"
              aria-hidden="true"
            />
          </div>
        </div>

        <Card className="gap-0 border-sidebar-border/70 pt-4 shadow-none dark:border-sidebar-border">
          <CardHeader className="border-b border-border/60 pb-4">
            <div className="flex items-center justify-between gap-2">
              <CardTitle>Listado de clientes</CardTitle>
              <div className="rounded-lg border border-border/60 bg-muted/40 px-3 py-1.5 text-xs text-muted-foreground sm:text-sm">
                {customers.length} cliente{customers.length === 1 ? '' : 's'}
              </div>
            </div>
          </CardHeader>

          <CardContent className="p-0">
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-border text-sm">
                <thead className="bg-muted/40 text-muted-foreground">
                  <tr>
                    <th className="px-6 py-3.5 text-left font-medium">Usuario</th>
                    <th className="px-6 py-3.5 text-left font-medium">Doc. de Id.</th>
                    <th className="w-35 px-6 py-3.5 text-center font-medium">F. de registro</th>
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
                            <Avatar>
                              <AvatarFallback>{getInitials(customer.name, customer.last_name)}</AvatarFallback>
                            </Avatar>
                            <div>
                              <p className="font-medium text-foreground">{formatFullName(customer)}</p>
                              <p className="text-xs text-muted-foreground">{customer.email}</p>
                            </div>
                          </div>
                        </td>
                        <td className="px-6 py-4 text-muted-foreground">{formatDocument(customer)}</td>
                        <td className="px-6 py-4 text-center text-muted-foreground">{formatDate(customer.created_at)}</td>
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

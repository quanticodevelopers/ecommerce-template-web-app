import { Head } from '@inertiajs/react'
import { TagIcon } from 'lucide-react'
import Heading from '@/components/heading'
import { Badge } from '@/components/ui/badge'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import BrandRowActions from '@/pages/admin/brands/components/brand-row-actions'
import { index as brandsIndex } from '@/routes/admin/brands'
import type { BrandListItem } from '@/types'

type BrandsIndexProps = {
  brands: BrandListItem[]
}

function formatStatus(isActive: boolean): { label: string; variant: 'default' | 'secondary' } {
  return isActive ? { label: 'Activa', variant: 'default' } : { label: 'Inactiva', variant: 'secondary' }
}

export default function BrandsIndex({ brands }: BrandsIndexProps) {
  return (
    <>
      <Head title="Marcas" />

      <div className="flex flex-col gap-8 p-4 lg:p-8">
        <div className="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
          <Heading
            title="Marcas"
            description="Gestiona las marcas del catálogo y revisa su estado, código y logo."
            badgeIcon={TagIcon}
            badgeLabel="Catálogo"
          />
        </div>

        <Card className="gap-0 border-sidebar-border/70 pt-4 shadow-none dark:border-sidebar-border">
          <CardHeader className="border-b border-border/60 pb-4">
            <div className="flex items-center justify-between gap-2">
              <CardTitle>Listado de marcas</CardTitle>
              <div className="rounded-lg border border-border/60 bg-muted/40 px-3 py-1.5 text-xs text-muted-foreground sm:text-sm">
                {brands.length} marca{brands.length === 1 ? '' : 's'}
              </div>
            </div>
          </CardHeader>

          <CardContent className="p-0">
            <div className="overflow-x-auto">
              <table className="min-w-full table-fixed divide-y divide-border text-sm">
                <colgroup>
                  <col className="w-29" />
                  <col className="w-[9ch]" />
                  <col />
                  <col />
                  <col className="w-32" />
                  <col className="w-24" />
                </colgroup>
                <thead className="bg-muted/40 text-muted-foreground">
                  <tr>
                    <th className="px-6 py-3.5 text-center font-medium">Logo</th>
                    <th className="px-6 py-3.5 text-left font-medium">Código</th>
                    <th className="px-6 py-3.5 text-left font-medium">Nombre</th>
                    <th className="px-6 py-3.5 text-left font-medium">Slug</th>
                    <th className="px-6 py-3.5 text-center font-medium">Estado</th>
                    <th className="px-6 py-3.5 text-right font-medium">Acciones</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-border bg-background">
                  {brands.length === 0 ? (
                    <tr>
                      <td
                        className="px-6 py-10 text-center text-muted-foreground"
                        colSpan={6}
                      >
                        No hay marcas registradas.
                      </td>
                    </tr>
                  ) : (
                    brands.map((brand) => {
                      const status = formatStatus(brand.is_active)

                      return (
                        <tr
                          key={brand.id}
                          className="align-middle transition-colors hover:bg-muted/30"
                        >
                          <td className="px-6 py-4">
                            {brand.logo_url === null ? (
                              <div className="flex h-11 w-20 items-center justify-center rounded-md border border-dashed border-border/70 bg-muted/30 text-[11px] text-muted-foreground">
                                Sin logo
                              </div>
                            ) : (
                              <img
                                alt={brand.name}
                                className="aspect-3/2 h-12 rounded-md border border-border/70 bg-background object-contain p-0.5"
                                src={brand.logo_url}
                              />
                            )}
                          </td>
                          <td className="px-6 py-4 font-mono whitespace-nowrap text-foreground">{brand.code}</td>
                          <td className="px-6 py-4 font-medium text-foreground">{brand.name}</td>
                          <td className="px-6 py-4 text-muted-foreground">{brand.slug}</td>
                          <td className="px-6 py-4 text-center">
                            <Badge variant={status.variant}>{status.label}</Badge>
                          </td>
                          <td className="px-6 py-4 text-right">
                            <BrandRowActions brand={brand} />
                          </td>
                        </tr>
                      )
                    })
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

BrandsIndex.layout = {
  breadcrumbs: [
    {
      title: 'Marcas',
      href: brandsIndex(),
    },
  ],
}

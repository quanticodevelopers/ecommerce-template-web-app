import { Head, Link } from '@inertiajs/react'
import { FolderTreeIcon } from 'lucide-react'
import Heading from '@/components/heading'
import { Badge } from '@/components/ui/badge'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import CategoryRowActions from '@/pages/admin/categories/components/category-row-actions'
import { index as categoriesIndex, subcategories } from '@/routes/admin/categories'
import type { CategoryListItem, CategoryParent } from '@/types'

type CategoriesIndexProps = {
  categories: CategoryListItem[]
  parent_category: CategoryParent | null
}

function formatStatus(isActive: boolean): { label: string; variant: 'default' | 'secondary' } {
  return isActive ? { label: 'Activa', variant: 'default' } : { label: 'Inactiva', variant: 'secondary' }
}

function getHeadingTitle(parentCategory: CategoryParent | null): string {
  return parentCategory === null ? 'Categorías' : `Subcategorías de ${parentCategory.name}`
}

function getHeadingDescription(parentCategory: CategoryParent | null): string {
  return parentCategory === null
    ? 'Gestiona las categorías del catálogo y accede a sus subcategorías desde el listado.'
    : `Gestiona las subcategorías asociadas a ${parentCategory.name}.`
}

export default function CategoriesIndex({ categories, parent_category }: CategoriesIndexProps) {
  const title = getHeadingTitle(parent_category)
  const description = getHeadingDescription(parent_category)

  return (
    <>
      <Head title={title} />

      <div className="flex flex-col gap-8 p-4 lg:p-8">
        <div className="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
          <Heading
            title={title}
            description={description}
            badgeIcon={FolderTreeIcon}
            badgeLabel="Catálogo"
          />
        </div>

        <Card className="gap-0 border-sidebar-border/70 pt-4 shadow-none dark:border-sidebar-border">
          <CardHeader className="border-b border-border/60 pb-4">
            <div className="flex items-center justify-between gap-2">
              <CardTitle>Listado de categorías</CardTitle>
              <div className="rounded-lg border border-border/60 bg-muted/40 px-3 py-1.5 text-xs text-muted-foreground sm:text-sm">
                {categories.length} categoría{categories.length === 1 ? '' : 's'}
              </div>
            </div>
          </CardHeader>

          <CardContent className="p-0">
            <div className="overflow-x-auto">
              <table className="min-w-full table-fixed divide-y divide-border text-sm">
                <colgroup>
                  <col className="w-[9ch]" />
                  <col />
                  <col />
                  <col className="w-34" />
                  <col className="w-44" />
                  <col className="w-30" />
                </colgroup>
                <thead className="bg-muted/40 text-muted-foreground">
                  <tr>
                    <th className="px-6 py-3.5 text-left font-medium">Código</th>
                    <th className="px-6 py-3.5 text-left font-medium">Nombre</th>
                    <th className="px-6 py-3.5 text-left font-medium">Slug</th>
                    <th className="px-6 py-3.5 text-center font-medium">Estado</th>
                    <th className="px-6 py-3.5 text-center font-medium">Subcategorías</th>
                    <th className="px-6 py-3.5 text-right font-medium">Acciones</th>
                  </tr>
                </thead>
                <tbody className="divide-y divide-border bg-background">
                  {categories.length === 0 ? (
                    <tr>
                      <td
                        className="px-6 py-10 text-center text-muted-foreground"
                        colSpan={6}
                      >
                        No hay categorías registradas.
                      </td>
                    </tr>
                  ) : (
                    categories.map((category) => {
                      const status = formatStatus(category.is_active)

                      return (
                        <tr
                          key={category.id}
                          className="align-middle transition-colors hover:bg-muted/30"
                        >
                          <td className="px-6 py-4 font-mono whitespace-nowrap text-foreground">{category.code}</td>
                          <td className="px-6 py-4">
                            <div className="space-y-1">
                              <p className="font-medium text-foreground">{category.name}</p>
                              <p className="text-xs text-muted-foreground">
                                {category.children_count} subcategoría{category.children_count === 1 ? '' : 's'}
                              </p>
                            </div>
                          </td>
                          <td className="px-6 py-4 text-muted-foreground">{category.slug}</td>
                          <td className="px-6 py-4 text-center">
                            <Badge variant={status.variant}>{status.label}</Badge>
                          </td>
                          <td className="px-6 py-4 text-center">
                            <Link
                              href={subcategories({ category: category.id })}
                              className="inline-flex items-center gap-2 text-sm font-medium text-primary transition-colors hover:text-primary/80"
                              prefetch
                            >
                              <span>Ver subcategorías</span>
                            </Link>
                          </td>
                          <td className="px-6 py-4 text-right">
                            <CategoryRowActions category={category} />
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

CategoriesIndex.layout = {
  breadcrumbs: [
    {
      title: 'Categorías',
      href: categoriesIndex(),
    },
  ],
}

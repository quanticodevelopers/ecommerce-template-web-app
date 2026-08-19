import { Package01Icon } from '@hugeicons/core-free-icons'
import { Head } from '@inertiajs/react'
import Heading from '@/components/heading'
import { Card, CardContent } from '@/components/ui/card'
import ProductPagination from '@/pages/admin/products/components/product-pagination'
import ProductTable from '@/pages/admin/products/components/product-table'
import ProductToolbar from '@/pages/admin/products/components/product-toolbar'
import { index as productsIndex } from '@/routes/admin/products'
import type { PaginatedProducts } from '@/types'

type ProductsIndexProps = {
  products: PaginatedProducts
  filters: {
    search: string
  }
}

export default function ProductsIndex({ products, filters }: ProductsIndexProps) {
  return (
    <>
      <Head title="Productos" />

      <div className="flex flex-col gap-8 p-4 lg:p-8">
        <Heading
          title="Productos"
          description="Administra el catálogo, sus precios y el estado de publicación."
          badgeIcon={Package01Icon}
          badgeLabel="Catálogo"
        />

        <ProductToolbar initialSearch={filters.search} />

        <Card className="gap-0 border-sidebar-border/70 p-0 shadow-none dark:border-sidebar-border">
          <CardContent className="px-0">
            <ProductTable
              hasSearch={filters.search !== ''}
              products={products.data}
            />
            <ProductPagination pagination={products} />
          </CardContent>
        </Card>
      </div>
    </>
  )
}

ProductsIndex.layout = {
  breadcrumbs: [
    {
      title: 'Productos',
      href: productsIndex(),
    },
  ],
}

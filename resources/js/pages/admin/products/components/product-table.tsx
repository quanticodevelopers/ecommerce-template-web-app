import ProductRow from '@/pages/admin/products/components/product-row'
import type { ProductListItem } from '@/types'

type ProductTableProps = {
  products: ProductListItem[]
  hasSearch: boolean
}

export default function ProductTable({ products, hasSearch }: ProductTableProps) {
  return (
    <div className="overflow-x-auto">
      <table className="min-w-full table-auto divide-y divide-border text-sm">
        <thead className="bg-muted/40 text-muted-foreground">
          <tr>
            <th className="min-w-80 px-6 py-3.5 text-left font-medium">Producto</th>
            <th className="min-w-52 px-6 py-3.5 text-left font-medium">Clasificación</th>
            <th className="w-44 px-6 py-3.5 text-right font-medium">Precio</th>
            <th className="w-48 px-6 py-3.5 text-center font-medium">Estado</th>
            <th className="w-32 px-6 py-3.5 text-right font-medium">Acciones</th>
          </tr>
        </thead>
        <tbody className="divide-y divide-border bg-background">
          {products.length === 0 ? (
            <tr>
              <td
                className="px-6 py-14 text-center text-muted-foreground"
                colSpan={5}
              >
                {hasSearch ? 'No encontramos productos para esta búsqueda.' : 'No hay productos registrados.'}
              </td>
            </tr>
          ) : (
            products.map((product) => (
              <ProductRow
                key={product.id}
                product={product}
              />
            ))
          )}
        </tbody>
      </table>
    </div>
  )
}

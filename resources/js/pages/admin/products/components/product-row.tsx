import { BarCode01Icon, Package01Icon, Tag01Icon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Badge } from '@/components/admin/ui/badge'
import ProductRowActions from '@/pages/admin/products/components/product-row-actions'
import type { Product } from '@/types/entities'

type ProductRowProps = {
  product: Product
}

function formatPrice(price: string): string {
  return `S/ ${new Intl.NumberFormat('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(Number(price))}`
}

function getPublicationStatus(publishedAt: string | null): { label: string; variant: 'default' | 'outline' | 'secondary' } {
  if (publishedAt === null) {
    return { label: 'Borrador', variant: 'secondary' }
  }

  if (new Date(publishedAt).getTime() > Date.now()) {
    return { label: 'Programado', variant: 'outline' }
  }

  return { label: 'Publicado', variant: 'default' }
}

export default function ProductRow({ product }: ProductRowProps) {
  const publicationStatus = getPublicationStatus(product.published_at)

  return (
    <tr className="hover:bg-muted/30 align-middle transition-colors">
      <td className="px-6 py-4">
        <div className="flex items-center gap-4">
          {product.thumbnail === null ? (
            <div className="bg-muted/30 text-muted-foreground flex size-14 shrink-0 items-center justify-center rounded-lg border border-dashed">
              <HugeiconsIcon
                icon={Package01Icon}
                className="size-5"
                strokeWidth={1.5}
              />
            </div>
          ) : (
            <img
              alt={product.thumbnail.alt}
              className="size-14 shrink-0 rounded-lg border object-cover"
              src={product.thumbnail.url}
            />
          )}
          <div className="min-w-0">
            <p className="text-foreground truncate font-medium">{product.name}</p>
            <div className="text-muted-foreground mt-1 flex flex-wrap gap-x-3 gap-y-1 text-xs">
              <span className="flex items-center gap-1.5 font-mono">
                <HugeiconsIcon
                  icon={Tag01Icon}
                  className="size-3.5 shrink-0"
                  strokeWidth={1.5}
                />
                {product.sku}
              </span>
              <span className="flex items-center gap-1.5 font-mono">
                <HugeiconsIcon
                  icon={BarCode01Icon}
                  className="size-3.5 shrink-0"
                  strokeWidth={1.5}
                />
                {product.barcode}
              </span>
            </div>
          </div>
        </div>
      </td>
      <td className="px-6 py-4">
        <p className="text-foreground font-medium">{product.category.name}</p>
        <p className="text-muted-foreground mt-1 text-xs">{product.brand.name}</p>
      </td>
      <td className="px-6 py-4 text-right">
        <p className="text-foreground font-semibold">{formatPrice(product.sale_price)}</p>
        {product.base_price !== null && product.base_price !== product.sale_price && (
          <p className="text-muted-foreground mt-1 text-xs line-through">{formatPrice(product.base_price)}</p>
        )}
      </td>
      <td className="px-6 py-4">
        <div className="flex flex-col items-center gap-2">
          <Badge variant={publicationStatus.variant}>{publicationStatus.label}</Badge>
          {product.flag !== null && <span className="text-muted-foreground text-xs">{product.flag.label}</span>}
        </div>
      </td>
      <td className="px-6 py-4 text-right">
        <ProductRowActions product={product} />
      </td>
    </tr>
  )
}

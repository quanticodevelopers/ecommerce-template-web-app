import { BarCode01Icon, Package01Icon, Tag01Icon } from '@hugeicons/core-free-icons'
import { Badge } from '@/components/ui/badge'
import { Icon } from '@/components/ui/icon'
import ProductRowActions from '@/pages/admin/products/components/product-row-actions'
import type { ProductFlag, ProductListItem } from '@/types'

type ProductRowProps = {
  product: ProductListItem
}

function formatPrice(price: string): string {
  return `S/ ${new Intl.NumberFormat('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(Number(price))}`
}

function formatFlag(flag: ProductFlag | null): string | null {
  if (flag === 'featured') {
    return 'Destacado'
  }

  if (flag === 'new') {
    return 'Nuevo'
  }

  return null
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
  const flagLabel = formatFlag(product.flag)
  const publicationStatus = getPublicationStatus(product.published_at)

  return (
    <tr className="align-middle transition-colors hover:bg-muted/30">
      <td className="px-6 py-4">
        <div className="flex items-center gap-4">
          {product.thumbnail === null ? (
            <div className="flex size-14 shrink-0 items-center justify-center rounded-lg border border-dashed bg-muted/30 text-muted-foreground">
              <Icon
                iconNode={Package01Icon}
                className="size-5"
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
            <p className="truncate font-medium text-foreground">{product.name}</p>
            <div className="mt-1 flex flex-wrap gap-x-3 gap-y-1 text-xs text-muted-foreground">
              <span className="flex items-center gap-1.5 font-mono">
                <Icon
                  iconNode={Tag01Icon}
                  className="size-3.5 shrink-0"
                />
                {product.sku}
              </span>
              <span className="flex items-center gap-1.5 font-mono">
                <Icon
                  iconNode={BarCode01Icon}
                  className="size-3.5 shrink-0"
                />
                {product.barcode}
              </span>
            </div>
          </div>
        </div>
      </td>
      <td className="px-6 py-4">
        <p className="font-medium text-foreground">{product.category.name}</p>
        <p className="mt-1 text-xs text-muted-foreground">{product.brand.name}</p>
      </td>
      <td className="px-6 py-4 text-right">
        <p className="font-semibold text-foreground">{formatPrice(product.sale_price)}</p>
        {product.base_price !== null && product.base_price !== product.sale_price && (
          <p className="mt-1 text-xs text-muted-foreground line-through">{formatPrice(product.base_price)}</p>
        )}
      </td>
      <td className="px-6 py-4">
        <div className="flex flex-col items-center gap-2">
          <Badge variant={publicationStatus.variant}>{publicationStatus.label}</Badge>
          {flagLabel !== null && <span className="text-xs text-muted-foreground">{flagLabel}</span>}
        </div>
      </td>
      <td className="px-6 py-4 text-right">
        <ProductRowActions product={product} />
      </td>
    </tr>
  )
}

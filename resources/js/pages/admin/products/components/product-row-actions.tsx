import { Edit02Icon, ExternalLinkIcon, ViewIcon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Link } from '@inertiajs/react'
import { Button } from '@/components/admin/ui/button'
import { edit as productsEdit, show as productsShow } from '@/routes/admin/products'
import type { Product } from '@/types/entities'

type ProductRowActionsProps = {
  product: Product
}

export default function ProductRowActions({ product }: ProductRowActionsProps) {
  return (
    <div className="inline-flex items-center gap-1">
      <Button
        type="button"
        variant="ghost"
        size="icon-xs"
        aria-label={`Abrir ${product.name} en la tienda`}
      >
        <HugeiconsIcon
          icon={ExternalLinkIcon}
          strokeWidth={1.5}
        />
      </Button>

      <Button
        type="button"
        variant="ghost"
        size="icon-xs"
        asChild
      >
        <Link
          aria-label={`Ver detalle de ${product.name}`}
          href={productsShow(product.id)}
          prefetch
        >
          <HugeiconsIcon
            icon={ViewIcon}
            strokeWidth={1.5}
          />
        </Link>
      </Button>

      <Button
        type="button"
        variant="ghost"
        size="icon-xs"
        asChild
      >
        <Link
          aria-label={`Editar ${product.name}`}
          href={productsEdit(product.id)}
          prefetch
        >
          <HugeiconsIcon
            icon={Edit02Icon}
            strokeWidth={1.5}
          />
        </Link>
      </Button>
    </div>
  )
}

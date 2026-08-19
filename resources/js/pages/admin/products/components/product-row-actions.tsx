import { Edit02Icon, ExternalLinkIcon } from '@hugeicons/core-free-icons'
import { Link } from '@inertiajs/react'
import { Button } from '@/components/ui/button'
import { Icon } from '@/components/ui/icon'
import { edit as productsEdit } from '@/routes/admin/products'
import type { ProductListItem } from '@/types'

type ProductRowActionsProps = {
  product: ProductListItem
}

export default function ProductRowActions({ product }: ProductRowActionsProps) {
  return (
    <div className="inline-flex items-center gap-1">
      <Button
        type="button"
        variant="ghost"
        size="icon"
        className="size-8"
        aria-label={`Ver página de ${product.name}`}
      >
        <Icon
          iconNode={ExternalLinkIcon}
          className="size-4"
        />
      </Button>

      <Button
        asChild
        className="size-8"
        size="icon"
        variant="ghost"
      >
        <Link
          aria-label={`Editar ${product.name}`}
          href={productsEdit(product.id)}
        >
          <Icon
            iconNode={Edit02Icon}
            className="size-4"
          />
        </Link>
      </Button>
    </div>
  )
}

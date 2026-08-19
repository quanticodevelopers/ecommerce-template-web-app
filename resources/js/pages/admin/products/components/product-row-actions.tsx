import { ExternalLinkIcon } from '@hugeicons/core-free-icons'
import { Button } from '@/components/ui/button'
import { Icon } from '@/components/ui/icon'
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
    </div>
  )
}

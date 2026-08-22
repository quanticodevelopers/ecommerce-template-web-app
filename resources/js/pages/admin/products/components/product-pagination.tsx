import { ArrowLeft01Icon, ArrowRight01Icon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Link } from '@inertiajs/react'
import { Button } from '@/components/ui/button'
import type { PaginatedProducts } from '@/types'

type ProductPaginationProps = {
  pagination: PaginatedProducts
}

export default function ProductPagination({ pagination }: ProductPaginationProps) {
  const hasProducts = pagination.meta.total > 0

  return (
    <div className="flex flex-col gap-3 border-t px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
      <p className="text-sm text-muted-foreground">
        {hasProducts ? `Mostrando ${pagination.meta.from}–${pagination.meta.to} de ${pagination.meta.total} productos` : '0 productos'}
      </p>
      {pagination.meta.last_page > 1 && (
        <div className="flex items-center gap-2">
          {pagination.links.prev === null ? (
            <Button
              disabled
              size="xs"
              variant="outline"
            >
              <HugeiconsIcon
                icon={ArrowLeft01Icon}
                strokeWidth={1.5}
              />
              Anterior
            </Button>
          ) : (
            <Button
              asChild
              size="xs"
              variant="outline"
            >
              <Link
                href={pagination.links.prev}
                preserveScroll
              >
                <HugeiconsIcon
                  icon={ArrowLeft01Icon}
                  strokeWidth={1.5}
                />
                Anterior
              </Link>
            </Button>
          )}

          <span className="px-2 text-sm text-muted-foreground">
            Página {pagination.meta.current_page} de {pagination.meta.last_page}
          </span>

          {pagination.links.next === null ? (
            <Button
              disabled
              size="xs"
              variant="outline"
            >
              Siguiente
              <HugeiconsIcon
                icon={ArrowRight01Icon}
                strokeWidth={1.5}
              />
            </Button>
          ) : (
            <Button
              asChild
              size="xs"
              variant="outline"
            >
              <Link
                href={pagination.links.next}
                preserveScroll
              >
                Siguiente
                <HugeiconsIcon
                  icon={ArrowRight01Icon}
                  strokeWidth={1.5}
                />
              </Link>
            </Button>
          )}
        </div>
      )}
    </div>
  )
}

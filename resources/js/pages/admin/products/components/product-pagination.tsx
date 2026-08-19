import { ArrowLeft01Icon, ArrowRight01Icon } from '@hugeicons/core-free-icons'
import { Link } from '@inertiajs/react'
import { Button } from '@/components/ui/button'
import { Icon } from '@/components/ui/icon'
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
              size="sm"
              variant="outline"
            >
              <Icon iconNode={ArrowLeft01Icon} />
              Anterior
            </Button>
          ) : (
            <Button
              asChild
              size="sm"
              variant="outline"
            >
              <Link
                href={pagination.links.prev}
                preserveScroll
              >
                <Icon iconNode={ArrowLeft01Icon} />
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
              size="sm"
              variant="outline"
            >
              Siguiente
              <Icon iconNode={ArrowRight01Icon} />
            </Button>
          ) : (
            <Button
              asChild
              size="sm"
              variant="outline"
            >
              <Link
                href={pagination.links.next}
                preserveScroll
              >
                Siguiente
                <Icon iconNode={ArrowRight01Icon} />
              </Link>
            </Button>
          )}
        </div>
      )}
    </div>
  )
}

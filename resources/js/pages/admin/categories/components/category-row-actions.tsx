import { ExternalLinkIcon, ViewIcon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { useState } from 'react'
import { Badge } from '@/components/admin/ui/badge'
import { Button } from '@/components/admin/ui/button'
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/admin/ui/dialog'
import { dateFormatter } from '@/lib/admin/date'
import EditCategoryModal from '@/pages/admin/categories/components/edit-category-modal'
import type { SelectOption } from '@/types'
import type { Category } from '@/types/entities'

type CategoryRowActionsProps = {
  category: Category
  parentCategoryOptions: SelectOption[]
}

function formatDate(value: string | null): string {
  if (!value) {
    return '—'
  }

  return dateFormatter.format(new Date(value))
}

function formatStatus(isActive: boolean): { label: string; variant: 'default' | 'secondary' } {
  return isActive ? { label: 'Activa', variant: 'default' } : { label: 'Inactiva', variant: 'secondary' }
}

function formatParentName(category: Category): string {
  return category.parent?.name ?? '—'
}

function formatDescription(value: string | null): string {
  return value ?? '—'
}

export default function CategoryRowActions({ category, parentCategoryOptions }: CategoryRowActionsProps) {
  const [isOpen, setIsOpen] = useState(false)
  const status = formatStatus(category.is_active)

  return (
    <div className="inline-flex items-center gap-1">
      <Button
        type="button"
        variant="ghost"
        size="icon-xs"
        aria-label={`Ver página de ${category.name}`}
      >
        <HugeiconsIcon
          icon={ExternalLinkIcon}
          strokeWidth={1.5}
        />
      </Button>

      <EditCategoryModal
        category={category}
        parentCategoryOptions={parentCategoryOptions}
      />

      <Button
        type="button"
        variant="ghost"
        size="icon-xs"
        aria-label={`Ver información de ${category.name}`}
        onClick={() => setIsOpen(true)}
      >
        <HugeiconsIcon
          icon={ViewIcon}
          strokeWidth={1.5}
        />
      </Button>

      <Dialog
        open={isOpen}
        onOpenChange={setIsOpen}
      >
        <DialogContent className="sm:max-w-xl">
          <DialogHeader>
            <DialogTitle>Información de la categoría</DialogTitle>
            <DialogDescription>Detalle completo del registro seleccionado.</DialogDescription>
          </DialogHeader>

          <div className="grid gap-4 sm:grid-cols-2">
            <div className="bg-muted/30 space-y-1 rounded-lg border p-3">
              <p className="text-muted-foreground text-xs">Nombre</p>
              <p className="text-foreground font-medium">{category.name}</p>
            </div>

            <div className="bg-muted/30 space-y-1 rounded-lg border p-3">
              <p className="text-muted-foreground text-xs">Código</p>
              <p className="text-foreground font-mono font-medium tracking-wide">{category.code}</p>
            </div>

            <div className="bg-muted/30 space-y-1 rounded-lg border p-3">
              <p className="text-muted-foreground text-xs">Slug</p>
              <p className="text-foreground font-medium">{category.slug}</p>
            </div>

            <div className="bg-muted/30 space-y-1 rounded-lg border p-3">
              <p className="text-muted-foreground text-xs">Estado</p>
              <Badge variant={status.variant}>{status.label}</Badge>
            </div>

            <div className="bg-muted/30 space-y-1 rounded-lg border p-3">
              <p className="text-muted-foreground text-xs">Categoría padre</p>
              <p className="text-foreground font-medium">{formatParentName(category)}</p>
            </div>

            <div className="bg-muted/30 space-y-1 rounded-lg border p-3">
              <p className="text-muted-foreground text-xs">Subcategorías</p>
              <p className="text-foreground font-medium">{category.children_count}</p>
            </div>

            <div className="bg-muted/30 space-y-1 rounded-lg border p-3 sm:col-span-2">
              <p className="text-muted-foreground text-xs">Descripción breve</p>
              <p className="text-foreground font-medium">{formatDescription(category.short_description)}</p>
            </div>

            <div className="bg-muted/30 space-y-1 rounded-lg border p-3 sm:col-span-2">
              <p className="text-muted-foreground text-xs">Registro</p>
              <p className="text-foreground font-medium">{formatDate(category.created_at)}</p>
            </div>
          </div>

          <DialogFooter>
            <DialogClose asChild>
              <Button
                type="button"
                variant="secondary"
              >
                Cerrar
              </Button>
            </DialogClose>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  )
}

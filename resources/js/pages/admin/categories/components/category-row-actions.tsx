import { EyeIcon } from 'lucide-react'
import { useState } from 'react'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { dateFormatter } from '@/lib/utils'
import type { CategoryListItem } from '@/types'

type CategoryRowActionsProps = {
  category: CategoryListItem
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

function formatParentName(category: CategoryListItem): string {
  return category.parent?.name ?? '—'
}

function formatDescription(value: string | null): string {
  return value ?? '—'
}

export default function CategoryRowActions({ category }: CategoryRowActionsProps) {
  const [isOpen, setIsOpen] = useState(false)
  const status = formatStatus(category.is_active)

  return (
    <>
      <Button
        type="button"
        variant="ghost"
        size="icon"
        className="size-8"
        aria-label={`Ver información de ${category.name}`}
        onClick={() => setIsOpen(true)}
      >
        <EyeIcon className="size-4" />
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
            <div className="space-y-1 rounded-lg border bg-muted/30 p-3">
              <p className="text-xs text-muted-foreground">Nombre</p>
              <p className="font-medium text-foreground">{category.name}</p>
            </div>

            <div className="space-y-1 rounded-lg border bg-muted/30 p-3">
              <p className="text-xs text-muted-foreground">Código</p>
              <p className="font-mono font-medium tracking-wide text-foreground">{category.code}</p>
            </div>

            <div className="space-y-1 rounded-lg border bg-muted/30 p-3">
              <p className="text-xs text-muted-foreground">Slug</p>
              <p className="font-medium text-foreground">{category.slug}</p>
            </div>

            <div className="space-y-1 rounded-lg border bg-muted/30 p-3">
              <p className="text-xs text-muted-foreground">Estado</p>
              <Badge variant={status.variant}>{status.label}</Badge>
            </div>

            <div className="space-y-1 rounded-lg border bg-muted/30 p-3">
              <p className="text-xs text-muted-foreground">Categoría padre</p>
              <p className="font-medium text-foreground">{formatParentName(category)}</p>
            </div>

            <div className="space-y-1 rounded-lg border bg-muted/30 p-3">
              <p className="text-xs text-muted-foreground">Subcategorías</p>
              <p className="font-medium text-foreground">{category.children_count}</p>
            </div>

            <div className="space-y-1 rounded-lg border bg-muted/30 p-3 sm:col-span-2">
              <p className="text-xs text-muted-foreground">Descripción breve</p>
              <p className="font-medium text-foreground">{formatDescription(category.short_description)}</p>
            </div>

            <div className="space-y-1 rounded-lg border bg-muted/30 p-3 sm:col-span-2">
              <p className="text-xs text-muted-foreground">Registro</p>
              <p className="font-medium text-foreground">{formatDate(category.created_at)}</p>
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
    </>
  )
}

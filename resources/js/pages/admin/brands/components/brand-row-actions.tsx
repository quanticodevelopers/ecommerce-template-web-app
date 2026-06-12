import { EyeIcon } from 'lucide-react'
import { useState } from 'react'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { dateFormatter } from '@/lib/utils'
import EditBrandModal from '@/pages/admin/brands/components/edit-brand-modal'
import type { BrandListItem } from '@/types'

type BrandRowActionsProps = {
  brand: BrandListItem
}

function formatDate(value: string | null): string {
  if (value === null) {
    return '-'
  }

  return dateFormatter.format(new Date(value))
}

function formatStatus(isActive: boolean): { label: string; variant: 'default' | 'secondary' } {
  return isActive ? { label: 'Activa', variant: 'default' } : { label: 'Inactiva', variant: 'secondary' }
}

function formatDescription(value: string | null): string {
  return value ?? '-'
}

export default function BrandRowActions({ brand }: BrandRowActionsProps) {
  const [isOpen, setIsOpen] = useState(false)
  const status = formatStatus(brand.is_active)

  return (
    <div className="inline-flex items-center gap-1">
      <EditBrandModal brand={brand} />

      <Button
        type="button"
        variant="ghost"
        size="icon"
        className="size-8"
        aria-label={`Ver información de ${brand.name}`}
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
            <DialogTitle>Información de la marca</DialogTitle>
            <DialogDescription>Detalle completo del registro seleccionado.</DialogDescription>
          </DialogHeader>

          <div className="space-y-4">
            <div className="flex items-center gap-4 rounded-lg border bg-muted/30 p-4">
              <div className="flex h-24 shrink-0 items-center justify-center rounded-md border bg-background p-1">
                {brand.logo_url === null ? (
                  <span className="text-[11px] text-muted-foreground">Sin logo</span>
                ) : (
                  <img
                    alt={brand.name}
                    className="aspect-3/2 h-full w-full object-contain"
                    src={brand.logo_url}
                  />
                )}
              </div>

              <div className="min-w-0 space-y-1">
                <p className="text-base font-semibold text-foreground">{brand.name}</p>
                <p className="font-mono text-sm tracking-wide text-muted-foreground">{brand.code}</p>
                <Badge variant={status.variant}>{status.label}</Badge>
              </div>
            </div>

            <div className="grid gap-3 sm:grid-cols-2">
              <div className="space-y-1 rounded-lg border bg-muted/30 p-3">
                <p className="text-xs text-muted-foreground">Nombre</p>
                <p className="font-medium text-foreground">{brand.name}</p>
              </div>

              <div className="space-y-1 rounded-lg border bg-muted/30 p-3">
                <p className="text-xs text-muted-foreground">Código</p>
                <p className="font-mono font-medium tracking-wide text-foreground">{brand.code}</p>
              </div>

              <div className="space-y-1 rounded-lg border bg-muted/30 p-3">
                <p className="text-xs text-muted-foreground">Slug</p>
                <p className="font-medium text-foreground">{brand.slug}</p>
              </div>

              <div className="space-y-1 rounded-lg border bg-muted/30 p-3">
                <p className="text-xs text-muted-foreground">Estado</p>
                <Badge variant={status.variant}>{status.label}</Badge>
              </div>

              <div className="space-y-1 rounded-lg border bg-muted/30 p-3 sm:col-span-2">
                <p className="text-xs text-muted-foreground">Descripción breve</p>
                <p className="font-medium text-foreground">{formatDescription(brand.short_description)}</p>
              </div>

              <div className="space-y-1 rounded-lg border bg-muted/30 p-3 sm:col-span-2">
                <p className="text-xs text-muted-foreground">Registro</p>
                <p className="font-medium text-foreground">{formatDate(brand.created_at)}</p>
              </div>
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

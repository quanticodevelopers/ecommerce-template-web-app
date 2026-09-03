import { ExternalLinkIcon, ViewIcon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { useState } from 'react'
import { Badge } from '@/components/admin/ui/badge'
import { Button } from '@/components/admin/ui/button'
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/admin/ui/dialog'
import { dateFormatter } from '@/lib/admin/date'
import EditBrandModal from '@/pages/admin/brands/components/edit-brand-modal'
import type { Brand } from '@/types/entities'

type BrandRowActionsProps = {
  brand: Brand
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
      <Button
        type="button"
        variant="ghost"
        size="icon-xs"
        aria-label={`Ver página de ${brand.name}`}
      >
        <HugeiconsIcon
          icon={ExternalLinkIcon}
          strokeWidth={1.5}
        />
      </Button>

      <EditBrandModal brand={brand} />

      <Button
        type="button"
        variant="ghost"
        size="icon-xs"
        aria-label={`Ver información de ${brand.name}`}
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
            <DialogTitle>Información de la marca</DialogTitle>
            <DialogDescription>Detalle completo del registro seleccionado.</DialogDescription>
          </DialogHeader>

          <div className="space-y-4">
            <div className="bg-muted/30 flex items-center gap-4 rounded-lg border p-4">
              <div className="bg-background flex h-24 shrink-0 items-center justify-center rounded-md border p-1">
                {brand.logo_url === null ? (
                  <span className="text-muted-foreground text-[11px]">Sin logo</span>
                ) : (
                  <img
                    alt={brand.name}
                    className="aspect-3/2 h-full w-full object-contain"
                    src={brand.logo_url}
                  />
                )}
              </div>

              <div className="min-w-0 space-y-1">
                <p className="text-foreground text-base font-semibold">{brand.name}</p>
                <p className="text-muted-foreground font-mono text-sm tracking-wide">{brand.code}</p>
                <Badge variant={status.variant}>{status.label}</Badge>
              </div>
            </div>

            <div className="grid gap-3 sm:grid-cols-2">
              <div className="bg-muted/30 space-y-1 rounded-lg border p-3">
                <p className="text-muted-foreground text-xs">Nombre</p>
                <p className="text-foreground font-medium">{brand.name}</p>
              </div>

              <div className="bg-muted/30 space-y-1 rounded-lg border p-3">
                <p className="text-muted-foreground text-xs">Código</p>
                <p className="text-foreground font-mono font-medium tracking-wide">{brand.code}</p>
              </div>

              <div className="bg-muted/30 space-y-1 rounded-lg border p-3">
                <p className="text-muted-foreground text-xs">Slug</p>
                <p className="text-foreground font-medium">{brand.slug}</p>
              </div>

              <div className="bg-muted/30 space-y-1 rounded-lg border p-3">
                <p className="text-muted-foreground text-xs">Estado</p>
                <Badge variant={status.variant}>{status.label}</Badge>
              </div>

              <div className="bg-muted/30 space-y-1 rounded-lg border p-3 sm:col-span-2">
                <p className="text-muted-foreground text-xs">Descripción breve</p>
                <p className="text-foreground font-medium">{formatDescription(brand.short_description)}</p>
              </div>

              <div className="bg-muted/30 space-y-1 rounded-lg border p-3 sm:col-span-2">
                <p className="text-muted-foreground text-xs">Registro</p>
                <p className="text-foreground font-medium">{formatDate(brand.created_at)}</p>
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

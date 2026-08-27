import { Call02Icon, Mail01Icon, UserIcon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Button } from '@/components/admin/ui/button'
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/admin/ui/dialog'
import { dateFormatter } from '@/lib/utils'
import type { Customer } from '@/types'

type CustomerDetailModalProps = {
  customer: Customer | null
  isOpen: boolean
  onClose: () => void
}

function formatFullName(customer: Customer): string {
  return `${customer.name} ${customer.last_name}`.trim()
}

function formatDate(value: string | null): string {
  if (!value) {
    return '—'
  }

  return dateFormatter.format(new Date(value))
}

export default function CustomerDetailModal({ customer, isOpen, onClose }: CustomerDetailModalProps) {
  return (
    <Dialog
      open={isOpen}
      onOpenChange={(open) => !open && onClose()}
    >
      <DialogContent className="sm:max-w-md">
        <DialogHeader>
          <DialogTitle className="flex items-center gap-2">
            <HugeiconsIcon
              icon={UserIcon}
              className="size-4"
              strokeWidth={1.5}
            />
            Información del cliente
          </DialogTitle>
          <DialogDescription>Datos completos del cliente registrado en la plataforma.</DialogDescription>
        </DialogHeader>

        {customer !== null && (
          <div className="space-y-4">
            <div className="flex items-center gap-4">
              <div className="bg-muted text-muted-foreground flex size-14 shrink-0 items-center justify-center rounded-full text-xl font-semibold">
                {customer.name.charAt(0).toUpperCase()}
                {customer.last_name.charAt(0).toUpperCase()}
              </div>
              <div>
                <p className="text-foreground text-base font-semibold">{formatFullName(customer)}</p>
                <p className="text-muted-foreground text-sm">
                  {customer.document_type.label} {customer.document_number}
                </p>
              </div>
            </div>

            <div className="bg-muted/30 space-y-3 rounded-lg border p-4 text-sm">
              <div className="flex items-center gap-3">
                <HugeiconsIcon
                  icon={Mail01Icon}
                  className="text-muted-foreground size-4 shrink-0"
                  strokeWidth={1.5}
                />
                <div>
                  <p className="text-muted-foreground text-xs">Correo electrónico</p>
                  <p className="text-foreground font-medium">{customer.email}</p>
                </div>
              </div>

              <div className="flex items-center gap-3">
                <HugeiconsIcon
                  icon={Call02Icon}
                  className="text-muted-foreground size-4 shrink-0"
                  strokeWidth={1.5}
                />
                <div>
                  <p className="text-muted-foreground text-xs">Teléfono</p>
                  <p className="text-foreground font-medium">{customer.phone || '—'}</p>
                </div>
              </div>
            </div>

            <div className="grid grid-cols-1 gap-3 text-sm">
              <div className="bg-muted/30 rounded-lg border p-3">
                <p className="text-muted-foreground text-xs">F. de registro</p>
                <p className="text-foreground mt-1 font-medium">{formatDate(customer.created_at)}</p>
              </div>
            </div>
          </div>
        )}

        <DialogFooter>
          <DialogClose asChild>
            <Button
              type="button"
              onClick={onClose}
            >
              Cerrar
            </Button>
          </DialogClose>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  )
}

import { Call02Icon, Mail01Icon, UserIcon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Button } from '@/components/ui/button'
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
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
              <div className="flex size-14 shrink-0 items-center justify-center rounded-full bg-muted text-xl font-semibold text-muted-foreground">
                {customer.name.charAt(0).toUpperCase()}
                {customer.last_name.charAt(0).toUpperCase()}
              </div>
              <div>
                <p className="text-base font-semibold text-foreground">{formatFullName(customer)}</p>
                <p className="text-sm text-muted-foreground">
                  {customer.document_type.label} {customer.document_number}
                </p>
              </div>
            </div>

            <div className="space-y-3 rounded-lg border bg-muted/30 p-4 text-sm">
              <div className="flex items-center gap-3">
                <HugeiconsIcon
                  icon={Mail01Icon}
                  className="size-4 shrink-0 text-muted-foreground"
                  strokeWidth={1.5}
                />
                <div>
                  <p className="text-xs text-muted-foreground">Correo electrónico</p>
                  <p className="font-medium text-foreground">{customer.email}</p>
                </div>
              </div>

              <div className="flex items-center gap-3">
                <HugeiconsIcon
                  icon={Call02Icon}
                  className="size-4 shrink-0 text-muted-foreground"
                  strokeWidth={1.5}
                />
                <div>
                  <p className="text-xs text-muted-foreground">Teléfono</p>
                  <p className="font-medium text-foreground">{customer.phone || '—'}</p>
                </div>
              </div>
            </div>

            <div className="grid grid-cols-1 gap-3 text-sm">
              <div className="rounded-lg border bg-muted/30 p-3">
                <p className="text-xs text-muted-foreground">F. de registro</p>
                <p className="mt-1 font-medium text-foreground">{formatDate(customer.created_at)}</p>
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

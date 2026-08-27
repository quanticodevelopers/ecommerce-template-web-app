import type { FC } from 'react'
import { Button } from '@/components/admin/ui/button'
import { Dialog, DialogClose, DialogContent, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/admin/ui/dialog'
import { cn } from '@/lib/utils'

interface ForgotPasswordDialogProps {
  triggerClassName?: string
}

const ForgotPasswordDialog: FC<ForgotPasswordDialogProps> = ({ triggerClassName = '' }) => {
  return (
    <Dialog>
      <DialogTrigger className={cn('hover:underline', triggerClassName)}>¿Olvidaste tu contraseña?</DialogTrigger>
      <DialogContent className="sm:max-w-md">
        <DialogHeader>
          <DialogTitle>¿Olvidaste tu contraseña?</DialogTitle>
        </DialogHeader>
        <div className="text-sm">
          Pídele a otro administrador que restablezca tu contraseña desde el panel de administración o ponte en contacto con el desarrollador{' '}
          <a
            href="mailto:quanticodevelopers@gmail.com"
            target="_blank"
            className="hover:underline"
          >
            Quantico Developers
          </a>
        </div>
        <DialogFooter>
          <DialogClose asChild>
            <Button type="button">Entendido</Button>
          </DialogClose>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  )
}

export default ForgotPasswordDialog

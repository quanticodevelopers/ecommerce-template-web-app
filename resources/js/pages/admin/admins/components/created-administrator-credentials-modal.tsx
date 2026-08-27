import { Copy01Icon, Key01Icon, Tick01Icon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { useMemo, useState } from 'react'
import { Button } from '@/components/admin/ui/button'
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/admin/ui/dialog'
import type { CreatedAdministratorCredentials } from '@/types'

type CreatedAdministratorCredentialsModalProps = {
  createdAdministratorCredentials: CreatedAdministratorCredentials | null
}

export default function CreatedAdministratorCredentialsModal({ createdAdministratorCredentials }: CreatedAdministratorCredentialsModalProps) {
  const [isOpen, setIsOpen] = useState(createdAdministratorCredentials !== null)
  const [isCopied, setIsCopied] = useState(false)

  const credentialsToCopy = useMemo(() => {
    if (createdAdministratorCredentials === null) {
      return ''
    }

    return `Usuario: ${createdAdministratorCredentials.email}\nContraseña: ${createdAdministratorCredentials.password}`
  }, [createdAdministratorCredentials])

  async function copyCredentials(): Promise<void> {
    if (createdAdministratorCredentials === null || typeof navigator?.clipboard === 'undefined') {
      return
    }

    try {
      await navigator.clipboard.writeText(credentialsToCopy)
      setIsCopied(true)
    } catch {
      setIsCopied(false)
    }
  }

  return (
    <Dialog
      open={isOpen}
      onOpenChange={setIsOpen}
    >
      <DialogContent>
        <DialogHeader>
          <DialogTitle className="flex items-center gap-2">
            <HugeiconsIcon
              icon={Key01Icon}
              strokeWidth={1.5}
            />
            Credenciales generadas
          </DialogTitle>
          <DialogDescription>Guarda estas credenciales y compartelas con el usuario administrador correspondiente.</DialogDescription>
        </DialogHeader>

        {createdAdministratorCredentials !== null && (
          <div className="bg-muted/30 space-y-3 rounded-lg border p-4 text-sm">
            <p>
              <span className="text-foreground font-medium">Nombre:</span> {createdAdministratorCredentials.name}
            </p>
            <p>
              <span className="text-foreground font-medium">Usuario:</span> {createdAdministratorCredentials.email}
            </p>
            <p>
              <span className="text-foreground font-medium">Contraseña:</span> {createdAdministratorCredentials.password}
            </p>
          </div>
        )}

        <DialogFooter>
          <Button
            type="button"
            variant="outline"
            onClick={copyCredentials}
            disabled={createdAdministratorCredentials === null}
          >
            <HugeiconsIcon
              icon={isCopied ? Tick01Icon : Copy01Icon}
              strokeWidth={1.5}
            />
            {isCopied ? 'Copiado' : 'Copiar'}
          </Button>

          <DialogClose asChild>
            <Button type="button">Entendido</Button>
          </DialogClose>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  )
}

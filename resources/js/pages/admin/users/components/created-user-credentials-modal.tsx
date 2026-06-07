import { CheckIcon, CopyIcon, KeyRoundIcon } from 'lucide-react'
import { useMemo, useState } from 'react'
import { Button } from '@/components/ui/button'
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import type { CreatedUserCredentials } from '@/types'

type CreatedUserCredentialsModalProps = {
  createdUserCredentials: CreatedUserCredentials | null
}

export default function CreatedUserCredentialsModal({ createdUserCredentials }: CreatedUserCredentialsModalProps) {
  const [isOpen, setIsOpen] = useState(createdUserCredentials !== null)
  const [isCopied, setIsCopied] = useState(false)

  const credentialsToCopy = useMemo(() => {
    if (createdUserCredentials === null) {
      return ''
    }

    return `Usuario: ${createdUserCredentials.email}\nContraseña: ${createdUserCredentials.password}`
  }, [createdUserCredentials])

  async function copyCredentials(): Promise<void> {
    if (createdUserCredentials === null || typeof navigator?.clipboard === 'undefined') {
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
            <KeyRoundIcon className="size-4" />
            Credenciales generadas
          </DialogTitle>
          <DialogDescription>Guarda estas credenciales y compártelas con el administrador creado.</DialogDescription>
        </DialogHeader>

        {createdUserCredentials !== null && (
          <div className="space-y-3 rounded-lg border bg-muted/30 p-4 text-sm">
            <p>
              <span className="font-medium text-foreground">Nombre:</span> {createdUserCredentials.name}
            </p>
            <p>
              <span className="font-medium text-foreground">Usuario:</span> {createdUserCredentials.email}
            </p>
            <p>
              <span className="font-medium text-foreground">Contraseña:</span> {createdUserCredentials.password}
            </p>
          </div>
        )}

        <DialogFooter>
          <Button
            type="button"
            variant="outline"
            onClick={copyCredentials}
            disabled={createdUserCredentials === null}
          >
            {isCopied ? <CheckIcon className="size-4" /> : <CopyIcon className="size-4" />}
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

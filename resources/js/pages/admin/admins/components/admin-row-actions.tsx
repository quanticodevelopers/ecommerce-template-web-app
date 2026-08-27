import { Key01Icon, MoreVerticalIcon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Form } from '@inertiajs/react'
import { useState } from 'react'
import { resetPassword as resetAdministratorPassword } from '@/actions/App/Http/Controllers/Admin/AdministratorController'
import InputError from '@/components/admin/input-error'
import PasswordInput from '@/components/admin/password-input'
import { Button } from '@/components/admin/ui/button'
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/admin/ui/dialog'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/admin/ui/dropdown-menu'
import { Label } from '@/components/admin/ui/label'
import { Spinner } from '@/components/admin/ui/spinner'
import type { AdministratorListItem } from '@/types'

type AdminRowActionsProps = {
  administrator: AdministratorListItem
}

export default function AdminRowActions({ administrator }: AdminRowActionsProps) {
  const [isResetPasswordOpen, setIsResetPasswordOpen] = useState(false)

  return (
    <>
      <DropdownMenu>
        <DropdownMenuTrigger asChild>
          <Button
            type="button"
            variant="ghost"
            size="icon-xs"
            aria-label={`Acciones para ${administrator.email}`}
          >
            <HugeiconsIcon
              icon={MoreVerticalIcon}
              strokeWidth={1.5}
            />
          </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent
          align="end"
          className="w-52"
        >
          <DropdownMenuItem
            onSelect={(event) => {
              event.preventDefault()
              setIsResetPasswordOpen(true)
            }}
          >
            <HugeiconsIcon
              icon={Key01Icon}
              strokeWidth={1.5}
            />
            Restablecer contraseña
          </DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>

      <Dialog
        open={isResetPasswordOpen}
        onOpenChange={setIsResetPasswordOpen}
      >
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Restablecer contraseña de usuario administrador</DialogTitle>
            <DialogDescription>
              Por seguridad, confirma tu contraseña para restablecer la contraseña de {administrator.name} {administrator.last_name}.
            </DialogDescription>
          </DialogHeader>

          <Form
            {...resetAdministratorPassword.form(administrator.id)}
            options={{
              preserveScroll: true,
            }}
            onSuccess={() => setIsResetPasswordOpen(false)}
            resetOnSuccess
            className="space-y-5"
          >
            {({ errors, processing, resetAndClearErrors }) => (
              <>
                <div className="grid gap-2">
                  <Label htmlFor={`action-password-${administrator.id}`}>Contraseña actual</Label>
                  <PasswordInput
                    id={`action-password-${administrator.id}`}
                    name="password"
                    autoComplete="current-password"
                    placeholder="Contraseña"
                    required
                  />
                  <InputError message={errors.password} />
                </div>

                <DialogFooter>
                  <DialogClose asChild>
                    <Button
                      type="button"
                      variant="secondary"
                      onClick={() => {
                        resetAndClearErrors()
                        setIsResetPasswordOpen(false)
                      }}
                    >
                      Cancelar
                    </Button>
                  </DialogClose>

                  <Button
                    type="submit"
                    variant="default"
                    disabled={processing}
                  >
                    {processing && <Spinner />}
                    Restablecer contraseña
                  </Button>
                </DialogFooter>
              </>
            )}
          </Form>
        </DialogContent>
      </Dialog>
    </>
  )
}

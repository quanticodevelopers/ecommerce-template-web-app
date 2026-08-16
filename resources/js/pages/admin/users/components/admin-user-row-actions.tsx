import { Key01Icon, MoreVerticalIcon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Form } from '@inertiajs/react'
import { useState } from 'react'
import { resetPassword as usersResetPassword } from '@/actions/App/Http/Controllers/Admin/UserController'
import InputError from '@/components/input-error'
import PasswordInput from '@/components/password-input'
import { Button } from '@/components/ui/button'
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import { Label } from '@/components/ui/label'
import { Spinner } from '@/components/ui/spinner'
import type { UserListItem } from '@/types'

type AdminUserRowActionsProps = {
  user: UserListItem
}

export default function AdminUserRowActions({ user }: AdminUserRowActionsProps) {
  const [isResetPasswordOpen, setIsResetPasswordOpen] = useState(false)

  return (
    <>
      <DropdownMenu>
        <DropdownMenuTrigger asChild>
          <Button
            variant="ghost"
            size="icon"
            className="size-8"
            aria-label={`Acciones para ${user.email}`}
          >
            <HugeiconsIcon
              icon={MoreVerticalIcon}
              className="size-4"
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
              className="size-4"
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
              Por seguridad, confirma tu contraseña para restablecer la contraseña de {user.name} {user.last_name}.
            </DialogDescription>
          </DialogHeader>

          <Form
            {...usersResetPassword.form(user.id)}
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
                  <Label htmlFor={`action-password-${user.id}`}>Contraseña actual</Label>
                  <PasswordInput
                    id={`action-password-${user.id}`}
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

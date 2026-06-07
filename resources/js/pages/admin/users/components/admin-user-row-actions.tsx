import { Form } from '@inertiajs/react'
import { EllipsisVerticalIcon, UserCheckIcon, UserXIcon } from 'lucide-react'
import { useState } from 'react'
import { deactivate as usersDeactivate, reactivate as usersReactivate } from '@/actions/App/Http/Controllers/Admin/UserController'
import InputError from '@/components/input-error'
import PasswordInput from '@/components/password-input'
import { Button } from '@/components/ui/button'
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu'
import { Label } from '@/components/ui/label'
import type { UserListItem } from '@/types'

type AdminUserRowActionsProps = {
  user: UserListItem
  currentUserId: string | null
}

export default function AdminUserRowActions({ user, currentUserId }: AdminUserRowActionsProps) {
  const [actionType, setActionType] = useState<'deactivate' | 'reactivate' | null>(null)
  const isCurrentSessionUser = currentUserId === user.id
  const canDeactivate = user.is_active && !isCurrentSessionUser
  const canReactivate = !user.is_active
  const isConfirmationDialogOpen = actionType !== null
  const isDeactivateAction = actionType === 'deactivate'

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
            <EllipsisVerticalIcon className="size-4" />
          </Button>
        </DropdownMenuTrigger>

        <DropdownMenuContent align="end">
          {user.is_active ? (
            <DropdownMenuItem
              variant="destructive"
              disabled={!canDeactivate}
              onSelect={(event) => {
                if (!canDeactivate) {
                  return
                }

                event.preventDefault()
                setActionType('deactivate')
              }}
            >
              <UserXIcon className="size-4" />
              {isCurrentSessionUser ? 'No puedes desactivarte' : 'Desactivar usuario'}
            </DropdownMenuItem>
          ) : (
            <DropdownMenuItem
              disabled={!canReactivate}
              onSelect={(event) => {
                if (!canReactivate) {
                  return
                }

                event.preventDefault()
                setActionType('reactivate')
              }}
            >
              <UserCheckIcon className="size-4" />
              Activar usuario
            </DropdownMenuItem>
          )}
        </DropdownMenuContent>
      </DropdownMenu>

      <Dialog
        open={isConfirmationDialogOpen}
        onOpenChange={(open) => setActionType(open ? actionType : null)}
      >
        <DialogContent>
          <DialogHeader>
            <DialogTitle>{isDeactivateAction ? 'Desactivar usuario administrador' : 'Reactivar usuario administrador'}</DialogTitle>
            <DialogDescription>
              Por seguridad, confirma tu contrasena para {isDeactivateAction ? 'desactivar' : 'activar'} a {user.name} {user.last_name}.
            </DialogDescription>
          </DialogHeader>

          <Form
            {...(isDeactivateAction ? usersDeactivate.form(user.id) : usersReactivate.form(user.id))}
            options={{
              preserveScroll: true,
            }}
            onSuccess={() => setActionType(null)}
            resetOnSuccess
            className="space-y-5"
          >
            {({ errors, processing, resetAndClearErrors }) => (
              <>
                <div className="grid gap-2">
                  <Label htmlFor={`deactivate-password-${user.id}`}>Contrasena actual</Label>
                  <PasswordInput
                    id={`deactivate-password-${user.id}`}
                    name="password"
                    autoComplete="current-password"
                    placeholder="Contrasena"
                    required
                  />
                  <InputError message={errors.password} />
                  <InputError message={errors.deactivate_user} />
                </div>

                <DialogFooter>
                  <DialogClose asChild>
                    <Button
                      type="button"
                      variant="secondary"
                      onClick={() => {
                        resetAndClearErrors()
                        setActionType(null)
                      }}
                    >
                      Cancelar
                    </Button>
                  </DialogClose>

                  <Button
                    type="submit"
                    variant={isDeactivateAction ? 'destructive' : 'default'}
                    disabled={processing}
                  >
                    {isDeactivateAction ? 'Desactivar' : 'Activar'}
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

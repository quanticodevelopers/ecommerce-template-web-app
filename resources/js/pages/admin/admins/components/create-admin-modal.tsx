import { UserAdd01Icon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Form } from '@inertiajs/react'
import { useState } from 'react'
import { store as storeAdministrator } from '@/actions/App/Http/Controllers/Admin/AdministratorController'
import InputError from '@/components/input-error'
import { Button } from '@/components/ui/button'
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Spinner } from '@/components/ui/spinner'
import { cn } from '@/lib/utils'

type CreateAdminModalProps = {
  triggerClassName?: string
}

export default function CreateAdminModal({ triggerClassName }: CreateAdminModalProps) {
  const [isOpen, setIsOpen] = useState(false)

  return (
    <Dialog
      open={isOpen}
      onOpenChange={setIsOpen}
    >
      <DialogTrigger asChild>
        <Button className={cn(triggerClassName)}>
          <HugeiconsIcon
            icon={UserAdd01Icon}
            strokeWidth={1.5}
          />
          Nuevo administrador
        </Button>
      </DialogTrigger>

      <DialogContent className="sm:max-w-2xl">
        <DialogHeader>
          <DialogTitle>Crear administrador</DialogTitle>
          <DialogDescription>Completa los datos obligatorios. La contraseña se generara automáticamente.</DialogDescription>
        </DialogHeader>

        <Form
          {...storeAdministrator.form()}
          options={{
            preserveScroll: true,
          }}
          onSuccess={() => setIsOpen(false)}
          resetOnSuccess
          className="space-y-5"
        >
          {({ errors, processing, resetAndClearErrors }) => (
            <>
              <div className="grid grid-cols-1 items-start gap-4 md:grid-cols-2">
                <div className="grid gap-2">
                  <Label htmlFor="name">Nombres</Label>
                  <Input
                    id="name"
                    name="name"
                    maxLength={64}
                    required
                    placeholder="Nombres"
                  />
                  <InputError message={errors.name} />
                </div>

                <div className="grid gap-2">
                  <Label htmlFor="last_name">Apellidos</Label>
                  <Input
                    id="last_name"
                    name="last_name"
                    maxLength={64}
                    required
                    placeholder="Apellidos"
                  />
                  <InputError message={errors.last_name} />
                </div>

                <div className="grid gap-2">
                  <Label htmlFor="phone">Celular</Label>
                  <Input
                    id="phone"
                    name="phone"
                    maxLength={9}
                    minLength={9}
                    required
                    placeholder="Ej: 987654321"
                  />
                  <InputError message={errors.phone} />
                </div>

                <div className="grid gap-2">
                  <Label htmlFor="email">Correo electrónico</Label>
                  <Input
                    id="email"
                    type="email"
                    name="email"
                    maxLength={128}
                    required
                    placeholder="Ej: correo@ejemplo.com"
                  />
                  <InputError message={errors.email} />
                </div>
              </div>

              <DialogFooter>
                <DialogClose asChild>
                  <Button
                    type="button"
                    variant="secondary"
                    onClick={() => resetAndClearErrors()}
                  >
                    Cancelar
                  </Button>
                </DialogClose>

                <Button
                  type="submit"
                  disabled={processing}
                >
                  {processing && <Spinner />}
                  Crear administrador
                </Button>
              </DialogFooter>
            </>
          )}
        </Form>
      </DialogContent>
    </Dialog>
  )
}

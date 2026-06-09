import { Form } from '@inertiajs/react'
import { UserPlusIcon } from 'lucide-react'
import { useState } from 'react'
import { store as usersStore } from '@/actions/App/Http/Controllers/Admin/UserController'
import InputError from '@/components/input-error'
import { Button } from '@/components/ui/button'
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Spinner } from '@/components/ui/spinner'
import { cn } from '@/lib/utils'
import type { SelectOption } from '@/types'

type CreateAdminUserModalProps = {
  documentTypeOptions: SelectOption[]
  triggerClassName?: string
}

export default function CreateAdminUserModal({ documentTypeOptions, triggerClassName }: CreateAdminUserModalProps) {
  const [isOpen, setIsOpen] = useState(false)

  return (
    <Dialog
      open={isOpen}
      onOpenChange={setIsOpen}
    >
      <DialogTrigger asChild>
        <Button className={cn(triggerClassName)}>
          <UserPlusIcon className="size-4" />
          Nuevo usuario
        </Button>
      </DialogTrigger>

      <DialogContent className="sm:max-w-2xl">
        <DialogHeader>
          <DialogTitle>Crear usuario</DialogTitle>
          <DialogDescription>Completa los datos obligatorios. La contraseña se generara automáticamente.</DialogDescription>
        </DialogHeader>

        <Form
          {...usersStore.form()}
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
                  <Label htmlFor="document_type">Tipo de documento</Label>
                  <Select
                    name="document_type"
                    defaultValue={documentTypeOptions[0]?.value}
                  >
                    <SelectTrigger
                      id="document_type"
                      className="w-full"
                    >
                      <SelectValue placeholder="Selecciona un tipo" />
                    </SelectTrigger>
                    <SelectContent>
                      {documentTypeOptions.map((documentType) => (
                        <SelectItem
                          key={documentType.value}
                          value={documentType.value}
                        >
                          {documentType.label}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                  <InputError message={errors.document_type} />
                </div>

                <div className="grid gap-2">
                  <Label htmlFor="document_number">Número de documento</Label>
                  <Input
                    id="document_number"
                    name="document_number"
                    maxLength={12}
                    required
                    placeholder="Documento"
                  />
                  <InputError message={errors.document_number} />
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
                  Crear usuario
                </Button>
              </DialogFooter>
            </>
          )}
        </Form>
      </DialogContent>
    </Dialog>
  )
}

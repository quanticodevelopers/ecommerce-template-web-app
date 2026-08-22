import { Form, Head } from '@inertiajs/react'
import ProfileController from '@/actions/App/Http/Controllers/Admin/Settings/ProfileController'
import Heading from '@/components/heading'
import InputError from '@/components/input-error'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Spinner } from '@/components/ui/spinner'
import { useAuthenticatedUser } from '@/hooks/use-authenticated-user'
import { edit } from '@/routes/admin/profile'
import type { SelectOption } from '@/types'

const Profile = ({ document_type_options }: { document_type_options: SelectOption[] }) => {
  const user = useAuthenticatedUser()

  return (
    <>
      <Head title="Configuración de tu perfil" />

      <div className="space-y-6">
        <Heading
          variant="small"
          title="Configuración de tu perfil"
          description="Actualiza tu documento, nombre completo, celular y correo electrónico."
        />

        <Form
          {...ProfileController.update.form()}
          options={{
            preserveScroll: true,
          }}
          className="grid gap-6"
        >
          {({ processing, errors }) => (
            <>
              <div className="grid grid-cols-1 gap-4 xl:grid-cols-2">
                <div className="grid gap-2">
                  <Label htmlFor="name">Nombre</Label>

                  <Input
                    id="name"
                    className="mt-1 block w-full"
                    defaultValue={user.name}
                    name="name"
                    required
                    autoComplete="name"
                    placeholder="Nombre"
                  />

                  <InputError
                    className="mt-2"
                    message={errors.name}
                  />
                </div>
                <div className="grid gap-2">
                  <Label htmlFor="last_name">Apellido</Label>

                  <Input
                    id="last_name"
                    className="mt-1 block w-full"
                    defaultValue={user.last_name}
                    name="last_name"
                    required
                    autoComplete="family-name"
                    placeholder="Apellido"
                  />

                  <InputError
                    className="mt-2"
                    message={errors.last_name}
                  />
                </div>
              </div>

              <div className="grid grid-cols-1 gap-4 xl:grid-cols-2">
                <div className="grid gap-2">
                  <Label htmlFor="document_type">Tipo de documento</Label>

                  <Select
                    defaultValue={user.document_type.value}
                    name="document_type"
                  >
                    <SelectTrigger
                      id="document_type"
                      className="mt-1 w-full"
                    >
                      <SelectValue placeholder="Selecciona un tipo" />
                    </SelectTrigger>

                    <SelectContent>
                      {document_type_options.map((documentType) => (
                        <SelectItem
                          value={documentType.value}
                          key={documentType.value}
                        >
                          {documentType.label}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>

                  <InputError
                    className="mt-2"
                    message={errors.document_type}
                  />
                </div>
                <div className="grid gap-2">
                  <Label htmlFor="document_number">Número de documento</Label>

                  <Input
                    id="document_number"
                    className="mt-1 block w-full"
                    defaultValue={user.document_number}
                    name="document_number"
                    required
                    placeholder="Número de documento"
                  />

                  <InputError
                    className="mt-2"
                    message={errors.document_number}
                  />
                </div>
              </div>

              <div className="grid grid-cols-1 gap-4 xl:grid-cols-2">
                <div className="grid gap-2">
                  <Label htmlFor="phone">Teléfono</Label>

                  <Input
                    id="phone"
                    className="mt-1 block w-full"
                    defaultValue={user.phone}
                    name="phone"
                    required
                    placeholder="Ej: 987654321"
                  />

                  <InputError
                    className="mt-2"
                    message={errors.phone}
                  />
                </div>
                <div className="grid gap-2">
                  <Label htmlFor="email">Correo electrónico</Label>

                  <Input
                    id="email"
                    type="email"
                    className="mt-1 block w-full"
                    defaultValue={user.email}
                    name="email"
                    required
                    autoComplete="username"
                    placeholder="correo@ejemplo.com"
                  />

                  <InputError
                    className="mt-2"
                    message={errors.email}
                  />
                </div>
              </div>

              <div className="flex items-center gap-4">
                <Button
                  disabled={processing}
                  data-test="update-profile-button"
                >
                  {processing && <Spinner />}
                  Guardar
                </Button>
              </div>
            </>
          )}
        </Form>
      </div>
    </>
  )
}

Profile.layout = {
  breadcrumbs: [
    {
      title: 'Ajustes de perfil',
      href: edit(),
    },
  ],
}

export default Profile

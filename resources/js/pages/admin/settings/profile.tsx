import { Form, Head } from '@inertiajs/react'
import ProfileController from '@/actions/App/Http/Controllers/Admin/Settings/ProfileController'
import Heading from '@/components/admin/heading'
import InputError from '@/components/admin/input-error'
import { Button } from '@/components/admin/ui/button'
import { Input } from '@/components/admin/ui/input'
import { Label } from '@/components/admin/ui/label'
import { Spinner } from '@/components/admin/ui/spinner'
import { useAuthenticatedUser } from '@/hooks/use-authenticated-user'
import { edit } from '@/routes/admin/profile'

const Profile = () => {
  const user = useAuthenticatedUser()

  return (
    <>
      <Head title="Configuración de tu perfil" />

      <div className="space-y-6">
        <Heading
          variant="small"
          title="Configuración de tu perfil"
          description="Actualiza tu nombre completo, celular y correo electrónico."
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

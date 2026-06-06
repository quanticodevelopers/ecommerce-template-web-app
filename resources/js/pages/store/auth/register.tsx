import { Form, Head } from '@inertiajs/react'
import InputError from '@/components/input-error'
import PasswordInput from '@/components/password-input'
import TextLink from '@/components/text-link'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Spinner } from '@/components/ui/spinner'
import { store } from '@/routes/register'
import { login } from '@/routes/store/auth'
import type { SelectOption } from '@/types'

interface RegisterProps {
  document_type_options: SelectOption[]
  passwordRules: string
}

export default function Register({ document_type_options, passwordRules }: RegisterProps) {
  return (
    <>
      <Head title="Registro" />
      <Form
        {...store.form()}
        resetOnSuccess={['password', 'password_confirmation']}
        disableWhileProcessing
        className="flex flex-col gap-6"
      >
        {({ processing, errors }) => (
          <>
            <div className="grid gap-6">
              <div className="grid grid-cols-1 gap-4 xl:grid-cols-2">
                <div className="grid gap-2">
                  <Label htmlFor="name">Nombre</Label>

                  <Input
                    id="name"
                    className="mt-1 block w-full"
                    name="name"
                    required
                    autoComplete="name"
                    placeholder="Nombre"
                    tabIndex={1}
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
                    name="last_name"
                    required
                    autoComplete="family-name"
                    placeholder="Apellido"
                    tabIndex={2}
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

                  <Select name="document_type">
                    <SelectTrigger
                      id="document_type"
                      className="mt-1 w-full"
                      tabIndex={3}
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
                    name="document_number"
                    required
                    placeholder="Número de documento"
                    tabIndex={4}
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
                    name="phone"
                    required
                    placeholder="Ej: 987654321"
                    tabIndex={5}
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
                    name="email"
                    required
                    autoComplete="username"
                    placeholder="correo@ejemplo.com"
                    tabIndex={6}
                  />

                  <InputError
                    className="mt-2"
                    message={errors.email}
                  />
                </div>
              </div>

              <div className="grid grid-cols-1 gap-4 xl:grid-cols-2">
                <div className="grid gap-2">
                  <Label htmlFor="password">Contraseña</Label>
                  <PasswordInput
                    id="password"
                    required
                    autoComplete="new-password"
                    name="password"
                    placeholder="Contraseña"
                    passwordrules={passwordRules}
                    tabIndex={7}
                  />
                  <InputError message={errors.password} />
                </div>
                <div className="grid gap-2">
                  <Label htmlFor="password_confirmation">Confirmar contraseña</Label>
                  <PasswordInput
                    id="password_confirmation"
                    required
                    autoComplete="new-password"
                    name="password_confirmation"
                    placeholder="Confirmar contraseña"
                    passwordrules={passwordRules}
                    tabIndex={8}
                  />
                  <InputError message={errors.password_confirmation} />
                </div>
              </div>

              <Button
                type="submit"
                className="mt-2 w-full"
                tabIndex={9}
                data-test="register-user-button"
              >
                {processing && <Spinner />}
                Crear cuenta
              </Button>
            </div>

            <div className="text-center text-sm text-muted-foreground">
              ¿Ya tienes una cuenta?{' '}
              <TextLink
                href={login()}
                tabIndex={10}
              >
                Inicia sesión
              </TextLink>
            </div>
          </>
        )}
      </Form>
    </>
  )
}

Register.layout = {
  title: 'Crear una cuenta',
  description: 'Ingresa tus datos para crear tu cuenta',
}

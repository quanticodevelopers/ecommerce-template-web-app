import { CheckmarkCircle02Icon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Form, Head } from '@inertiajs/react'
import InputError from '@/components/input-error'
import PasswordInput from '@/components/password-input'
import TextLink from '@/components/text-link'
import { Alert, AlertDescription } from '@/components/ui/alert'
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Spinner } from '@/components/ui/spinner'
import { request } from '@/routes//store/auth/password'
import { store } from '@/routes/login'
import { register } from '@/routes/store/auth'

type Props = {
  status?: string
  canResetPassword: boolean
}

export default function Login({ status, canResetPassword }: Props) {
  return (
    <>
      <Head title="Iniciar sesión" />

      <Form
        {...store.form()}
        resetOnSuccess={['password']}
        className="flex flex-col gap-6"
      >
        {({ processing, errors }) => (
          <>
            <div className="grid gap-6">
              <div className="grid gap-2">
                <Label htmlFor="email">Correo electrónico</Label>
                <Input
                  id="email"
                  type="email"
                  name="email"
                  required
                  autoFocus
                  tabIndex={1}
                  autoComplete="email"
                  placeholder="correo@ejemplo.com"
                />
                <InputError message={errors.email} />
              </div>

              <div className="grid gap-2">
                <div className="flex items-center">
                  <Label htmlFor="password">Contraseña</Label>
                  {canResetPassword && (
                    <TextLink
                      href={request()}
                      className="ml-auto text-sm"
                      tabIndex={5}
                    >
                      ¿Olvidaste tu contraseña?
                    </TextLink>
                  )}
                </div>
                <PasswordInput
                  id="password"
                  name="password"
                  required
                  tabIndex={2}
                  autoComplete="current-password"
                  placeholder="Contraseña"
                />
                <InputError message={errors.password} />
              </div>

              <div className="flex items-center space-x-3">
                <Checkbox
                  id="remember"
                  name="remember"
                  tabIndex={3}
                />
                <Label htmlFor="remember">Recordarme</Label>
              </div>

              <Button
                type="submit"
                className="mt-4 w-full"
                tabIndex={4}
                disabled={processing}
                data-test="login-button"
              >
                {processing && <Spinner />}
                Iniciar sesión
              </Button>
            </div>

            <div className="text-center text-sm text-muted-foreground">
              ¿No tienes una cuenta?{' '}
              <TextLink
                href={register()}
                tabIndex={5}
              >
                Regístrate
              </TextLink>
            </div>
          </>
        )}
      </Form>

      {status && (
        <Alert className="w-full text-green-700 dark:text-green-400">
          <HugeiconsIcon icon={CheckmarkCircle02Icon} />
          <AlertDescription className="text-green-700/80 dark:text-green-400/80">{status}</AlertDescription>
        </Alert>
      )}
    </>
  )
}

Login.layout = {
  title: 'Inicia sesión en tu cuenta',
  description: 'Ingresa tu correo y contraseña para iniciar sesión',
}

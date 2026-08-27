import { CheckmarkCircle02Icon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Form, Head } from '@inertiajs/react'
import InputError from '@/components/store/input-error'
import TextLink from '@/components/store/text-link'
import { Alert, AlertDescription } from '@/components/store/ui/alert'
import { Button } from '@/components/store/ui/button'
import { Input } from '@/components/store/ui/input'
import { Label } from '@/components/store/ui/label'
import { Spinner } from '@/components/store/ui/spinner'
import { login } from '@/routes/store/auth'
import { email } from '@/routes/store/auth/password'

export default function ForgotPassword({ status }: { status?: string }) {
  return (
    <>
      <Head title="Olvidé mi contraseña" />

      {status && (
        <Alert className="w-full text-green-700 dark:text-green-400">
          <HugeiconsIcon
            icon={CheckmarkCircle02Icon}
            strokeWidth={1.5}
          />
          <AlertDescription className="text-green-700/80 dark:text-green-400/80">{status}</AlertDescription>
        </Alert>
      )}

      <div className="space-y-6">
        <Form {...email.form()}>
          {({ processing, errors }) => (
            <>
              <div className="grid gap-2">
                <Label htmlFor="email">Correo electrónico</Label>
                <Input
                  id="email"
                  type="email"
                  name="email"
                  autoComplete="off"
                  autoFocus
                  placeholder="correo@ejemplo.com"
                />

                <InputError message={errors.email} />
              </div>

              <div className="my-6 flex items-center justify-start">
                <Button
                  className="w-full"
                  disabled={processing}
                  data-test="email-password-reset-link-button"
                >
                  {processing && <Spinner />}
                  Enviar enlace de recuperación
                </Button>
              </div>
            </>
          )}
        </Form>

        <div className="text-muted-foreground space-x-1 text-center text-sm">
          <span>O volver a</span>
          <TextLink href={login()}>iniciar sesión</TextLink>
        </div>
      </div>
    </>
  )
}

ForgotPassword.layout = {
  title: 'Olvidé mi contraseña',
  description: 'Ingresa tu correo para recibir un enlace de recuperación',
}

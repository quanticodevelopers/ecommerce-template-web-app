import { CheckmarkCircle02Icon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Form, Head } from '@inertiajs/react'
import TextLink from '@/components/text-link'
import { Alert, AlertDescription } from '@/components/ui/alert'
import { Button } from '@/components/ui/button'
import { Spinner } from '@/components/ui/spinner'
import { logout } from '@/routes'
import { send } from '@/routes/store/verification'

export default function VerifyEmail({ status }: { status?: string }) {
  return (
    <>
      <Head title="Verificación de correo" />

      {status === 'verification-link-sent' && (
        <Alert className="w-full text-green-700 dark:text-green-400">
          <HugeiconsIcon icon={CheckmarkCircle02Icon} />
          <AlertDescription className="text-green-700/80 dark:text-green-400/80">
            Se ha enviado un nuevo enlace de verificación al correo electrónico que proporcionaste durante el registro.
          </AlertDescription>
        </Alert>
      )}

      <Form
        {...send.form()}
        className="space-y-6 text-center"
      >
        {({ processing }) => (
          <>
            <Button
              disabled={processing}
              variant="secondary"
            >
              {processing && <Spinner />}
              Reenviar correo de verificación
            </Button>

            <TextLink
              href={logout()}
              className="mx-auto block text-sm"
            >
              Cerrar sesión
            </TextLink>
          </>
        )}
      </Form>
    </>
  )
}

VerifyEmail.layout = {
  title: 'Verificación de correo',
  description: 'Por favor verifica tu dirección de correo haciendo clic en el enlace que te enviamos.',
}

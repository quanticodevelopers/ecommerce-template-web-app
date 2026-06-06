import type { PropsWithChildren } from 'react'

interface AuthLayoutProps extends PropsWithChildren {
  title?: string
  description?: string
}

const AuthLayout = ({ title = '', description = '', children }: AuthLayoutProps) => {
  return (
    <div className="py-8">
      <div className="store-auth-container grid gap-6">
        <div className="space-y-2 text-center">
          <h1 className="text-xl font-medium">{title}</h1>
          <p className="text-center text-sm text-muted-foreground">{description}</p>
        </div>

        {children}
      </div>
    </div>
  )
}

export default AuthLayout

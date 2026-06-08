import type { Auth } from '@/types/auth'

declare module 'react' {
  // eslint-disable-next-line unused-imports/no-unused-vars
  interface InputHTMLAttributes<T> {
    passwordrules?: string
  }
}

declare module '@inertiajs/core' {
  export interface InertiaConfig {
    sharedPageProps: {
      name: string
      url: string
      auth: Auth
      sidebarOpen: boolean
      [key: string]: unknown
    }
  }
}

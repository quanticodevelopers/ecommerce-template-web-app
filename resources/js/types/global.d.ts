import type { Auth } from '@/types/auth'
import type { SiteSettings } from '@/types/site-settings'

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
      site: SiteSettings
      sidebarOpen: boolean
      [key: string]: unknown
    }
  }
}

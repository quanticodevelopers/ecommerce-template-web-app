import { createInertiaApp } from '@inertiajs/react'
import { Toaster } from '@/components/ui/sonner'
import { TooltipProvider } from '@/components/ui/tooltip'
import { initializeTheme } from '@/hooks/use-appearance'
import AdminAuthLayout from '@/layouts/admin/auth-layout'
import AdminLayout from '@/layouts/admin/layout'
import AdminSettingsLayout from '@/layouts/admin/settings-layout'
import StoreAccountLayout from '@/layouts/store/account-layout'
import StoreAuthLayout from '@/layouts/store/auth-layout'
import StoreLayout from '@/layouts/store/layout'

const appName = import.meta.env.VITE_APP_NAME || 'Laravel'

createInertiaApp({
  title: (title) => (title ? `${title} - ${appName}` : appName),
  layout: (name) => {
    switch (true) {
      case name.startsWith('store/auth/'):
        return [StoreLayout, StoreAuthLayout]
      case name.startsWith('store/account/'):
        return [StoreLayout, StoreAccountLayout]
      case name.startsWith('store/'):
        return StoreLayout
      case name.startsWith('admin/auth/'):
        return AdminAuthLayout
      case name.startsWith('admin/settings/'):
        return [AdminLayout, AdminSettingsLayout]
      case name.startsWith('admin/'):
        return AdminLayout
      default:
        return null
    }
  },
  strictMode: true,
  withApp(app) {
    return (
      <TooltipProvider delayDuration={0}>
        {app}
        <Toaster richColors />
      </TooltipProvider>
    )
  },
  progress: {
    color: '#4B5563',
  },
})

// This will set light / dark mode on load...
initializeTheme()

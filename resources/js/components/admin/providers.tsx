import type { PropsWithChildren } from 'react'
import { Toaster } from '@/components/admin/ui/sonner'
import { TooltipProvider } from '@/components/admin/ui/tooltip'

export default function AdminProviders({ children }: PropsWithChildren) {
  return (
    <TooltipProvider delayDuration={0}>
      {children}
      <Toaster richColors />
    </TooltipProvider>
  )
}

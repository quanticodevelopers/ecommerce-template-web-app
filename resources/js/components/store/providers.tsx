import type { PropsWithChildren } from 'react'
import { Toaster } from '@/components/store/ui/sonner'
import { TooltipProvider } from '@/components/store/ui/tooltip'

export default function StoreProviders({ children }: PropsWithChildren) {
  return (
    <TooltipProvider delayDuration={0}>
      {children}
      <Toaster richColors />
    </TooltipProvider>
  )
}

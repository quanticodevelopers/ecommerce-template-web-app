import { router } from '@inertiajs/react'
import { useCallback } from 'react'
import { useMobileNavigation } from '@/hooks/use-mobile-navigation'
import type { CleanupFn } from '@/hooks/use-mobile-navigation'

export function useLogoutCleanup(): CleanupFn {
  const cleanupNavigation = useMobileNavigation()

  return useCallback(() => {
    cleanupNavigation()
    router.flushAll()
  }, [cleanupNavigation])
}

import { useCallback } from 'react'

export type GetInitialsFn = (name: string, last_name: string) => string

export function useInitials(): GetInitialsFn {
  return useCallback((name: string, last_name: string): string => {
    const firstInitial = name.charAt(0)
    const lastInitial = last_name.charAt(0)

    return `${firstInitial}${lastInitial}`.toUpperCase()
  }, [])
}

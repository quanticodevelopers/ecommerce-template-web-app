import { usePage } from '@inertiajs/react'
import type { Administrator } from '@/types/entities'

export function useAuthenticatedAdministrator(): Administrator {
  const user = usePage<{ auth: { user: Administrator | null } }>().props.auth.user

  if (!user) {
    throw new Error('An authenticated administrator is required for this page.')
  }

  return user
}

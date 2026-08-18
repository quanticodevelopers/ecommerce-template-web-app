import { usePage } from '@inertiajs/react'
import type { User } from '@/types'

export function useAuthenticatedUser(): User {
  const user = usePage().props.auth.user

  if (!user) {
    throw new Error('An authenticated user is required for this page.')
  }

  return user
}

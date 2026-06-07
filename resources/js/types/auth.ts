import type { UserDocumentTypeOption, UserRoleOption } from '@/types/enums'

export type User = {
  id: number
  document_type: UserDocumentTypeOption
  document_number: string
  name: string
  last_name: string
  email: string
  phone: string
  role: UserRoleOption
  avatar?: string
  email_verified_at: string | null
  created_at: string | null
  updated_at: string | null
  [key: string]: unknown
}

export type Auth = {
  user: User
}

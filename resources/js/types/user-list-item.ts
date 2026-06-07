import type { UserDocumentTypeOption, UserRoleOption } from './enums'

export type UserListItem = {
  id: string
  document_type: UserDocumentTypeOption
  document_number: string
  name: string
  last_name: string
  email: string
  phone: string
  role: UserRoleOption
  is_active: boolean
  created_at: string | null
}

export type CreatedUserCredentials = {
  name: string
  email: string
  password: string
}

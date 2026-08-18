import type { UserDocumentTypeOption, UserRoleOption } from '@/types/enums'

export type User = {
  id: string
  document_type: UserDocumentTypeOption
  document_number: string
  name: string
  last_name: string
  email: string
  phone: string
  role: UserRoleOption
  created_at: string | null
}

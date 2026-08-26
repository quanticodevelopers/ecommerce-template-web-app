import type { AdministratorRoleOption, UserDocumentTypeOption } from '@/types/enums'

type AuthenticatedIdentity = {
  id: string
  name: string
  last_name: string
  email: string
  phone: string
  created_at: string | null
}

export type Customer = AuthenticatedIdentity & {
  kind: 'customer'
  document_type: UserDocumentTypeOption
  document_number: string
}

export type Administrator = AuthenticatedIdentity & {
  kind: 'administrator'
  role: AdministratorRoleOption
}

export type User = Customer | Administrator

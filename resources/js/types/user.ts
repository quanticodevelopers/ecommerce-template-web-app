import type { AdministratorRoleOption, UserDocumentTypeOption } from '@/types/enums'

type AuthenticatedIdentity = {
  id: string
  document_type: UserDocumentTypeOption
  document_number: string
  name: string
  last_name: string
  email: string
  phone: string
  created_at: string | null
}

export type Customer = AuthenticatedIdentity & {
  kind: 'customer'
}

export type Administrator = AuthenticatedIdentity & {
  kind: 'administrator'
  role: AdministratorRoleOption
}

export type User = Customer | Administrator

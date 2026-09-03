import type { AuthenticatedIdentity } from '@/types/identity'

export type UserDocumentTypeValue = 'dni' | 'ce' | 'pasaporte'

export type UserDocumentTypeOption = {
  label: string
  value: UserDocumentTypeValue
}

export type Customer = AuthenticatedIdentity & {
  kind: 'customer'
  document_type: UserDocumentTypeOption
  document_number: string
}

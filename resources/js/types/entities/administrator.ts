import type { AuthenticatedIdentity } from '@/types/identity'

export type AdministratorRoleValue = 'admin' | 'super_admin'

export type AdministratorRoleOption = {
  label: string
  value: AdministratorRoleValue
}

export type Administrator = AuthenticatedIdentity & {
  kind: 'administrator'
  role: AdministratorRoleOption
}

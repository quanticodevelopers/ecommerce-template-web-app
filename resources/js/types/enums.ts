export type UserDocumentTypeValue = 'dni' | 'ce' | 'pasaporte'
export type AdministratorRoleValue = 'admin' | 'super_admin'

export type EnumOption<TValue extends string> = {
  label: string
  value: TValue
}

export type UserDocumentTypeOption = EnumOption<UserDocumentTypeValue>
export type AdministratorRoleOption = EnumOption<AdministratorRoleValue>

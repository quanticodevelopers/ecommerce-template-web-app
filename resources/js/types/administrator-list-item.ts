import type { Administrator } from './user'

export type AdministratorListItem = Administrator

export type CreatedAdministratorCredentials = {
  name: string
  email: string
  password: string
}

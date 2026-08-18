import type { User } from './user'

export type UserListItem = User

export type CreatedUserCredentials = {
  name: string
  email: string
  password: string
}

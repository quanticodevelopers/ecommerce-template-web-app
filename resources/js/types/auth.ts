export type User = {
  id: number
  document_type: 'dni' | 'ce' | 'pasaporte'
  document_number: string
  name: string
  last_name: string
  phone: string
  email: string
  avatar?: string
  email_verified_at: string | null
  created_at: string
  updated_at: string
  [key: string]: unknown
}

export type Auth = {
  user: User
}

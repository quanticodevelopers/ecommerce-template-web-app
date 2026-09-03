import type { Administrator, Customer } from '@/types/entities'

export type Auth = {
  user: Administrator | Customer | null
}

export type CategoryParent = {
  id: string
  name: string
}

export type CategoryListItem = {
  id: string
  name: string
  slug: string
  code: string
  parent: CategoryParent | null
  short_description: string | null
  is_active: boolean
  children_count: number
  created_at: string | null
}

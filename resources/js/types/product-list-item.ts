export type ProductFlag = 'featured' | 'new'

export type ProductListItem = {
  id: string
  sku: string
  barcode: string
  name: string
  brand: {
    id: string
    name: string
  }
  category: {
    id: string
    name: string
  }
  base_price: string | null
  sale_price: string
  flag: ProductFlag | null
  published_at: string | null
  thumbnail: {
    url: string
    alt: string
  } | null
}

export type PaginatedProducts = {
  data: ProductListItem[]
  links: {
    first: string
    last: string
    prev: string | null
    next: string | null
  }
  meta: {
    current_page: number
    from: number | null
    last_page: number
    per_page: number
    to: number | null
    total: number
  }
}

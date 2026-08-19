export type ProductDetailImage = {
  id: string
  url: string
  thumbnail_url: string
  alt: string
  position: number
}

export type ProductDetail = {
  id: string
  sku: string
  barcode: string
  name: string
  slug: string
  short_description: string | null
  description: string | null
  base_price: string | null
  sale_price: string
  flag: {
    value: 'featured' | 'new'
    label: string
  } | null
  brand: {
    id: string
    name: string
  }
  category: {
    id: string
    name: string
  }
  published_at: string | null
  created_at: string
  updated_at: string
  images: ProductDetailImage[]
}

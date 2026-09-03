export type ProductFlagValue = 'featured' | 'new'

export type ProductFlag = {
  value: ProductFlagValue
  label: string
}

export type ProductImage = {
  id: string
  url: string
  thumbnail_url: string
  alt: string
  position: number
}

export type Product = {
  id: string
  sku: string
  barcode: string
  name: string
  slug: string
  short_description: string | null
  description: string | null
  base_price: string | null
  sale_price: string
  flag: ProductFlag | null
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
  thumbnail: {
    url: string
    alt: string
  } | null
  images: ProductImage[]
}

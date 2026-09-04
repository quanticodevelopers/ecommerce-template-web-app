export type ProductFlagValue = 'featured' | 'new'

export type ProductFlag = {
  value: ProductFlagValue
  label: string
}

export type ProductImage = {
  id: string
  url: string
  variants: Record<string, string>
  listing_variant: string
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
  primary_image?: ProductImage | null
  images?: ProductImage[]
}

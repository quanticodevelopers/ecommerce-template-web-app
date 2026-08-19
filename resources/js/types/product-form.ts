import type { ProductFlag } from '@/types/product-list-item'

export type ProductCatalogOption = {
  id: string
  name: string
}

export type ProductFormImage = {
  id: string
  url: string
  alt: string
}

export type ProductImageSlot = {
  id: string | null
  file: File | null
}

export type EditableProduct = {
  id: string
  brand_id: string
  category_id: string
  sku: string
  barcode: string
  name: string
  short_description: string | null
  description: string | null
  base_price: string | null
  sale_price: string
  flag: ProductFlag | null
  is_draft: boolean
  images: ProductFormImage[]
}

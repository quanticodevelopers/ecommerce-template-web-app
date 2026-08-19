import ProductFormPage from '@/pages/admin/products/components/product-form-page'
import type { ProductFormPageProps } from '@/pages/admin/products/components/product-form-page'
import { index as productsIndex } from '@/routes/admin/products'
import type { EditableProduct } from '@/types'

type ProductsEditProps = ProductFormPageProps & {
  product: EditableProduct
}

export default function ProductsEdit(props: ProductsEditProps) {
  return <ProductFormPage {...props} />
}

ProductsEdit.layout = {
  breadcrumbs: [
    { title: 'Productos', href: productsIndex() },
    { title: 'Editar producto', href: '#' },
  ],
}

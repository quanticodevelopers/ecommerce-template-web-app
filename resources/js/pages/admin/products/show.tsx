import { ArrowLeft01Icon, Edit02Icon, Package01Icon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Head, Link } from '@inertiajs/react'
import Heading from '@/components/heading'
import { Button } from '@/components/ui/button'
import ProductDescription from '@/pages/admin/products/components/product-description'
import ProductDetailOverview from '@/pages/admin/products/components/product-detail-overview'
import ProductImageGallery from '@/pages/admin/products/components/product-image-gallery'
import { edit as productsEdit, index as productsIndex, show as productsShow } from '@/routes/admin/products'
import type { ProductDetail } from '@/types'

type ProductsShowProps = {
  product: ProductDetail
}

export default function ProductsShow({ product }: ProductsShowProps) {
  return (
    <>
      <Head title={product.name} />

      <div className="flex w-full max-w-7xl flex-col gap-8 p-4 lg:p-8">
        <div className="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
          <Heading
            title={product.name}
            description={`SKU ${product.sku} · ${product.brand.name}`}
            badgeIcon={Package01Icon}
            badgeLabel="Detalle de producto"
          />

          <div className="flex items-center gap-2">
            <Button
              asChild
              variant="outline"
            >
              <Link href={productsIndex()}>
                <HugeiconsIcon
                  icon={ArrowLeft01Icon}
                  strokeWidth={1.5}
                />
                Volver
              </Link>
            </Button>
            <Button asChild>
              <Link
                href={productsEdit(product.id)}
                prefetch
              >
                <HugeiconsIcon
                  icon={Edit02Icon}
                  strokeWidth={1.5}
                />
                Editar producto
              </Link>
            </Button>
          </div>
        </div>

        <div className="grid items-start gap-5 xl:grid-cols-[minmax(0,1fr)_minmax(22rem,0.72fr)]">
          <div className="flex min-w-0 flex-col gap-5">
            <ProductImageGallery
              images={product.images}
              productName={product.name}
            />
            <ProductDescription product={product} />
          </div>
          <ProductDetailOverview product={product} />
        </div>
      </div>
    </>
  )
}

ProductsShow.layout = ({ product }: ProductsShowProps) => ({
  breadcrumbs: [
    { title: 'Productos', href: productsIndex() },
    { title: product.name, href: productsShow(product.id) },
  ],
})

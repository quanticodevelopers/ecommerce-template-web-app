import { Edit02Icon, PackageAddIcon, SaveIcon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Head, Link, useForm } from '@inertiajs/react'
import type { SubmitEvent } from 'react'
import { store as productsStore, update as productsUpdate } from '@/actions/App/Http/Controllers/Admin/ProductController'
import Heading from '@/components/admin/heading'
import InputError from '@/components/admin/input-error'
import { Button } from '@/components/admin/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/admin/ui/card'
import { Checkbox } from '@/components/admin/ui/checkbox'
import { Input } from '@/components/admin/ui/input'
import { Label } from '@/components/admin/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/admin/ui/select'
import { Spinner } from '@/components/admin/ui/spinner'
import { Textarea } from '@/components/admin/ui/textarea'
import { cn } from '@/lib/utils'
import ProductImageUploader from '@/pages/admin/products/components/product-image-uploader'
import ProductRichTextEditor from '@/pages/admin/products/components/product-rich-text-editor'
import { index as productsIndex } from '@/routes/admin/products'
import type { EditableProduct, ProductCatalogOption, ProductImageSlot } from '@/types'

export type ProductFormPageProps = {
  brands: ProductCatalogOption[]
  categories: ProductCatalogOption[]
  product?: EditableProduct
}

type ProductFlag = '' | 'featured' | 'new'

type ProductFormData = {
  _method?: 'patch'
  name: string
  sku: string
  barcode: string
  brand_id: string
  category_id: string
  short_description: string
  description: string
  base_price: string
  sale_price: string
  flag: ProductFlag
  is_draft: boolean
  images: ProductImageSlot[]
}

const flags: Array<{ description: string; label: string; value: ProductFlag }> = [
  { value: '', label: 'Ninguna', description: 'No mostrar una etiqueta especial.' },
  { value: 'featured', label: 'Destacado', description: 'Resalta el producto en bloques promocionales.' },
  { value: 'new', label: 'Nuevo', description: 'Identifica el producto como una novedad.' },
]

export default function ProductFormPage({ brands, categories, product }: ProductFormPageProps) {
  const isEditing = product !== undefined
  const { data, setData, post, processing, progress, errors } = useForm<ProductFormData>({
    ...(isEditing ? { _method: 'patch' as const } : {}),
    name: product?.name ?? '',
    sku: product?.sku ?? '',
    barcode: product?.barcode ?? '',
    brand_id: product?.brand_id ?? '',
    category_id: product?.category_id ?? '',
    short_description: product?.short_description ?? '',
    description: product?.description ?? '',
    base_price: product?.base_price ?? '',
    sale_price: product?.sale_price ?? '',
    flag: product?.flag ?? '',
    is_draft: product?.is_draft ?? false,
    images: product?.images.map((image) => ({ id: image.id, file: null })) ?? [],
  })
  const imageError = errors.images ?? Object.entries(errors).find(([field]) => field.startsWith('images.'))?.[1]

  function submit(event: SubmitEvent<HTMLFormElement>): void {
    event.preventDefault()
    post(isEditing ? productsUpdate(product.id).url : productsStore().url, {
      forceFormData: true,
      preserveScroll: true,
    })
  }

  return (
    <>
      <Head title={isEditing ? `Editar ${product.name}` : 'Nuevo producto'} />

      <form
        className="flex flex-col gap-8 p-4 lg:p-8"
        onSubmit={submit}
      >
        <div className="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
          <Heading
            title={isEditing ? 'Editar producto' : 'Nuevo producto'}
            description={isEditing ? 'Actualiza la información y las imágenes del producto.' : 'Completa la información para agregar un producto al catálogo.'}
            badgeIcon={isEditing ? Edit02Icon : PackageAddIcon}
            badgeLabel="Catálogo"
          />
          <Button
            asChild
            variant="outline"
          >
            <Link href={productsIndex()}>Cancelar</Link>
          </Button>
        </div>

        <div className="grid items-start gap-6 2xl:grid-cols-[minmax(0,1fr)_minmax(32rem,0.9fr)]">
          <div className="grid min-w-0 gap-6">
            <Card>
              <CardHeader>
                <CardTitle>Información del producto</CardTitle>
                <CardDescription>Datos comerciales y clasificación dentro del catálogo.</CardDescription>
              </CardHeader>
              <CardContent className="grid gap-5">
                <div className="grid gap-2">
                  <Label htmlFor="name">
                    Nombre <span className="text-destructive">*</span>
                  </Label>
                  <Input
                    id="name"
                    maxLength={128}
                    onChange={(event) => setData('name', event.target.value)}
                    placeholder="Nombre del producto"
                    required
                    value={data.name}
                  />
                  <InputError message={errors.name} />
                </div>

                <div className="grid gap-5 sm:grid-cols-2">
                  <div className="grid gap-2">
                    <Label htmlFor="sku">
                      SKU <span className="text-destructive">*</span>
                    </Label>
                    <Input
                      id="sku"
                      maxLength={24}
                      onChange={(event) => setData('sku', event.target.value)}
                      placeholder="Ej. PROD-001"
                      required
                      value={data.sku}
                    />
                    <InputError message={errors.sku} />
                  </div>
                  <div className="grid gap-2">
                    <Label htmlFor="barcode">
                      Código de barras <span className="text-destructive">*</span>
                    </Label>
                    <Input
                      id="barcode"
                      inputMode="numeric"
                      maxLength={13}
                      onChange={(event) => setData('barcode', event.target.value.replace(/\D/g, ''))}
                      pattern="[0-9]{13}"
                      placeholder="13 dígitos"
                      required
                      value={data.barcode}
                    />
                    <InputError message={errors.barcode} />
                  </div>
                </div>

                <div className="grid gap-5 sm:grid-cols-2">
                  <div className="grid gap-2">
                    <Label htmlFor="brand_id">
                      Marca <span className="text-destructive">*</span>
                    </Label>
                    <Select
                      value={data.brand_id}
                      onValueChange={(value) => setData('brand_id', value)}
                    >
                      <SelectTrigger
                        id="brand_id"
                        className="w-full"
                        aria-invalid={errors.brand_id !== undefined}
                      >
                        <SelectValue placeholder="Selecciona una marca" />
                      </SelectTrigger>
                      <SelectContent position="popper">
                        {brands.map((brand) => (
                          <SelectItem
                            key={brand.id}
                            value={brand.id}
                          >
                            {brand.name}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                    <InputError message={errors.brand_id} />
                  </div>
                  <div className="grid gap-2">
                    <Label htmlFor="category_id">
                      Categoría <span className="text-destructive">*</span>
                    </Label>
                    <Select
                      value={data.category_id}
                      onValueChange={(value) => setData('category_id', value)}
                    >
                      <SelectTrigger
                        id="category_id"
                        className="w-full"
                        aria-invalid={errors.category_id !== undefined}
                      >
                        <SelectValue placeholder="Selecciona una categoría" />
                      </SelectTrigger>
                      <SelectContent position="popper">
                        {categories.map((category) => (
                          <SelectItem
                            key={category.id}
                            value={category.id}
                          >
                            {category.name}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                    <InputError message={errors.category_id} />
                  </div>
                </div>

                <div className="grid gap-5 sm:grid-cols-2">
                  <div className="grid gap-2">
                    <Label htmlFor="sale_price">
                      Precio de venta <span className="text-destructive">*</span>
                    </Label>
                    <Input
                      id="sale_price"
                      inputMode="decimal"
                      min="0"
                      onChange={(event) => setData('sale_price', event.target.value)}
                      placeholder="0.00"
                      required
                      step="0.01"
                      type="number"
                      value={data.sale_price}
                    />
                    <InputError message={errors.sale_price} />
                  </div>
                  <div className="grid gap-2">
                    <Label htmlFor="base_price">Precio regular</Label>
                    <Input
                      id="base_price"
                      inputMode="decimal"
                      min="0"
                      onChange={(event) => setData('base_price', event.target.value)}
                      placeholder="0.00"
                      step="0.01"
                      type="number"
                      value={data.base_price}
                    />
                    <InputError message={errors.base_price} />
                  </div>
                </div>

                <div className="grid gap-2">
                  <Label htmlFor="short_description">Descripción corta</Label>
                  <Textarea
                    id="short_description"
                    maxLength={255}
                    onChange={(event) => setData('short_description', event.target.value)}
                    placeholder="Resumen breve del producto (máximo 255 caracteres)"
                    value={data.short_description}
                  />
                  <InputError message={errors.short_description} />
                </div>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Descripción</CardTitle>
                <CardDescription>Agrega contenido con formato para presentar el producto.</CardDescription>
              </CardHeader>
              <CardContent>
                <ProductRichTextEditor
                  value={data.description}
                  onChange={(value) => setData('description', value)}
                />
                <InputError message={errors.description} />
              </CardContent>
            </Card>
          </div>

          <div className="grid min-w-0 gap-6 2xl:sticky 2xl:top-6">
            <Card>
              <CardContent>
                <ProductImageUploader
                  error={imageError}
                  initialImages={product?.images}
                  onChange={(images) => setData('images', images)}
                />
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle>Etiqueta</CardTitle>
                <CardDescription>Opcional. Permite resaltar el producto donde corresponda.</CardDescription>
              </CardHeader>
              <CardContent className="grid gap-3 sm:grid-cols-3 xl:grid-cols-1 2xl:grid-cols-3">
                {flags.map((flag) => (
                  <label
                    key={flag.value}
                    className={cn(
                      'hover:bg-muted/40 flex cursor-pointer gap-3 rounded-lg border p-4 transition-colors',
                      data.flag === flag.value && 'border-foreground bg-muted/40',
                    )}
                  >
                    <input
                      checked={data.flag === flag.value}
                      className="accent-foreground mt-1"
                      name="flag"
                      onChange={() => setData('flag', flag.value)}
                      type="radio"
                      value={flag.value}
                    />
                    <span>
                      <span className="block font-medium">{flag.label}</span>
                      <span className="text-muted-foreground mt-1 block text-xs">{flag.description}</span>
                    </span>
                  </label>
                ))}
                <InputError
                  className="sm:col-span-3 xl:col-span-1 2xl:col-span-3"
                  message={errors.flag}
                />
              </CardContent>
            </Card>

            <Card>
              <CardContent className="gap-5">
                <label className="flex cursor-pointer items-start gap-3">
                  <Checkbox
                    checked={data.is_draft}
                    id="is_draft"
                    onCheckedChange={(checked) => setData('is_draft', checked === true)}
                  />
                  <span>
                    <span className="block font-medium">Guardar como borrador</span>
                    <span className="text-muted-foreground mt-1 block text-sm">El producto no será visible en la tienda.</span>
                  </span>
                </label>
                <InputError message={errors.is_draft} />

                {progress && (
                  <div className="grid gap-2">
                    <div className="text-muted-foreground flex justify-between text-xs">
                      <span>Subiendo imágenes</span>
                      <span>{progress.percentage}%</span>
                    </div>
                    <div className="bg-muted h-1.5 overflow-hidden rounded-full">
                      <div
                        className="bg-primary h-full transition-all"
                        style={{ width: `${progress.percentage}%` }}
                      />
                    </div>
                  </div>
                )}

                <Button
                  className="w-full"
                  disabled={processing}
                  type="submit"
                >
                  {processing ? (
                    <Spinner />
                  ) : (
                    <HugeiconsIcon
                      icon={SaveIcon}
                      strokeWidth={1.5}
                    />
                  )}
                  {isEditing ? 'Guardar cambios' : 'Guardar producto'}
                </Button>
              </CardContent>
            </Card>
          </div>
        </div>
      </form>
    </>
  )
}

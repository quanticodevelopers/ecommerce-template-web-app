import { BarcodeIcon, Calendar03Icon, Clock01Icon, Folder01Icon, Link01Icon, Money03Icon, Package01Icon, Tag01Icon } from '@hugeicons/core-free-icons'
import type { IconSvgElement } from '@hugeicons/react'
import { HugeiconsIcon } from '@hugeicons/react'
import { Badge } from '@/components/admin/ui/badge'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/admin/ui/card'
import { Separator } from '@/components/admin/ui/separator'
import type { ProductDetail } from '@/types'

type ProductDetailOverviewProps = {
  product: ProductDetail
}

type DetailRowProps = {
  icon: IconSvgElement
  label: string
  value: string
  monospaced?: boolean
}

const currencyFormatter = new Intl.NumberFormat('es-PE', {
  style: 'currency',
  currency: 'PEN',
  minimumFractionDigits: 2,
})

const dateTimeFormatter = new Intl.DateTimeFormat('es-PE', {
  dateStyle: 'long',
  timeStyle: 'short',
  timeZone: 'America/Lima',
})

function formatPrice(price: string): string {
  return currencyFormatter.format(Number(price))
}

function formatDateTime(date: string): string {
  return dateTimeFormatter.format(new Date(date))
}

function DetailRow({ icon, label, value, monospaced = false }: DetailRowProps) {
  return (
    <div className="flex items-start gap-3">
      <span className="bg-muted text-muted-foreground mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-lg">
        <HugeiconsIcon
          icon={icon}
          className="size-4"
          strokeWidth={1.5}
        />
      </span>
      <div className="min-w-0 space-y-0.5">
        <p className="text-muted-foreground text-xs">{label}</p>
        <p className={monospaced ? 'break-all font-mono text-xs font-medium' : 'break-words font-medium'}>{value}</p>
      </div>
    </div>
  )
}

export default function ProductDetailOverview({ product }: ProductDetailOverviewProps) {
  const isPublished = product.published_at !== null

  return (
    <div className="flex flex-col gap-5">
      <Card className="shadow-none">
        <CardHeader className="border-b">
          <div className="flex flex-wrap items-center gap-2">
            <Badge variant={isPublished ? 'default' : 'secondary'}>{isPublished ? 'Publicado' : 'Borrador'}</Badge>
            {product.flag && <Badge variant="outline">{product.flag.label}</Badge>}
          </div>
          <CardTitle className="mt-3 text-2xl">{formatPrice(product.sale_price)}</CardTitle>
          <CardDescription>
            {product.base_price === null ? (
              'Sin precio regular registrado'
            ) : (
              <>
                Precio regular: <span className="line-through">{formatPrice(product.base_price)}</span>
              </>
            )}
          </CardDescription>
        </CardHeader>
        <CardContent className="grid gap-5 sm:grid-cols-2 xl:grid-cols-1">
          <DetailRow
            icon={Tag01Icon}
            label="Marca"
            value={product.brand.name}
          />
          <DetailRow
            icon={Folder01Icon}
            label="Categoría"
            value={product.category.name}
          />
          <DetailRow
            icon={Package01Icon}
            label="SKU"
            value={product.sku}
            monospaced
          />
          <DetailRow
            icon={BarcodeIcon}
            label="Código de barras"
            value={product.barcode}
            monospaced
          />
          <DetailRow
            icon={Link01Icon}
            label="Slug"
            value={product.slug}
            monospaced
          />
          <DetailRow
            icon={Money03Icon}
            label="Etiqueta"
            value={product.flag?.label ?? 'Sin etiqueta'}
          />
        </CardContent>
      </Card>

      <Card className="shadow-none">
        <CardHeader>
          <CardTitle>Registro y publicación</CardTitle>
          <CardDescription>Historial principal del producto en el catálogo.</CardDescription>
        </CardHeader>
        <CardContent className="gap-4">
          <DetailRow
            icon={Calendar03Icon}
            label="Creado"
            value={formatDateTime(product.created_at)}
          />
          <Separator />
          <DetailRow
            icon={Clock01Icon}
            label="Última actualización"
            value={formatDateTime(product.updated_at)}
          />
          <Separator />
          <DetailRow
            icon={Calendar03Icon}
            label="Publicación"
            value={product.published_at === null ? 'Aún no publicado' : formatDateTime(product.published_at)}
          />
          <Separator />
          <DetailRow
            icon={Package01Icon}
            label="ID del producto"
            value={product.id}
            monospaced
          />
        </CardContent>
      </Card>
    </div>
  )
}

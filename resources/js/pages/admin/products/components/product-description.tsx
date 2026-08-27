import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/admin/ui/card'
import type { ProductDetail } from '@/types'

type ProductDescriptionProps = {
  product: ProductDetail
}

export default function ProductDescription({ product }: ProductDescriptionProps) {
  return (
    <Card className="shadow-none">
      <CardHeader className="border-b">
        <CardTitle>Descripción del producto</CardTitle>
        <CardDescription>Contenido que acompaña al producto dentro del catálogo.</CardDescription>
      </CardHeader>
      <CardContent className="gap-6">
        <section className="space-y-2">
          <h2 className="text-sm font-medium">Descripción corta</h2>
          <p className="text-muted-foreground leading-6">{product.short_description ?? 'No se registró una descripción corta.'}</p>
        </section>

        <section className="space-y-3 border-t pt-6">
          <h2 className="text-sm font-medium">Descripción completa</h2>
          {product.description ? (
            <div
              className="text-muted-foreground [&_a]:text-primary [&_h2]:text-foreground [&_h3]:text-foreground [&_h4]:text-foreground [&_strong]:text-foreground max-w-none leading-7 [&_a]:underline [&_blockquote]:border-l-2 [&_blockquote]:pl-4 [&_h2]:mb-3 [&_h2]:mt-6 [&_h2]:text-xl [&_h2]:font-semibold [&_h3]:mb-2 [&_h3]:mt-5 [&_h3]:text-lg [&_h3]:font-semibold [&_h4]:mb-2 [&_h4]:mt-4 [&_h4]:font-semibold [&_ol]:list-decimal [&_ol]:pl-6 [&_p+p]:mt-3 [&_strong]:font-semibold [&_ul]:list-disc [&_ul]:pl-6"
              dangerouslySetInnerHTML={{ __html: product.description }}
            />
          ) : (
            <p className="text-muted-foreground">No se registró una descripción completa.</p>
          )}
        </section>
      </CardContent>
    </Card>
  )
}

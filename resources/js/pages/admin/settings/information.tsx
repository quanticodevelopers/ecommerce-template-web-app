import { Delete02Icon, Image01Icon, RefreshIcon, SaveIcon, Upload04Icon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Form, Head } from '@inertiajs/react'
import type { ChangeEvent } from 'react'
import { useEffect, useRef, useState } from 'react'
import InformationController from '@/actions/App/Http/Controllers/Admin/Settings/InformationController'
import Heading from '@/components/admin/heading'
import InputError from '@/components/admin/input-error'
import { Button } from '@/components/admin/ui/button'
import { Input } from '@/components/admin/ui/input'
import { InputGroup, InputGroupAddon, InputGroupInput, InputGroupText } from '@/components/admin/ui/input-group'
import { Label } from '@/components/admin/ui/label'
import { Spinner } from '@/components/admin/ui/spinner'
import { Textarea } from '@/components/admin/ui/textarea'
import { edit as editInformation } from '@/routes/admin/information'
import type { SiteSettings } from '@/types'

interface InformationProps {
  settings: SiteSettings
}

const Information = ({ settings }: InformationProps) => {
  const fileInputRef = useRef<HTMLInputElement>(null)
  const previewUrlRef = useRef<string | null>(null)
  const [logoPreview, setLogoPreview] = useState<string | null>(settings.logo_url)
  const [removeLogo, setRemoveLogo] = useState(false)

  useEffect(() => {
    return () => {
      if (previewUrlRef.current) {
        URL.revokeObjectURL(previewUrlRef.current)
      }
    }
  }, [])

  function clearTemporaryPreview(): void {
    if (previewUrlRef.current) {
      URL.revokeObjectURL(previewUrlRef.current)
      previewUrlRef.current = null
    }
  }

  function handleLogoChange(event: ChangeEvent<HTMLInputElement>): void {
    const file = event.target.files?.[0]

    if (!file) {
      return
    }

    clearTemporaryPreview()

    const objectUrl = URL.createObjectURL(file)
    previewUrlRef.current = objectUrl

    setRemoveLogo(false)
    setLogoPreview(objectUrl)
  }

  function handleRemoveLogo(): void {
    clearTemporaryPreview()
    setLogoPreview(null)
    setRemoveLogo(true)

    if (fileInputRef.current) {
      fileInputRef.current.value = ''
    }
  }

  function handleFormReset(): void {
    clearTemporaryPreview()
    setLogoPreview(settings.logo_url)
    setRemoveLogo(false)

    if (fileInputRef.current) {
      fileInputRef.current.value = ''
    }
  }

  return (
    <>
      <Head title="Configuración de información del sitio" />

      <div className="space-y-6">
        <Heading
          variant="small"
          title="Configuración de información del sitio"
          description="Administra la información pública del sitio. Está información se muestra al público y es importante para el SEO."
        />

        <Form
          {...InformationController.update.form()}
          options={{
            preserveScroll: true,
          }}
          className="space-y-6"
          onReset={handleFormReset}
        >
          {({ errors, processing, progress }) => (
            <>
              <section className="bg-background/60 flex items-start gap-4">
                <div className="h-22 w-22 flex shrink-0 items-center justify-center overflow-hidden rounded-lg">
                  {logoPreview ? (
                    <img
                      alt="Logo del sitio"
                      className="h-full w-full object-contain"
                      src={logoPreview}
                    />
                  ) : (
                    <HugeiconsIcon
                      icon={Image01Icon}
                      className="text-muted-foreground size-8"
                      strokeWidth={1.5}
                    />
                  )}
                </div>

                <div className="min-w-0 flex-1 space-y-3">
                  <div className="grid gap-1">
                    <Label className="text-sm font-medium">Logo del sitio</Label>
                    <p className="text-muted-foreground text-sm">PNG o SVG, minimo 512 x 512 px.</p>
                  </div>

                  <div className="flex flex-wrap gap-2">
                    <Button
                      type="button"
                      variant="outline"
                      size="xs"
                      onClick={() => fileInputRef.current?.click()}
                    >
                      <HugeiconsIcon
                        icon={Upload04Icon}
                        strokeWidth={1.5}
                      />
                      Subir nuevo
                    </Button>

                    <Button
                      type="button"
                      variant={removeLogo ? 'secondary' : 'outline'}
                      size="xs"
                      disabled={!settings.logo_url && !logoPreview}
                      onClick={handleRemoveLogo}
                    >
                      <HugeiconsIcon
                        icon={Delete02Icon}
                        strokeWidth={1.5}
                      />
                      Quitar
                    </Button>

                    <input
                      ref={fileInputRef}
                      accept=".png,.svg,image/png,image/svg+xml"
                      className="sr-only"
                      name="logo"
                      type="file"
                      onChange={handleLogoChange}
                    />

                    <input
                      name="remove_logo"
                      type="hidden"
                      value={removeLogo ? '1' : '0'}
                    />
                  </div>

                  <InputError message={errors.logo} />
                </div>
              </section>

              {progress && (
                <div className="grid gap-2">
                  <div className="text-muted-foreground flex items-center justify-between text-xs">
                    <span>Subiendo logo</span>
                    <span>{Math.round(progress.percentage ?? 0)}%</span>
                  </div>
                  <div className="bg-muted h-2 overflow-hidden rounded-full">
                    <div
                      className="bg-primary h-full rounded-full transition-all"
                      style={{ width: `${progress.percentage ?? 0}%` }}
                    />
                  </div>
                </div>
              )}

              <div className="grid items-start gap-4 xl:grid-cols-2">
                <div className="grid gap-2">
                  <Label htmlFor="site_name">Nombre del sitio</Label>

                  <Input
                    id="site_name"
                    defaultValue={settings.site_name}
                    maxLength={128}
                    name="site_name"
                    placeholder="Nombre del sitio"
                    required
                  />

                  <InputError message={errors.site_name} />
                </div>

                <div className="grid gap-2">
                  <Label htmlFor="footer_credit_name">Nombre en créditos del footer</Label>

                  <Input
                    id="footer_credit_name"
                    defaultValue={settings.footer_credit_name}
                    maxLength={128}
                    name="footer_credit_name"
                    placeholder="Nombre para créditos"
                    required
                  />

                  <InputError message={errors.footer_credit_name} />
                </div>
              </div>

              <div className="grid items-start gap-4 xl:grid-cols-2">
                <div className="grid gap-2">
                  <Label htmlFor="email">Correo electrónico</Label>

                  <Input
                    id="email"
                    type="email"
                    defaultValue={settings.email}
                    maxLength={128}
                    name="email"
                    placeholder="correo@ejemplo.com"
                    required
                  />

                  <InputError message={errors.email} />
                </div>

                <div className="grid gap-2">
                  <Label htmlFor="phone">Celular</Label>

                  <InputGroup>
                    <InputGroupAddon align="inline-start">
                      <InputGroupText>+51</InputGroupText>
                    </InputGroupAddon>
                    <InputGroupInput
                      id="phone"
                      className="rounded-none border-0 shadow-none focus-visible:ring-0"
                      defaultValue={settings.phone}
                      inputMode="numeric"
                      maxLength={9}
                      minLength={9}
                      name="phone"
                      pattern="[0-9]{9}"
                      placeholder="987654321"
                      required
                    />
                  </InputGroup>

                  <InputError message={errors.phone} />
                </div>
              </div>

              <div className="grid gap-2">
                <Label htmlFor="address">Dirección</Label>

                <Input
                  id="address"
                  defaultValue={settings.address}
                  maxLength={128}
                  name="address"
                  placeholder="Av. Principal 123"
                  required
                />

                <InputError message={errors.address} />
              </div>

              <div className="grid gap-2">
                <Label htmlFor="site_description">Descripción del sitio</Label>

                <Textarea
                  id="site_description"
                  defaultValue={settings.site_description}
                  maxLength={255}
                  name="site_description"
                  placeholder="Describe brevemente el sitio"
                  required
                />

                <InputError message={errors.site_description} />
              </div>

              <div className="grid gap-2">
                <Label htmlFor="site_keywords">Palabras clave</Label>

                <Input
                  id="site_keywords"
                  defaultValue={settings.site_keywords}
                  maxLength={255}
                  name="site_keywords"
                  placeholder="ecommerce, tienda, online"
                  required
                />

                <p className="text-muted-foreground text-xs">Se guardan separadas por comas.</p>
                <InputError message={errors.site_keywords} />
              </div>

              <div className="flex flex-col-reverse gap-3 border-t pt-6 sm:flex-row sm:justify-end">
                <Button
                  type="reset"
                  variant="outline"
                  disabled={processing}
                >
                  <HugeiconsIcon
                    icon={RefreshIcon}
                    strokeWidth={1.5}
                  />
                  Cancelar
                </Button>

                <Button
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
                  Guardar cambios
                </Button>
              </div>
            </>
          )}
        </Form>
      </div>
    </>
  )
}

Information.layout = {
  breadcrumbs: [
    {
      title: 'Informacion del sitio',
      href: editInformation(),
    },
  ],
}

export default Information

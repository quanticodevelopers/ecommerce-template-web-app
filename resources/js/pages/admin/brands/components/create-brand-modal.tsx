import { Add01Icon, Delete02Icon, Image01Icon, RefreshIcon, Upload04Icon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Form } from '@inertiajs/react'
import type { ChangeEvent } from 'react'
import { useEffect, useRef, useState } from 'react'
import { store as brandsStore } from '@/actions/App/Http/Controllers/Admin/BrandController'
import InputError from '@/components/input-error'
import { Button } from '@/components/ui/button'
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Spinner } from '@/components/ui/spinner'
import { Textarea } from '@/components/ui/textarea'

export default function CreateBrandModal() {
  const fileInputRef = useRef<HTMLInputElement>(null)
  const previewUrlRef = useRef<string | null>(null)
  const [isOpen, setIsOpen] = useState(false)
  const [logoPreview, setLogoPreview] = useState<string | null>(null)

  useEffect(() => {
    if (previewUrlRef.current) {
      URL.revokeObjectURL(previewUrlRef.current)
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
    setLogoPreview(objectUrl)
  }

  function resetLocalState(): void {
    clearTemporaryPreview()
    setLogoPreview(null)

    if (fileInputRef.current) {
      fileInputRef.current.value = ''
    }
  }

  function handleDialogOpenChange(open: boolean): void {
    setIsOpen(open)

    if (!open) {
      resetLocalState()
    }
  }

  return (
    <Dialog
      open={isOpen}
      onOpenChange={handleDialogOpenChange}
    >
      <DialogTrigger asChild>
        <Button type="button">
          <HugeiconsIcon
            icon={Add01Icon}
            className="size-4"
          />
          Nueva marca
        </Button>
      </DialogTrigger>

      <DialogContent className="max-h-[calc(100vh-2rem)] overflow-y-auto sm:max-w-2xl">
        <DialogHeader>
          <DialogTitle>Crear marca</DialogTitle>
          <DialogDescription>Completa los datos obligatorios para registrar una marca del catálogo.</DialogDescription>
        </DialogHeader>

        <Form
          {...brandsStore.form()}
          options={{
            preserveScroll: true,
          }}
          className="space-y-5"
          onSuccess={() => {
            resetLocalState()
            setIsOpen(false)
          }}
          resetOnSuccess
        >
          {({ errors, processing, progress, resetAndClearErrors }) => (
            <>
              <div className="grid gap-4">
                <div className="grid gap-2">
                  <Label htmlFor="name">Nombre</Label>
                  <Input
                    id="name"
                    name="name"
                    maxLength={128}
                    placeholder="Nombre de la marca"
                    required
                  />
                  <InputError message={errors.name} />
                </div>

                <div className="grid gap-2">
                  <Label>Logo</Label>

                  <div className="flex flex-col gap-4 rounded-lg border bg-background/60 p-4 sm:flex-row sm:items-center">
                    <div className="flex aspect-3/2 h-32 shrink-0 items-center justify-center overflow-hidden rounded-md border bg-muted/40 md:h-24">
                      {logoPreview ? (
                        <img
                          alt="Vista previa del logo"
                          className="h-full w-full object-contain p-1"
                          src={logoPreview}
                        />
                      ) : (
                        <HugeiconsIcon
                          icon={Image01Icon}
                          className="size-8 text-muted-foreground"
                        />
                      )}
                    </div>

                    <div className="min-w-0 flex-1 space-y-3">
                      <div className="grid gap-1">
                        <p className="text-sm font-medium text-foreground">Vista previa</p>
                        <p className="text-sm text-muted-foreground">Recomendado 600 x 400 px. JPG, JPEG, PNG, WebP o AVIF.</p>
                      </div>

                      <div className="flex flex-wrap gap-2">
                        <Button
                          type="button"
                          variant="outline"
                          size="sm"
                          onClick={() => fileInputRef.current?.click()}
                        >
                          <HugeiconsIcon
                            icon={Upload04Icon}
                            className="size-4"
                          />
                          Seleccionar logo
                        </Button>

                        <Button
                          type="button"
                          variant="outline"
                          size="sm"
                          disabled={!logoPreview}
                          onClick={resetLocalState}
                        >
                          <HugeiconsIcon
                            icon={Delete02Icon}
                            className="size-4"
                          />
                          Quitar selección
                        </Button>
                      </div>

                      <input
                        ref={fileInputRef}
                        accept=".jpg,.jpeg,.png,.webp,.avif,image/jpeg,image/png,image/webp,image/avif"
                        className="sr-only"
                        name="logo"
                        type="file"
                        onChange={handleLogoChange}
                      />

                      <InputError message={errors.logo} />
                    </div>
                  </div>
                </div>

                <div className="grid gap-2">
                  <Label htmlFor="short_description">Descripción breve</Label>
                  <Textarea
                    id="short_description"
                    name="short_description"
                    maxLength={128}
                    placeholder="Descripción breve de la marca"
                    rows={3}
                  />
                  <InputError message={errors.short_description} />
                </div>
              </div>

              {progress && (
                <div className="grid gap-2">
                  <div className="flex items-center justify-between text-xs text-muted-foreground">
                    <span>Subiendo logo</span>
                    <span>{Math.round(progress.percentage ?? 0)}%</span>
                  </div>
                  <div className="h-2 overflow-hidden rounded-full bg-muted">
                    <div
                      className="h-full rounded-full bg-primary transition-all"
                      style={{ width: `${progress.percentage ?? 0}%` }}
                    />
                  </div>
                </div>
              )}

              <DialogFooter>
                <DialogClose asChild>
                  <Button
                    type="button"
                    variant="secondary"
                    onClick={() => {
                      resetAndClearErrors()
                      resetLocalState()
                    }}
                  >
                    <HugeiconsIcon
                      icon={RefreshIcon}
                      className="size-4"
                    />
                    Cancelar
                  </Button>
                </DialogClose>

                <Button
                  type="submit"
                  disabled={processing}
                >
                  {processing && <Spinner />}
                  Crear marca
                </Button>
              </DialogFooter>
            </>
          )}
        </Form>
      </DialogContent>
    </Dialog>
  )
}

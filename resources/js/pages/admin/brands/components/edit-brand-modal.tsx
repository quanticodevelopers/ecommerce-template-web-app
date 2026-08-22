import { Edit02Icon, Image01Icon, RefreshIcon, Upload04Icon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Form } from '@inertiajs/react'
import type { ChangeEvent } from 'react'
import { useEffect, useRef, useState } from 'react'
import { update as brandsUpdate } from '@/actions/App/Http/Controllers/Admin/BrandController'
import InputError from '@/components/input-error'
import { Button } from '@/components/ui/button'
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Spinner } from '@/components/ui/spinner'
import { Textarea } from '@/components/ui/textarea'
import { cn } from '@/lib/utils'
import type { BrandListItem } from '@/types'

type EditBrandModalProps = {
  brand: BrandListItem
  triggerClassName?: string
}

function statusValue(isActive: boolean): string {
  return isActive ? '1' : '0'
}

export default function EditBrandModal({ brand, triggerClassName }: EditBrandModalProps) {
  const fileInputRef = useRef<HTMLInputElement>(null)
  const previewUrlRef = useRef<string | null>(null)
  const [isOpen, setIsOpen] = useState(false)
  const [logoPreview, setLogoPreview] = useState<string | null>(brand.logo_url)

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

  function resetLocalState(): void {
    clearTemporaryPreview()
    setLogoPreview(brand.logo_url)

    if (fileInputRef.current) {
      fileInputRef.current.value = ''
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
        <Button
          className={cn(triggerClassName)}
          type="button"
          variant="ghost"
          size="icon-xs"
          aria-label={`Editar ${brand.name}`}
        >
          <HugeiconsIcon
            icon={Edit02Icon}
            strokeWidth={1.5}
          />
        </Button>
      </DialogTrigger>

      <DialogContent className="max-h-[calc(100vh-2rem)] overflow-y-auto sm:max-w-2xl">
        <DialogHeader>
          <DialogTitle>Editar marca</DialogTitle>
          <DialogDescription>Actualiza los datos de la marca seleccionada.</DialogDescription>
        </DialogHeader>

        <Form
          {...brandsUpdate.form(brand.id)}
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
          {({ errors, processing, resetAndClearErrors }) => (
            <>
              <div className="grid gap-4">
                <div className="grid gap-2">
                  <Label htmlFor={`edit-name-${brand.id}`}>Nombre</Label>
                  <Input
                    id={`edit-name-${brand.id}`}
                    name="name"
                    maxLength={128}
                    placeholder="Nombre de la marca"
                    required
                    defaultValue={brand.name}
                  />
                  <InputError message={errors.name} />
                </div>

                <div className="grid gap-2">
                  <Label>Logo</Label>

                  <div className="flex flex-col gap-4 rounded-lg border bg-background/60 p-4 sm:flex-row sm:items-center">
                    <div className="flex aspect-3/2 h-32 shrink-0 items-center justify-center overflow-hidden rounded-md border bg-muted/40 md:h-24">
                      {logoPreview ? (
                        <img
                          alt={`Logo actual de ${brand.name}`}
                          className="h-full w-full object-contain p-1"
                          src={logoPreview}
                        />
                      ) : (
                        <HugeiconsIcon
                          icon={Image01Icon}
                          className="size-8 text-muted-foreground"
                          strokeWidth={1.5}
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
                          size="xs"
                          onClick={() => fileInputRef.current?.click()}
                        >
                          <HugeiconsIcon
                            icon={Upload04Icon}
                            strokeWidth={1.5}
                          />
                          Cambiar logo
                        </Button>

                        <Button
                          type="button"
                          variant="outline"
                          size="xs"
                          onClick={resetLocalState}
                        >
                          <HugeiconsIcon
                            icon={RefreshIcon}
                            strokeWidth={1.5}
                          />
                          Restablecer vista
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
                  <Label htmlFor={`edit-short_description-${brand.id}`}>Descripción breve</Label>
                  <Textarea
                    id={`edit-short_description-${brand.id}`}
                    name="short_description"
                    maxLength={128}
                    rows={3}
                    defaultValue={brand.short_description ?? ''}
                    placeholder="Descripción breve de la marca"
                  />
                  <InputError message={errors.short_description} />
                </div>

                <div className="grid gap-2">
                  <Label htmlFor={`edit-is_active-${brand.id}`}>Estado</Label>
                  <Select
                    name="is_active"
                    defaultValue={statusValue(brand.is_active)}
                  >
                    <SelectTrigger
                      id={`edit-is_active-${brand.id}`}
                      className="w-full"
                    >
                      <SelectValue placeholder="Selecciona un estado" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value="1">Activa</SelectItem>
                      <SelectItem value="0">Inactiva</SelectItem>
                    </SelectContent>
                  </Select>
                  <InputError message={errors.is_active} />
                </div>
              </div>

              <DialogFooter>
                <DialogClose asChild>
                  <Button
                    type="button"
                    variant="secondary"
                    onClick={() => resetAndClearErrors()}
                  >
                    Cancelar
                  </Button>
                </DialogClose>

                <Button
                  type="submit"
                  disabled={processing}
                >
                  {processing && <Spinner />}
                  Guardar cambios
                </Button>
              </DialogFooter>
            </>
          )}
        </Form>
      </DialogContent>
    </Dialog>
  )
}

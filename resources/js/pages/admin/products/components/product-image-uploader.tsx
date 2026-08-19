import { ArrowLeft01Icon, ArrowRight01Icon, Cancel01Icon, Image01Icon, Upload04Icon } from '@hugeicons/core-free-icons'
import type { ChangeEvent, DragEvent } from 'react'
import { useEffect, useRef, useState } from 'react'
import { Button } from '@/components/ui/button'
import { Icon } from '@/components/ui/icon'
import { cn } from '@/lib/utils'
import type { ProductFormImage, ProductImageSlot } from '@/types'

type ProductImageUploaderProps = {
  error?: string
  initialImages?: ProductFormImage[]
  onChange: (images: ProductImageSlot[]) => void
}

type ProductImagePreview = {
  alt: string
  existingId: string | null
  file: File | null
  id: string
  url: string
}

const maximumImages = 5
const maximumFileSize = 2 * 1024 * 1024
const allowedMimeTypes = new Set(['image/jpeg', 'image/png', 'image/webp', 'image/avif'])

export default function ProductImageUploader({ error, initialImages = [], onChange }: ProductImageUploaderProps) {
  const inputRef = useRef<HTMLInputElement>(null)
  const previewsRef = useRef<ProductImagePreview[]>([])
  const [previews, setPreviews] = useState<ProductImagePreview[]>(() =>
    initialImages.map((image) => ({
      alt: image.alt,
      existingId: image.id,
      file: null,
      id: image.id,
      url: image.url,
    })),
  )
  const [localError, setLocalError] = useState<string | null>(null)
  const [draggedIndex, setDraggedIndex] = useState<number | null>(null)

  useEffect(() => {
    previewsRef.current = previews
  }, [previews])

  useEffect(
    () => () => {
      previewsRef.current.forEach((preview) => {
        if (preview.file !== null) {
          URL.revokeObjectURL(preview.url)
        }
      })
    },
    [],
  )

  function updatePreviews(nextPreviews: ProductImagePreview[]): void {
    setPreviews(nextPreviews)
    onChange(
      nextPreviews.map((preview) => ({
        id: preview.existingId,
        file: preview.file,
      })),
    )
  }

  function handleFilesSelected(event: ChangeEvent<HTMLInputElement>): void {
    const selectedFiles = Array.from(event.target.files ?? [])
    event.target.value = ''

    if (selectedFiles.length === 0) {
      return
    }

    if (previews.length + selectedFiles.length > maximumImages) {
      setLocalError(`Puedes seleccionar un máximo de ${maximumImages} imágenes.`)

      return
    }

    const invalidType = selectedFiles.find((file) => !allowedMimeTypes.has(file.type))

    if (invalidType) {
      setLocalError(`${invalidType.name} no tiene un formato permitido.`)

      return
    }

    const oversizedFile = selectedFiles.find((file) => file.size > maximumFileSize)

    if (oversizedFile) {
      setLocalError(`${oversizedFile.name} supera el límite de 2 MB.`)

      return
    }

    setLocalError(null)
    updatePreviews([
      ...previews,
      ...selectedFiles.map((file) => ({
        alt: file.name,
        existingId: null,
        file,
        id: crypto.randomUUID(),
        url: URL.createObjectURL(file),
      })),
    ])
  }

  function removeImage(index: number): void {
    const preview = previews[index]

    if (preview.file !== null) {
      URL.revokeObjectURL(preview.url)
    }

    setLocalError(null)

    updatePreviews(previews.filter((_, previewIndex) => previewIndex !== index))
  }

  function moveImage(fromIndex: number, toIndex: number): void {
    if (toIndex < 0 || toIndex >= previews.length || fromIndex === toIndex) {
      return
    }

    const nextPreviews = [...previews]
    const [movedPreview] = nextPreviews.splice(fromIndex, 1)
    nextPreviews.splice(toIndex, 0, movedPreview)
    updatePreviews(nextPreviews)
  }

  function handleDrop(event: DragEvent<HTMLDivElement>, targetIndex: number): void {
    event.preventDefault()

    if (draggedIndex !== null) {
      moveImage(draggedIndex, targetIndex)
    }

    setDraggedIndex(null)
  }

  return (
    <div className="grid gap-3">
      <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
          <p className="font-medium">
            Imágenes <span className="text-destructive">*</span>
          </p>
          <p className="text-sm text-muted-foreground">Entre 1 y 5 imágenes. Las nuevas se recortarán al centro en proporción 1:1 y se guardarán en AVIF.</p>
        </div>

        <Button
          type="button"
          disabled={previews.length >= maximumImages}
          onClick={() => inputRef.current?.click()}
          variant="outline"
        >
          <Icon iconNode={Upload04Icon} />
          Agregar imágenes
        </Button>
        <input
          ref={inputRef}
          accept=".jpg,.jpeg,.png,.webp,.avif,image/jpeg,image/png,image/webp,image/avif"
          className="sr-only"
          multiple
          onChange={handleFilesSelected}
          type="file"
        />
      </div>

      {previews.length === 0 ? (
        <button
          type="button"
          className="flex min-h-48 flex-col items-center justify-center gap-3 rounded-xl border border-dashed bg-muted/20 px-6 text-center transition-colors hover:bg-muted/40"
          onClick={() => inputRef.current?.click()}
        >
          <span className="grid size-12 place-items-center rounded-full bg-background shadow-sm ring-1 ring-border">
            <Icon
              iconNode={Image01Icon}
              className="size-6 text-muted-foreground"
            />
          </span>
          <span>
            <span className="block font-medium">Selecciona las imágenes del producto</span>
            <span className="mt-1 block text-sm text-muted-foreground">JPG, JPEG, PNG, WebP o AVIF · máximo 2 MB cada una</span>
          </span>
        </button>
      ) : (
        <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-5">
          {previews.map((preview, index) => (
            <div
              key={preview.id}
              className={cn('group relative overflow-hidden rounded-xl border bg-muted/30', draggedIndex === index && 'opacity-50')}
              draggable
              onDragEnd={() => setDraggedIndex(null)}
              onDragOver={(event) => event.preventDefault()}
              onDragStart={() => setDraggedIndex(index)}
              onDrop={(event) => handleDrop(event, index)}
            >
              <img
                alt={preview.alt || `Vista previa ${index + 1}`}
                className="aspect-square w-full object-cover"
                src={preview.url}
              />
              <div className="absolute inset-x-0 top-0 flex items-center justify-between bg-linear-to-b from-black/60 to-transparent p-2">
                <span className="rounded-full bg-background/90 px-2 py-0.5 text-[11px] font-medium text-foreground">{index === 0 ? 'Principal' : `Imagen ${index + 1}`}</span>
                <Button
                  type="button"
                  aria-label={`Quitar imagen ${index + 1}`}
                  className="size-7 bg-background/90"
                  onClick={() => removeImage(index)}
                  size="icon-sm"
                  variant="ghost"
                >
                  <Icon iconNode={Cancel01Icon} />
                </Button>
              </div>
              <div className="absolute inset-x-0 bottom-0 flex justify-center gap-1 bg-linear-to-t from-black/60 to-transparent p-2">
                <Button
                  type="button"
                  aria-label={`Mover imagen ${index + 1} a la izquierda`}
                  className="size-7 bg-background/90"
                  disabled={index === 0}
                  onClick={() => moveImage(index, index - 1)}
                  size="icon-sm"
                  variant="ghost"
                >
                  <Icon iconNode={ArrowLeft01Icon} />
                </Button>
                <Button
                  type="button"
                  aria-label={`Mover imagen ${index + 1} a la derecha`}
                  className="size-7 bg-background/90"
                  disabled={index === previews.length - 1}
                  onClick={() => moveImage(index, index + 1)}
                  size="icon-sm"
                  variant="ghost"
                >
                  <Icon iconNode={ArrowRight01Icon} />
                </Button>
              </div>
            </div>
          ))}
        </div>
      )}

      <div className="flex items-center justify-between text-sm">
        <p className="text-muted-foreground">Arrastra las imágenes o usa las flechas para reordenarlas.</p>
        <span className="font-medium">
          {previews.length}/{maximumImages}
        </span>
      </div>
      {(localError ?? error) && <p className="text-sm text-destructive">{localError ?? error}</p>}
    </div>
  )
}

import { FolderAddIcon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Form } from '@inertiajs/react'
import { useState } from 'react'
import { store as categoriesStore } from '@/actions/App/Http/Controllers/Admin/CategoryController'
import InputError from '@/components/input-error'
import { Button } from '@/components/ui/button'
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Spinner } from '@/components/ui/spinner'
import { Textarea } from '@/components/ui/textarea'
import { cn } from '@/lib/utils'
import type { SelectOption } from '@/types'

const ROOT_PARENT_VALUE = '__root__'

type CreateCategoryModalProps = {
  parentCategoryOptions: SelectOption[]
  defaultParentId?: string | null
  triggerClassName?: string
}

export default function CreateCategoryModal({ parentCategoryOptions, defaultParentId = null, triggerClassName }: CreateCategoryModalProps) {
  const [isOpen, setIsOpen] = useState(false)
  const defaultValue = defaultParentId ?? ROOT_PARENT_VALUE

  return (
    <Dialog
      open={isOpen}
      onOpenChange={setIsOpen}
    >
      <DialogTrigger asChild>
        <Button className={cn(triggerClassName)}>
          <HugeiconsIcon
            icon={FolderAddIcon}
            className="size-4"
          />
          Nueva categoría
        </Button>
      </DialogTrigger>

      <DialogContent className="sm:max-w-2xl">
        <DialogHeader>
          <DialogTitle>Crear categoría</DialogTitle>
          <DialogDescription>Completa los datos obligatorios para registrar una categoría del catálogo.</DialogDescription>
        </DialogHeader>

        <Form
          {...categoriesStore.form()}
          options={{
            preserveScroll: true,
          }}
          onSuccess={() => setIsOpen(false)}
          resetOnSuccess
          className="space-y-5"
        >
          {({ errors, processing, resetAndClearErrors }) => (
            <>
              <div className="grid grid-cols-1 items-start gap-4 md:grid-cols-2">
                <div className="grid gap-2 md:col-span-2">
                  <Label htmlFor="name">Nombre</Label>
                  <Input
                    id="name"
                    name="name"
                    maxLength={128}
                    required
                    placeholder="Nombre de la categoría"
                  />
                  <InputError message={errors.name} />
                </div>

                <div className="grid gap-2 md:col-span-2">
                  <Label htmlFor="parent_id">Categoría padre</Label>
                  <Select
                    name="parent_id"
                    defaultValue={defaultValue}
                  >
                    <SelectTrigger
                      id="parent_id"
                      className="w-full"
                    >
                      <SelectValue placeholder="Selecciona una categoría padre" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value={ROOT_PARENT_VALUE}>Sin categoría padre</SelectItem>
                      {parentCategoryOptions.map((parentCategory) => (
                        <SelectItem
                          key={parentCategory.value}
                          value={parentCategory.value}
                        >
                          {parentCategory.label}
                        </SelectItem>
                      ))}
                    </SelectContent>
                  </Select>
                  <InputError message={errors.parent_id} />
                </div>

                <div className="grid gap-2 md:col-span-2">
                  <Label htmlFor="short_description">Descripción breve</Label>
                  <Textarea
                    id="short_description"
                    name="short_description"
                    maxLength={128}
                    rows={3}
                    placeholder="Descripción breve"
                  />
                  <InputError message={errors.short_description} />
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
                  Crear categoría
                </Button>
              </DialogFooter>
            </>
          )}
        </Form>
      </DialogContent>
    </Dialog>
  )
}

import { Edit02Icon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Form } from '@inertiajs/react'
import { useState } from 'react'
import { update as categoriesUpdate } from '@/actions/App/Http/Controllers/Admin/CategoryController'
import InputError from '@/components/input-error'
import { Button } from '@/components/ui/button'
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Spinner } from '@/components/ui/spinner'
import { Textarea } from '@/components/ui/textarea'
import { cn } from '@/lib/utils'
import type { CategoryListItem, SelectOption } from '@/types'

const ROOT_PARENT_VALUE = '__root__'

type EditCategoryModalProps = {
  category: CategoryListItem
  parentCategoryOptions: SelectOption[]
  triggerClassName?: string
}

export default function EditCategoryModal({ category, parentCategoryOptions, triggerClassName }: EditCategoryModalProps) {
  const [isOpen, setIsOpen] = useState(false)
  const defaultParentValue = category.parent?.id ?? ROOT_PARENT_VALUE
  const editableParentOptions = parentCategoryOptions.filter((parentCategory) => parentCategory.value !== category.id)
  const defaultStatusValue = category.is_active ? '1' : '0'

  return (
    <Dialog
      open={isOpen}
      onOpenChange={setIsOpen}
    >
      <DialogTrigger asChild>
        <Button
          className={cn(triggerClassName)}
          type="button"
          variant="ghost"
          size="icon-xs"
          aria-label={`Editar ${category.name}`}
        >
          <HugeiconsIcon
            icon={Edit02Icon}
            strokeWidth={1.5}
          />
        </Button>
      </DialogTrigger>

      <DialogContent className="sm:max-w-2xl">
        <DialogHeader>
          <DialogTitle>Editar categoría</DialogTitle>
          <DialogDescription>Actualiza los datos de la categoría seleccionada.</DialogDescription>
        </DialogHeader>

        <Form
          {...categoriesUpdate.form(category.id)}
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
                  <Label htmlFor={`edit-name-${category.id}`}>Nombre</Label>
                  <Input
                    id={`edit-name-${category.id}`}
                    name="name"
                    maxLength={128}
                    required
                    defaultValue={category.name}
                    placeholder="Nombre de la categoría"
                  />
                  <InputError message={errors.name} />
                </div>

                <div className="grid gap-2 md:col-span-2">
                  <Label htmlFor={`edit-parent_id-${category.id}`}>Categoría padre</Label>
                  <Select
                    name="parent_id"
                    defaultValue={defaultParentValue}
                  >
                    <SelectTrigger
                      id={`edit-parent_id-${category.id}`}
                      className="w-full"
                    >
                      <SelectValue placeholder="Selecciona una categoría padre" />
                    </SelectTrigger>
                    <SelectContent>
                      <SelectItem value={ROOT_PARENT_VALUE}>Sin categoría padre</SelectItem>
                      {editableParentOptions.map((parentCategory) => (
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
                  <Label htmlFor={`edit-short_description-${category.id}`}>Descripción breve</Label>
                  <Textarea
                    id={`edit-short_description-${category.id}`}
                    name="short_description"
                    maxLength={128}
                    rows={3}
                    defaultValue={category.short_description ?? ''}
                    placeholder="Descripción breve"
                  />
                  <InputError message={errors.short_description} />
                </div>

                <div className="grid gap-2 md:col-span-2">
                  <Label htmlFor={`edit-is_active-${category.id}`}>Estado</Label>
                  <Select
                    name="is_active"
                    defaultValue={defaultStatusValue}
                  >
                    <SelectTrigger
                      id={`edit-is_active-${category.id}`}
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

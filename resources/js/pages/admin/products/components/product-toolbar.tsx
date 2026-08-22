import { Add01Icon, Cancel01Icon, Search01Icon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { Link, router } from '@inertiajs/react'
import { useState } from 'react'
import type { SubmitEvent } from 'react'
import { Button } from '@/components/ui/button'
import { InputGroup, InputGroupAddon, InputGroupButton, InputGroupInput } from '@/components/ui/input-group'
import { create as productsCreate, index as productsIndex } from '@/routes/admin/products'

type ProductToolbarProps = {
  initialSearch: string
}

export default function ProductToolbar({ initialSearch }: ProductToolbarProps) {
  const [search, setSearch] = useState(initialSearch)

  function submitSearch(event: SubmitEvent<HTMLFormElement>) {
    event.preventDefault()

    router.get(productsIndex().url, search.trim() === '' ? {} : { search: search.trim() }, {
      preserveState: true,
      replace: true,
    })
  }

  function clearSearch() {
    setSearch('')
    router.get(productsIndex().url, {}, { preserveState: true, replace: true })
  }

  return (
    <div className="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
      <form
        className="flex w-full max-w-xl gap-2"
        onSubmit={submitSearch}
      >
        <InputGroup>
          <InputGroupAddon>
            <HugeiconsIcon
              icon={Search01Icon}
              strokeWidth={1.5}
            />
          </InputGroupAddon>
          <InputGroupInput
            aria-label="Buscar productos"
            maxLength={128}
            name="search"
            onChange={(event) => setSearch(event.target.value)}
            placeholder="Buscar por SKU, nombre o código de barras"
            value={search}
          />
          {search !== '' && (
            <InputGroupAddon align="inline-end">
              <InputGroupButton
                aria-label="Limpiar búsqueda"
                onClick={clearSearch}
                size="icon-xs"
              >
                <HugeiconsIcon
                  icon={Cancel01Icon}
                  strokeWidth={1.5}
                />
              </InputGroupButton>
            </InputGroupAddon>
          )}
        </InputGroup>
        <Button type="submit">
          Buscar
          <HugeiconsIcon
            icon={Search01Icon}
            strokeWidth={1.5}
          />
        </Button>
      </form>

      <Button
        asChild
        className="w-full sm:w-auto"
      >
        <Link
          href={productsCreate()}
          prefetch="mount"
          cacheFor="1m"
        >
          <HugeiconsIcon
            icon={Add01Icon}
            strokeWidth={1.5}
          />
          Nuevo producto
        </Link>
      </Button>
    </div>
  )
}

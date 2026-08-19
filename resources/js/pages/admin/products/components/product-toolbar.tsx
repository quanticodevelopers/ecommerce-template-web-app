import { Add01Icon, Cancel01Icon, Search01Icon } from '@hugeicons/core-free-icons'
import { Link, router } from '@inertiajs/react'
import { useState } from 'react'
import type { SubmitEvent } from 'react'
import { Button } from '@/components/ui/button'
import { Icon } from '@/components/ui/icon'
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
            <Icon iconNode={Search01Icon} />
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
                <Icon iconNode={Cancel01Icon} />
              </InputGroupButton>
            </InputGroupAddon>
          )}
        </InputGroup>
        <Button type="submit">Buscar</Button>
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
          <Icon iconNode={Add01Icon} />
          Nuevo producto
        </Link>
      </Button>
    </div>
  )
}

import { Cancel01Icon, Search01Icon } from '@hugeicons/core-free-icons'
import { router } from '@inertiajs/react'
import { useState } from 'react'
import type { FormEvent } from 'react'
import { Button } from '@/components/ui/button'
import { Icon } from '@/components/ui/icon'
import { InputGroup, InputGroupAddon, InputGroupButton, InputGroupInput } from '@/components/ui/input-group'
import { index as productsIndex } from '@/routes/admin/products'

type ProductToolbarProps = {
  initialSearch: string
  total: number
}

export default function ProductToolbar({ initialSearch, total }: ProductToolbarProps) {
  const [search, setSearch] = useState(initialSearch)

  function submitSearch(event: FormEvent<HTMLFormElement>) {
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

      <p className="shrink-0 text-sm text-muted-foreground">
        {total} {total === 1 ? 'producto' : 'productos'}
      </p>
    </div>
  )
}

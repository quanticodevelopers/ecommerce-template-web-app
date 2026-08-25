import { ViewIcon } from '@hugeicons/core-free-icons'
import { HugeiconsIcon } from '@hugeicons/react'
import { useState } from 'react'
import { Button } from '@/components/ui/button'
import CustomerDetailModal from '@/pages/admin/customers/components/customer-detail-modal'
import type { Customer } from '@/types'

type CustomerRowActionsProps = {
  customer: Customer
}

export default function CustomerRowActions({ customer }: CustomerRowActionsProps) {
  const [isDetailModalOpen, setIsDetailModalOpen] = useState(false)

  return (
    <>
      <div className="flex items-center justify-end gap-1">
        <Button
          type="button"
          variant="ghost"
          size="icon-xs"
          aria-label={`Ver detalles de ${customer.email}`}
          onClick={() => setIsDetailModalOpen(true)}
        >
          <HugeiconsIcon
            icon={ViewIcon}
            strokeWidth={1.5}
          />
        </Button>
      </div>

      <CustomerDetailModal
        customer={customer}
        isOpen={isDetailModalOpen}
        onClose={() => setIsDetailModalOpen(false)}
      />
    </>
  )
}

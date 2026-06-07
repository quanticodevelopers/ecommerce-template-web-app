import { EyeIcon } from 'lucide-react'
import { useState } from 'react'
import { Button } from '@/components/ui/button'
import CustomerDetailModal from '@/pages/admin/customers/components/customer-detail-modal'
import type { UserListItem } from '@/types'

type CustomerRowActionsProps = {
  customer: UserListItem
}

export default function CustomerRowActions({ customer }: CustomerRowActionsProps) {
  const [isDetailModalOpen, setIsDetailModalOpen] = useState(false)

  return (
    <>
      <div className="flex items-center justify-end gap-1">
        <Button
          variant="ghost"
          size="icon"
          className="size-8"
          aria-label={`Ver detalles de ${customer.email}`}
          onClick={() => setIsDetailModalOpen(true)}
        >
          <EyeIcon className="size-4" />
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

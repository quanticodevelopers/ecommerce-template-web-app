import type { PropsWithChildren } from 'react'
import StoreProviders from '@/components/store/providers'

const Layout = ({ children }: PropsWithChildren) => {
  return (
    <StoreProviders>
      <div>{children}</div>
    </StoreProviders>
  )
}

export default Layout

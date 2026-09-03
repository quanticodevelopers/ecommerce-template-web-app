export type FlashToast = {
  type: 'success' | 'info' | 'warning' | 'error'
  message: string
}

export interface SelectOption {
  value: string
  label: string
}

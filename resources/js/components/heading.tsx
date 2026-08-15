import type { IconSvgElement } from '@hugeicons/react'
import { Badge } from '@/components/ui/badge'
import { Icon } from '@/components/ui/icon'
import { cn } from '@/lib/utils'

interface HeadingProps {
  title: string
  description?: string
  badgeIcon?: IconSvgElement
  badgeLabel?: string
  variant?: 'default' | 'small'
}

export default function Heading({ title, description, badgeIcon, badgeLabel, variant = 'default' }: HeadingProps) {
  const isSmall = variant === 'small'
  const HeadingTag = isSmall ? 'h2' : 'h1'
  const showBadge = !isSmall && badgeIcon && badgeLabel

  return (
    <header className={cn(!isSmall && 'space-y-3')}>
      {showBadge && (
        <Badge
          variant="secondary"
          className="w-fit gap-1.5"
        >
          <Icon
            iconNode={badgeIcon}
            className="size-3.5"
          />
          {badgeLabel}
        </Badge>
      )}
      <div className={cn(!isSmall && 'space-y-2')}>
        <HeadingTag className={cn(isSmall ? 'text-base font-medium' : 'text-3xl font-semibold tracking-tight')}>{title}</HeadingTag>
        {description && <p className="text-sm text-muted-foreground">{description}</p>}
      </div>
    </header>
  )
}

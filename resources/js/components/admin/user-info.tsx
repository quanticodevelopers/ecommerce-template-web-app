import { Avatar, AvatarFallback } from '@/components/admin/ui/avatar'
import { useInitials } from '@/hooks/use-initials'
import type { User } from '@/types'

interface UserInfoProps {
  user: User
  showEmail?: boolean
  showRole?: boolean
}

export function UserInfo({ user, showEmail = false, showRole = false }: UserInfoProps) {
  const getInitials = useInitials()

  return (
    <>
      <Avatar className="h-8 w-8 overflow-hidden rounded-full">
        <AvatarFallback className="rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">{getInitials(user.name, user.last_name)}</AvatarFallback>
      </Avatar>
      <div className="grid flex-1 text-left text-sm leading-tight">
        <span className="truncate font-medium">{`${user.name} ${user.last_name}`}</span>
        {showEmail && <span className="text-muted-foreground truncate text-xs">{user.email}</span>}
        {showRole && user.kind === 'administrator' && <span className="text-muted-foreground truncate text-xs">{user.role.label}</span>}
      </div>
    </>
  )
}

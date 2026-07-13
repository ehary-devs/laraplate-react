import { Inbox  } from 'lucide-react';
import type {LucideIcon} from 'lucide-react';
import type { ReactNode } from 'react';
import { cn } from '@/lib/utils';

type EmptyStateProps = {
    title: string;
    description?: string;
    icon?: LucideIcon;
    /** Optional action, e.g. a Create button. */
    action?: ReactNode;
    className?: string;
};

/** Centered placeholder shown when a list or table has no rows. */
export function EmptyState({
    title,
    description,
    icon: Icon = Inbox,
    action,
    className,
}: EmptyStateProps) {
    return (
        <div
            className={cn(
                'flex flex-col items-center justify-center gap-2 px-6 py-12 text-center',
                className,
            )}
        >
            <div className="rounded-full bg-muted p-3 text-muted-foreground">
                <Icon className="size-6" />
            </div>
            <p className="text-sm font-medium">{title}</p>
            {description ? (
                <p className="max-w-sm text-sm text-muted-foreground">
                    {description}
                </p>
            ) : null}
            {action ? <div className="mt-2">{action}</div> : null}
        </div>
    );
}

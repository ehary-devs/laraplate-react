import { ArrowDown, ArrowUp, ChevronsUpDown } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';

type ColumnHeaderProps = {
    title: string;
    /** Column key; omit for a non-sortable header. */
    column?: string;
    activeSort?: string;
    activeDir?: 'asc' | 'desc';
    onSort?: (column: string) => void;
    className?: string;
};

/** Sortable DataTable column header. Renders plain text when not sortable. */
export function ColumnHeader({
    title,
    column,
    activeSort,
    activeDir,
    onSort,
    className,
}: ColumnHeaderProps) {
    if (!column || !onSort) {
        return <span className={cn('text-sm font-medium', className)}>{title}</span>;
    }

    const isActive = activeSort === column;

    return (
        <Button
            variant="ghost"
            size="sm"
            className={cn('-ml-3 h-8 data-[active=true]:text-foreground', className)}
            data-active={isActive}
            onClick={() => onSort(column)}
        >
            <span>{title}</span>
            {isActive && activeDir === 'asc' ? (
                <ArrowUp className="ml-2 size-4" />
            ) : isActive && activeDir === 'desc' ? (
                <ArrowDown className="ml-2 size-4" />
            ) : (
                <ChevronsUpDown className="ml-2 size-4 opacity-50" />
            )}
        </Button>
    );
}

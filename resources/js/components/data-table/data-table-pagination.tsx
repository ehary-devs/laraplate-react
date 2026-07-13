import { router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { PaginationMeta } from '@/types';

const PER_PAGE_OPTIONS = [10, 25, 50, 100];

type PaginationProps = {
    meta: PaginationMeta;
    perPage: number;
    onPerPageChange: (perPage: number) => void;
};

/** Per-page selector + prev/next navigation for a server-side DataTable. */
export function DataTablePagination({
    meta,
    perPage,
    onPerPageChange,
}: PaginationProps) {
    const prev = meta.links.find((link) => /previous|prev|«/i.test(link.label));
    const next = meta.links.find((link) => /next|»/i.test(link.label));

    const visit = (url: string | null) => {
        if (!url) {
            return;
        }

        router.get(url, {}, { preserveState: true, preserveScroll: true });
    };

    return (
        <div className="flex flex-col items-center justify-between gap-4 py-4 sm:flex-row">
            <div className="flex items-center gap-2 text-sm text-muted-foreground">
                <span>Rows per page</span>
                <Select
                    value={String(perPage)}
                    onValueChange={(value) => onPerPageChange(Number(value))}
                >
                    <SelectTrigger className="h-8 w-[72px]">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        {PER_PAGE_OPTIONS.map((option) => (
                            <SelectItem key={option} value={String(option)}>
                                {option}
                            </SelectItem>
                        ))}
                    </SelectContent>
                </Select>
            </div>

            <div className="flex items-center gap-4 text-sm">
                <span className="text-muted-foreground">
                    {meta.total > 0
                        ? `${meta.from}–${meta.to} of ${meta.total}`
                        : '0 results'}
                </span>
                <div className="flex items-center gap-2">
                    <Button
                        variant="outline"
                        size="sm"
                        disabled={!prev?.url}
                        onClick={() => visit(prev?.url ?? null)}
                    >
                        Previous
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        disabled={!next?.url}
                        onClick={() => visit(next?.url ?? null)}
                    >
                        Next
                    </Button>
                </div>
            </div>
        </div>
    );
}

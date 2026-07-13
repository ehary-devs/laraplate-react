import {
    
    flexRender,
    getCoreRowModel,
    useReactTable
} from '@tanstack/react-table';
import type {ColumnDef} from '@tanstack/react-table';
import type { ReactNode } from 'react';
import { EmptyState } from '@/components/empty-state';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { useDataTable } from '@/hooks/use-data-table';
import type { DataTableFilters, Paginated } from '@/types';
import { ColumnHeader } from './column-header';
import { DataTablePagination } from './data-table-pagination';
import { DataTableToolbar } from './data-table-toolbar';

type DataTableProps<T> = {
    /** TanStack column definitions. Use `meta.sortKey` to enable server sorting. */
    columns: ColumnDef<T, unknown>[];
    /** Laravel ResourceCollection payload (data + links + meta). */
    page: Paginated<T>;
    filters: DataTableFilters;
    searchPlaceholder?: string;
    /** Right-aligned toolbar slot (e.g. Create button). */
    toolbarActions?: ReactNode;
    emptyTitle?: string;
    emptyDescription?: string;
    /** Partial-reload keys so search/sort only refetch the table prop. */
    only?: string[];
};

/**
 * Server-side DataTable: search, sort, and pagination are query-string driven
 * via Inertia. TanStack Table handles column rendering only (manual mode).
 */
export function DataTable<T>({
    columns,
    page,
    filters,
    searchPlaceholder,
    toolbarActions,
    emptyTitle = 'No results',
    emptyDescription = 'Nothing matches your filters yet.',
    only,
}: DataTableProps<T>) {
    const { search, setSearch, toggleSort, setPerPage, sort, dir, perPage } =
        useDataTable({ filters, only });

    const table = useReactTable({
        data: page.data,
        columns,
        getCoreRowModel: getCoreRowModel(),
        manualSorting: true,
        manualPagination: true,
        manualFiltering: true,
    });

    const hasRows = page.data.length > 0;

    return (
        <div className="w-full">
            <DataTableToolbar
                search={search}
                onSearchChange={setSearch}
                searchPlaceholder={searchPlaceholder}
                actions={toolbarActions}
            />

            <div className="rounded-md border">
                <Table>
                    <TableHeader>
                        {table.getHeaderGroups().map((headerGroup) => (
                            <TableRow key={headerGroup.id}>
                                {headerGroup.headers.map((header) => {
                                    const meta = header.column.columnDef.meta;
                                    const sortKey = meta?.sortKey;

                                    return (
                                        <TableHead
                                            key={header.id}
                                            className={meta?.headerClassName}
                                        >
                                            {header.isPlaceholder ? null : (
                                                <ColumnHeader
                                                    title={meta?.title ?? ''}
                                                    column={sortKey}
                                                    activeSort={sort}
                                                    activeDir={dir}
                                                    onSort={
                                                        sortKey
                                                            ? toggleSort
                                                            : undefined
                                                    }
                                                />
                                            )}
                                        </TableHead>
                                    );
                                })}
                            </TableRow>
                        ))}
                    </TableHeader>
                    <TableBody>
                        {hasRows ? (
                            table.getRowModel().rows.map((row) => (
                                <TableRow key={row.id}>
                                    {row.getVisibleCells().map((cell) => (
                                        <TableCell key={cell.id}>
                                            {flexRender(
                                                cell.column.columnDef.cell,
                                                cell.getContext(),
                                            )}
                                        </TableCell>
                                    ))}
                                </TableRow>
                            ))
                        ) : (
                            <TableRow>
                                <TableCell
                                    colSpan={columns.length}
                                    className="h-48 p-0"
                                >
                                    <EmptyState
                                        title={emptyTitle}
                                        description={emptyDescription}
                                    />
                                </TableCell>
                            </TableRow>
                        )}
                    </TableBody>
                </Table>
            </div>

            <DataTablePagination
                meta={page.meta}
                perPage={perPage}
                onPerPageChange={setPerPage}
            />
        </div>
    );
}

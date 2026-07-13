import { Head } from '@inertiajs/react';
import type { ColumnDef } from '@tanstack/react-table';
import { index } from '@/actions/Modules/Access/Http/Controllers/PermissionController';
import { DataTable } from '@/components/data-table/data-table';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import type { DataTableFilters, Paginated } from '@/types';
import type { PermissionRow } from '../types';

interface Props {
    permissions: Paginated<PermissionRow>;
    filters: DataTableFilters;
}

const columns: ColumnDef<PermissionRow, unknown>[] = [
    {
        accessorKey: 'name',
        meta: { title: 'Name', sortKey: 'name' },
        cell: ({ row }) => (
            <span className="font-medium">{row.original.name}</span>
        ),
    },
    {
        accessorKey: 'slug',
        meta: { title: 'Slug', sortKey: 'slug' },
        cell: ({ row }) => (
            <code className="text-xs text-muted-foreground">
                {row.original.slug}
            </code>
        ),
    },
    {
        accessorKey: 'group',
        meta: { title: 'Group', sortKey: 'group' },
        cell: ({ row }) => <Badge variant="secondary">{row.original.group}</Badge>,
    },
];

export default function PermissionsIndex({ permissions, filters }: Props) {
    return (
        <>
            <Head title="Permissions" />
            <div className="px-4 py-6">
                <Heading
                    title="Permissions"
                    description="The permissions available to assign to roles."
                />
                <DataTable
                    columns={columns}
                    page={permissions}
                    filters={filters}
                    only={['permissions', 'filters']}
                    searchPlaceholder="Search permissions…"
                    emptyTitle="No permissions"
                    emptyDescription="No permissions match your search."
                />
            </div>
        </>
    );
}

PermissionsIndex.layout = {
    breadcrumbs: [{ title: 'Permissions', href: index() }],
};

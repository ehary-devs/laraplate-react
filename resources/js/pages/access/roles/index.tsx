import { Head, Link } from '@inertiajs/react';
import type { ColumnDef } from '@tanstack/react-table';
import { Plus } from 'lucide-react';
import {
    create,
    index,
} from '@/actions/Modules/Access/Http/Controllers/RoleController';
import { DataTable } from '@/components/data-table/data-table';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { useCan } from '@/hooks/use-can';
import type { DataTableFilters, Paginated } from '@/types';
import type { RoleRow } from '../types';
import { RoleRowActions } from './role-row-actions';

interface Props {
    roles: Paginated<RoleRow>;
    filters: DataTableFilters;
}

const columns: ColumnDef<RoleRow, unknown>[] = [
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
        id: 'permissions_count',
        meta: { title: 'Permissions' },
        cell: ({ row }) => (
            <Badge variant="secondary">{row.original.permissions_count ?? 0}</Badge>
        ),
    },
    {
        id: 'users_count',
        meta: { title: 'Users' },
        cell: ({ row }) => (
            <Badge variant="outline">{row.original.users_count ?? 0}</Badge>
        ),
    },
    {
        id: 'actions',
        meta: { title: '', headerClassName: 'w-12' },
        cell: ({ row }) => <RoleRowActions role={row.original} />,
    },
];

export default function RolesIndex({ roles, filters }: Props) {
    const { can } = useCan();

    return (
        <>
            <Head title="Roles" />
            <div className="px-4 py-6">
                <Heading
                    title="Roles & Permissions"
                    description="Manage roles and the permissions assigned to them."
                />
                <DataTable
                    columns={columns}
                    page={roles}
                    filters={filters}
                    only={['roles', 'filters']}
                    searchPlaceholder="Search roles…"
                    emptyTitle="No roles"
                    emptyDescription="No roles match your search."
                    toolbarActions={
                        can('role.create') ? (
                            <Button asChild>
                                <Link href={create()}>
                                    <Plus className="size-4" />
                                    New role
                                </Link>
                            </Button>
                        ) : null
                    }
                />
            </div>
        </>
    );
}

RolesIndex.layout = {
    breadcrumbs: [{ title: 'Roles', href: index() }],
};

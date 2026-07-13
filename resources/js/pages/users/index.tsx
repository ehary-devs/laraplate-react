import { Head, Link } from '@inertiajs/react';
import type { ColumnDef } from '@tanstack/react-table';
import { Plus } from 'lucide-react';
import {
    create,
    index,
} from '@/actions/Modules/User/Http/Controllers/UserController';
import { DataTable } from '@/components/data-table/data-table';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { useCan } from '@/hooks/use-can';
import type { DataTableFilters, Paginated } from '@/types';
import type { UserRow } from './types';
import { UserRowActions } from './user-row-actions';

interface Props {
    users: Paginated<UserRow>;
    filters: DataTableFilters;
}

const columns: ColumnDef<UserRow, unknown>[] = [
    {
        accessorKey: 'name',
        meta: { title: 'Name', sortKey: 'name' },
        cell: ({ row }) => (
            <span className="font-medium">{row.original.name}</span>
        ),
    },
    {
        accessorKey: 'email',
        meta: { title: 'Email', sortKey: 'email' },
    },
    {
        id: 'roles',
        meta: { title: 'Roles' },
        cell: ({ row }) => (
            <div className="flex flex-wrap gap-1">
                {row.original.roles.length > 0 ? (
                    row.original.roles.map((role) => (
                        <Badge key={role.id} variant="secondary">
                            {role.name}
                        </Badge>
                    ))
                ) : (
                    <span className="text-muted-foreground">—</span>
                )}
            </div>
        ),
    },
    {
        accessorKey: 'created_at',
        meta: { title: 'Created', sortKey: 'created_at' },
        cell: ({ row }) =>
            new Date(row.original.created_at).toLocaleDateString(),
    },
    {
        id: 'actions',
        meta: { title: '', headerClassName: 'w-12' },
        cell: ({ row }) => <UserRowActions user={row.original} />,
    },
];

export default function UsersIndex({ users, filters }: Props) {
    const { can } = useCan();

    return (
        <>
            <Head title="Users" />
            <div className="px-4 py-6">
                <Heading
                    title="Users"
                    description="Manage user accounts and their roles."
                />
                <DataTable
                    columns={columns}
                    page={users}
                    filters={filters}
                    only={['users', 'filters']}
                    searchPlaceholder="Search users…"
                    emptyTitle="No users"
                    emptyDescription="No users match your search."
                    toolbarActions={
                        can('user.create') ? (
                            <Button asChild>
                                <Link href={create()}>
                                    <Plus className="size-4" />
                                    New user
                                </Link>
                            </Button>
                        ) : null
                    }
                />
            </div>
        </>
    );
}

UsersIndex.layout = {
    breadcrumbs: [{ title: 'Users', href: index() }],
};

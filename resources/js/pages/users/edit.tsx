import { Head } from '@inertiajs/react';
import { index } from '@/actions/Modules/User/Http/Controllers/UserController';
import Heading from '@/components/heading';
import { UserForm } from './form';
import type { RoleOption, UserRow } from './types';

interface Props {
    user: { data: UserRow };
    roles: RoleOption[];
}

export default function EditUser({ user, roles }: Props) {
    return (
        <>
            <Head title={`Edit ${user.data.name}`} />
            <div className="px-4 py-6">
                <Heading
                    title="Edit user"
                    description="Update the user's details and roles."
                />
                <UserForm roles={roles} user={user.data} />
            </div>
        </>
    );
}

EditUser.layout = {
    breadcrumbs: [
        { title: 'Users', href: index() },
        { title: 'Edit', href: '#' },
    ],
};

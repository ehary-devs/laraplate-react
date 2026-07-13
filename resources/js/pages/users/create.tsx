import { Head } from '@inertiajs/react';
import {
    create,
    index,
} from '@/actions/Modules/User/Http/Controllers/UserController';
import Heading from '@/components/heading';
import { UserForm } from './form';
import type { RoleOption } from './types';

interface Props {
    roles: RoleOption[];
}

export default function CreateUser({ roles }: Props) {
    return (
        <>
            <Head title="Create user" />
            <div className="px-4 py-6">
                <Heading
                    title="Create user"
                    description="Add a new user and assign their roles."
                />
                <UserForm roles={roles} />
            </div>
        </>
    );
}

CreateUser.layout = {
    breadcrumbs: [
        { title: 'Users', href: index() },
        { title: 'Create', href: create() },
    ],
};

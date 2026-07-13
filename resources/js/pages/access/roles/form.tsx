import { Head, useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import RoleController, {
    index,
} from '@/actions/Modules/Access/Http/Controllers/RoleController';
import { FormField } from '@/components/form-field';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import type { PermissionGroups, RoleRow } from '../types';

interface Props {
    role?: { data: RoleRow };
    permissionGroups: PermissionGroups;
}

type RoleFormData = {
    name: string;
    slug: string;
    description: string;
    permissions: string[];
};

export default function RoleForm({ role, permissionGroups }: Props) {
    const current = role?.data;
    const isEdit = Boolean(current);

    const form = useForm<RoleFormData>({
        name: current?.name ?? '',
        slug: current?.slug ?? '',
        description: current?.description ?? '',
        permissions: current?.permissions ?? [],
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();

        if (isEdit && current) {
            form.put(RoleController.update.url({ role: current.id }), {
                preserveScroll: true,
            });
        } else {
            form.post(RoleController.store.url(), { preserveScroll: true });
        }
    };

    const togglePermission = (slug: string, checked: boolean) => {
        form.setData(
            'permissions',
            checked
                ? [...form.data.permissions, slug]
                : form.data.permissions.filter((value) => value !== slug),
        );
    };

    return (
        <>
            <Head title={isEdit ? `Edit ${current?.name}` : 'Create role'} />
            <div className="px-4 py-6">
                <Heading
                    title={isEdit ? 'Edit role' : 'Create role'}
                    description="Set the role details and select its permissions."
                />

                <form onSubmit={submit} className="max-w-3xl space-y-6">
                    <FormField
                        label="Name"
                        htmlFor="name"
                        error={form.errors.name}
                        required
                    >
                        <Input
                            id="name"
                            value={form.data.name}
                            onChange={(event) =>
                                form.setData('name', event.target.value)
                            }
                            required
                        />
                    </FormField>

                    <FormField
                        label="Slug"
                        htmlFor="slug"
                        error={form.errors.slug}
                        required
                        hint="Lowercase identifier, e.g. content-editor."
                    >
                        <Input
                            id="slug"
                            value={form.data.slug}
                            onChange={(event) =>
                                form.setData('slug', event.target.value)
                            }
                            required
                        />
                    </FormField>

                    <FormField
                        label="Description"
                        htmlFor="description"
                        error={form.errors.description}
                    >
                        <Textarea
                            id="description"
                            value={form.data.description}
                            onChange={(event) =>
                                form.setData('description', event.target.value)
                            }
                            rows={3}
                        />
                    </FormField>

                    <div className="space-y-4">
                        <div>
                            <p className="text-sm font-medium">Permissions</p>
                            <p className="text-sm text-muted-foreground">
                                Grant this role access to specific actions.
                            </p>
                        </div>

                        {Object.entries(permissionGroups).map(
                            ([group, permissions]) => (
                                <fieldset
                                    key={group}
                                    className="rounded-md border p-4"
                                >
                                    <legend className="px-1 text-sm font-medium">
                                        {group}
                                    </legend>
                                    <div className="grid gap-3 sm:grid-cols-2">
                                        {permissions.map((permission) => (
                                            <div
                                                key={permission.slug}
                                                className="flex items-center gap-2"
                                            >
                                                <Checkbox
                                                    id={permission.slug}
                                                    checked={form.data.permissions.includes(
                                                        permission.slug,
                                                    )}
                                                    onCheckedChange={(checked) =>
                                                        togglePermission(
                                                            permission.slug,
                                                            checked === true,
                                                        )
                                                    }
                                                />
                                                <Label
                                                    htmlFor={permission.slug}
                                                    className="font-normal"
                                                >
                                                    {permission.name}
                                                </Label>
                                            </div>
                                        ))}
                                    </div>
                                </fieldset>
                            ),
                        )}
                    </div>

                    <div className="flex items-center gap-3">
                        <Button type="submit" disabled={form.processing}>
                            {isEdit ? 'Save changes' : 'Create role'}
                        </Button>
                    </div>
                </form>
            </div>
        </>
    );
}

RoleForm.layout = {
    breadcrumbs: [
        { title: 'Roles', href: index() },
        { title: 'Form', href: '#' },
    ],
};

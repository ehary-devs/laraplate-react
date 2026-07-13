import { useForm } from '@inertiajs/react';
import type { FormEvent } from 'react';
import UserController from '@/actions/Modules/User/Http/Controllers/UserController';
import { FormField } from '@/components/form-field';
import PasswordInput from '@/components/password-input';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { RoleOption, UserRow } from './types';

type UserFormProps = {
    roles: RoleOption[];
    /** When present, the form edits this user; otherwise it creates one. */
    user?: UserRow;
};

type UserFormData = {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
    roles: number[];
};

export function UserForm({ roles, user }: UserFormProps) {
    const isEdit = Boolean(user);

    const form = useForm<UserFormData>({
        name: user?.name ?? '',
        email: user?.email ?? '',
        password: '',
        password_confirmation: '',
        roles: user?.roles.map((role) => role.id) ?? [],
    });

    const submit = (event: FormEvent) => {
        event.preventDefault();

        if (isEdit && user) {
            form.put(UserController.update.url({ user: user.id }), {
                preserveScroll: true,
            });
        } else {
            form.post(UserController.store.url(), { preserveScroll: true });
        }
    };

    const toggleRole = (roleId: number, checked: boolean) => {
        form.setData(
            'roles',
            checked
                ? [...form.data.roles, roleId]
                : form.data.roles.filter((id) => id !== roleId),
        );
    };

    return (
        <form onSubmit={submit} className="max-w-2xl space-y-6">
            <FormField label="Name" htmlFor="name" error={form.errors.name} required>
                <Input
                    id="name"
                    value={form.data.name}
                    onChange={(event) => form.setData('name', event.target.value)}
                    autoComplete="name"
                    required
                />
            </FormField>

            <FormField
                label="Email address"
                htmlFor="email"
                error={form.errors.email}
                required
            >
                <Input
                    id="email"
                    type="email"
                    value={form.data.email}
                    onChange={(event) => form.setData('email', event.target.value)}
                    autoComplete="off"
                    required
                />
            </FormField>

            <FormField
                label="Password"
                htmlFor="password"
                error={form.errors.password}
                required={!isEdit}
                hint={isEdit ? 'Leave blank to keep the current password.' : undefined}
            >
                <PasswordInput
                    id="password"
                    value={form.data.password}
                    onChange={(event) =>
                        form.setData('password', event.target.value)
                    }
                    autoComplete="new-password"
                />
            </FormField>

            <FormField
                label="Confirm password"
                htmlFor="password_confirmation"
                error={form.errors.password_confirmation}
                required={!isEdit}
            >
                <PasswordInput
                    id="password_confirmation"
                    value={form.data.password_confirmation}
                    onChange={(event) =>
                        form.setData('password_confirmation', event.target.value)
                    }
                    autoComplete="new-password"
                />
            </FormField>

            <fieldset className="grid gap-3">
                <legend className="mb-1 text-sm font-medium">Roles</legend>
                {roles.length === 0 ? (
                    <p className="text-sm text-muted-foreground">
                        No roles available.
                    </p>
                ) : (
                    roles.map((role) => (
                        <div key={role.id} className="flex items-center gap-2">
                            <Checkbox
                                id={`role-${role.id}`}
                                checked={form.data.roles.includes(role.id)}
                                onCheckedChange={(checked) =>
                                    toggleRole(role.id, checked === true)
                                }
                            />
                            <Label htmlFor={`role-${role.id}`} className="font-normal">
                                {role.name}
                            </Label>
                        </div>
                    ))
                )}
            </fieldset>

            <div className="flex items-center gap-3">
                <Button type="submit" disabled={form.processing}>
                    {isEdit ? 'Save changes' : 'Create user'}
                </Button>
            </div>
        </form>
    );
}

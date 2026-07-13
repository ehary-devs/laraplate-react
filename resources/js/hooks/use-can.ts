import { usePage } from '@inertiajs/react';

export type UseCanReturn = {
    /** True if the current user has the given permission slug (super-admin always true). */
    can: (permission: string) => boolean;
    /** True if the user has every one of the given permission slugs. */
    canAll: (permissions: string[]) => boolean;
    /** True if the user has at least one of the given permission slugs. */
    canAny: (permissions: string[]) => boolean;
    /** True if the user is assigned the given role slug. */
    hasRole: (role: string) => boolean;
};

/**
 * Client-side permission checks for show/hide UI only. The backend remains the
 * source of truth via middleware and Gate authorization.
 */
export function useCan(): UseCanReturn {
    const { auth } = usePage().props;
    const permissions = auth?.permissions ?? [];
    const roles = auth?.user?.roles ?? [];
    const isSuperAdmin = roles.includes('super-admin');

    const can = (permission: string): boolean =>
        isSuperAdmin || permissions.includes(permission);

    return {
        can,
        canAll: (list) => list.every(can),
        canAny: (list) => list.some(can),
        hasRole: (role) => roles.includes(role),
    };
}

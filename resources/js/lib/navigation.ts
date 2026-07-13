import { KeyRound, LayoutGrid, Shield, Users } from 'lucide-react';
import { index as permissionsIndex } from '@/actions/Modules/Access/Http/Controllers/PermissionController';
import { index as rolesIndex } from '@/actions/Modules/Access/Http/Controllers/RoleController';
import { index as usersIndex } from '@/actions/Modules/User/Http/Controllers/UserController';
import { dashboard } from '@/routes';
import type { NavItem } from '@/types';

/**
 * Sidebar navigation. Items with a `permission` are filtered client-side via
 * useCan(); the backend still enforces access on every route.
 */
export const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Users',
        href: usersIndex(),
        icon: Users,
        permission: 'user.view',
    },
    {
        title: 'Roles',
        href: rolesIndex(),
        icon: Shield,
        permission: 'role.view',
    },
    {
        title: 'Permissions',
        href: permissionsIndex(),
        icon: KeyRound,
        permission: 'permission.view',
    },
];

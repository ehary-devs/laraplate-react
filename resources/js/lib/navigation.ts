import { LayoutGrid, Shield, Users } from 'lucide-react';
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
        href: '/users',
        icon: Users,
        permission: 'user.view',
    },
    {
        title: 'Roles & Permissions',
        href: '/access/roles',
        icon: Shield,
        permission: 'role.view',
    },
];

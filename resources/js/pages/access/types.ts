/** A permission option in the role form's grouped checkbox list. */
export type PermissionOption = {
    slug: string;
    name: string;
};

/** Permissions keyed by their group name. */
export type PermissionGroups = Record<string, PermissionOption[]>;

/** A role row as serialized by RoleResource. */
export type RoleRow = {
    id: string;
    name: string;
    slug: string;
    description: string | null;
    permissions_count?: number;
    users_count?: number;
    permissions?: string[];
    created_at: string;
};

/** A permission row as serialized by PermissionResource. */
export type PermissionRow = {
    id: string;
    name: string;
    slug: string;
    group: string;
    description: string | null;
};

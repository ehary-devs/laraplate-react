/** A role reference as returned by UserResource / the roles option list. */
export type RoleOption = {
    id: number;
    name: string;
    slug: string;
};

/** A user row as serialized by UserResource. */
export type UserRow = {
    id: string;
    name: string;
    email: string;
    email_verified_at: string | null;
    roles: RoleOption[];
    created_at: string;
};

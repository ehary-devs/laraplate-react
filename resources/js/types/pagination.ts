/** A single pagination link as emitted by Laravel's paginator. */
export type PaginationLink = {
    url: string | null;
    label: string;
    active: boolean;
};

/** Meta block from a Laravel API ResourceCollection. */
export type PaginationMeta = {
    current_page: number;
    from: number | null;
    to: number | null;
    last_page: number;
    per_page: number;
    total: number;
    path: string;
    links: PaginationLink[];
};

/** Shape of `UserResource::collection($paginator)` serialized through Inertia. */
export type Paginated<T> = {
    data: T[];
    links: {
        first: string | null;
        last: string | null;
        prev: string | null;
        next: string | null;
    };
    meta: PaginationMeta;
};

/** DataTable query-string state carried in props and links. */
export type DataTableFilters = {
    search?: string;
    sort?: string;
    dir?: 'asc' | 'desc';
    per_page?: number;
};

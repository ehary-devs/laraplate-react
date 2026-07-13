import { router } from '@inertiajs/react';
import { useCallback, useEffect, useRef, useState } from 'react';
import type { DataTableFilters } from '@/types';

type UseDataTableOptions = {
    /** Current filter state from the server (props). */
    filters: DataTableFilters;
    /** Base URL to push query-string updates to (defaults to current path). */
    only?: string[];
    /** Debounce for the search input, in ms. */
    searchDebounce?: number;
};

/**
 * Manages server-side DataTable query-string state (search, sort, per-page).
 * Every change issues an Inertia `router.get` that preserves state and scroll.
 */
export function useDataTable({
    filters,
    only,
    searchDebounce = 300,
}: UseDataTableOptions) {
    const [search, setSearch] = useState(filters.search ?? '');
    const isFirstRender = useRef(true);

    const visit = useCallback(
        (next: DataTableFilters) => {
            const merged: DataTableFilters = { ...filters, ...next };

            // Drop empty values so URLs stay clean.
            const data = Object.fromEntries(
                Object.entries(merged).filter(
                    ([, value]) => value !== '' && value != null,
                ),
            );

            router.get(window.location.pathname, data, {
                preserveState: true,
                preserveScroll: true,
                replace: true,
                only,
            });
        },
        [filters, only],
    );

    // Debounced search → resets to page 1 by omitting `page`.
    useEffect(() => {
        if (isFirstRender.current) {
            isFirstRender.current = false;

            return;
        }

        const handle = setTimeout(() => {
            if (search !== (filters.search ?? '')) {
                visit({ search });
            }
        }, searchDebounce);

        return () => clearTimeout(handle);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search]);

    const toggleSort = useCallback(
        (column: string) => {
            const dir: 'asc' | 'desc' =
                filters.sort === column && filters.dir === 'asc'
                    ? 'desc'
                    : 'asc';
            visit({ sort: column, dir });
        },
        [filters.sort, filters.dir, visit],
    );

    const setPerPage = useCallback(
        (perPage: number) => visit({ per_page: perPage }),
        [visit],
    );

    return {
        search,
        setSearch,
        toggleSort,
        setPerPage,
        sort: filters.sort,
        dir: filters.dir,
        perPage: filters.per_page ?? 10,
    };
}

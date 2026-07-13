<?php

namespace App\Core\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class BaseService
{
    /**
     * Allowed page sizes for the shared DataTable helper.
     *
     * @var array<int, int>
     */
    protected array $perPageOptions = [10, 25, 50, 100];

    /**
     * Apply DataTable query-string filters (search, sort, per-page) to a query
     * and return a paginated result. Powers the server-side DataTable component.
     *
     * @template TModel of Model
     *
     * @param  Builder<TModel>  $query
     * @param  array{searchable?: array<int, string>, sortable?: array<int, string>, default_sort?: string, default_dir?: string}  $options
     * @return LengthAwarePaginator<int, TModel>
     */
    protected function paginateQuery(Builder $query, array $options = []): LengthAwarePaginator
    {
        $request = request();

        $searchable = $options['searchable'] ?? [];
        $sortable = $options['sortable'] ?? [];
        $defaultSort = $options['default_sort'] ?? 'id';
        $defaultDir = $options['default_dir'] ?? 'desc';

        $search = trim((string) $request->query('search', ''));

        if ($search !== '' && $searchable !== []) {
            $query->where(function (Builder $builder) use ($searchable, $search): void {
                foreach ($searchable as $column) {
                    $builder->orWhere($column, 'like', "%{$search}%");
                }
            });
        }

        $sort = (string) $request->query('sort', $defaultSort);

        if (! in_array($sort, $sortable, true)) {
            $sort = $defaultSort;
        }

        $dir = strtolower((string) $request->query('dir', $defaultDir)) === 'asc' ? 'asc' : 'desc';

        $query->orderBy($sort, $dir);

        $perPage = (int) $request->query('per_page', $this->perPageOptions[0]);

        if (! in_array($perPage, $this->perPageOptions, true)) {
            $perPage = $this->perPageOptions[0];
        }

        return $query->paginate($perPage)->withQueryString();
    }
}

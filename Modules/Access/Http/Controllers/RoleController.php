<?php

namespace Modules\Access\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Access\Http\Requests\StoreRoleRequest;
use Modules\Access\Http\Requests\UpdateRoleRequest;
use Modules\Access\Http\Resources\RoleResource;
use Modules\Access\Models\Permission;
use Modules\Access\Models\Role;
use Modules\Access\Services\AccessService;

class RoleController extends Controller
{
    public function __construct(private readonly AccessService $access) {}

    public function index(): Response
    {
        return Inertia::render('access/roles/index', [
            'roles' => RoleResource::collection($this->access->listRoles())
                ->response()
                ->getData(true),
            'filters' => request()->only(['search', 'sort', 'dir', 'per_page']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('access/roles/form', [
            'permissionGroups' => $this->permissionGroups(),
        ]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $role = $this->access->createRole([
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
        ]);

        $this->access->syncRolePermissions($role, $data['permissions'] ?? []);

        return to_route('role.index')->with('success', 'Role created.');
    }

    public function edit(Role $role): Response
    {
        $role->load('permissions');

        return Inertia::render('access/roles/form', [
            'role' => (new RoleResource($role))->response()->getData(true),
            'permissionGroups' => $this->permissionGroups(),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        $data = $request->validated();

        $this->access->updateRole($role, [
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
        ]);

        $this->access->syncRolePermissions($role, $data['permissions'] ?? []);

        return to_route('role.index')->with('success', 'Role updated.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $this->access->deleteRole($role);

        return to_route('role.index')->with('success', 'Role deleted.');
    }

    /**
     * Permissions grouped by their `group`, for the role form's checkbox list.
     *
     * @return array<string, array<int, array{slug: string, name: string}>>
     */
    private function permissionGroups(): array
    {
        return Permission::query()
            ->orderBy('group')
            ->orderBy('name')
            ->get(['name', 'slug', 'group'])
            ->groupBy('group')
            ->map(fn ($group) => $group
                ->map(fn (Permission $permission): array => [
                    'slug' => $permission->slug,
                    'name' => $permission->name,
                ])
                ->values()
                ->all())
            ->all();
    }
}

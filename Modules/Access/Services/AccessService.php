<?php

namespace Modules\Access\Services;

use App\Core\Services\BaseService;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Modules\Access\Events\PermissionGranted;
use Modules\Access\Events\PermissionRevoked;
use Modules\Access\Events\RoleAssigned;
use Modules\Access\Events\RoleRevoked;
use Modules\Access\Models\Permission;
use Modules\Access\Models\Role;

class AccessService extends BaseService
{
    /** @param array<string, mixed> $data */
    public function createRole(array $data): Role
    {
        return Role::create($data);
    }

    /** @param array<string, mixed> $data */
    public function updateRole(Role $role, array $data): Role
    {
        $role->update($data);

        return $role->fresh();
    }

    public function deleteRole(Role $role): void
    {
        $role->permissions()->detach();
        $role->users()->detach();
        $role->delete();
    }

    /** @param array<string, mixed> $data */
    public function createPermission(array $data): Permission
    {
        return Permission::create($data);
    }

    /** @param array<string, mixed> $data */
    public function updatePermission(Permission $permission, array $data): Permission
    {
        $permission->update($data);

        return $permission->fresh();
    }

    public function deletePermission(Permission $permission): void
    {
        $permission->roles()->detach();
        $permission->users()->detach();
        $permission->delete();
    }

    /** @param array<int, string> $permissionSlugs */
    public function syncRolePermissions(Role $role, array $permissionSlugs): void
    {
        $role->syncPermissions($permissionSlugs);

        // Invalidate cache for all users with this role
        $role->users->each->clearPermissionCache();
    }

    public function assignUserRole(User $user, string $roleSlug): void
    {
        $user->assignRole($roleSlug);

        event(new RoleAssigned($user, Role::where('slug', $roleSlug)->first()));
    }

    public function revokeUserRole(User $user, string $roleSlug): void
    {
        $user->removeRole($roleSlug);

        event(new RoleRevoked($user, Role::where('slug', $roleSlug)->first()));
    }

    public function giveDirectPermission(User $user, string $permissionSlug): void
    {
        $user->givePermissionTo($permissionSlug);
        event(new PermissionGranted($user, Permission::where('slug', $permissionSlug)->first()));
    }

    public function revokeDirectPermission(User $user, string $permissionSlug): void
    {
        $user->revokePermissionTo($permissionSlug);
        event(new PermissionRevoked($user, Permission::where('slug', $permissionSlug)->first()));
    }

    /** @return Collection<array-key, EloquentCollection<int, Permission>> */
    public function getPermissionsGrouped(): Collection
    {
        return Permission::all()->groupBy('group')->toBase();
    }
}

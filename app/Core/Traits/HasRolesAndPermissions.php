<?php

namespace App\Core\Traits;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Collection;
use Modules\Access\Models\Permission;
use Modules\Access\Models\Role;

/**
 * @property EloquentCollection<int, Role> $roles
 * @property EloquentCollection<int, Permission> $directPermissions
 */
trait HasRolesAndPermissions
{
    /** @return BelongsToMany<Role, $this, Pivot> */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /** @return BelongsToMany<Permission, $this, Pivot> */
    public function directPermissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_user');
    }

    public function assignRole(string|Role $role): void
    {
        $role = $role instanceof Role ? $role : Role::where('slug', $role)->firstOrFail();
        $this->roles()->syncWithoutDetaching([$role->id]);
        $this->clearPermissionCache();
    }

    public function removeRole(string|Role $role): void
    {
        $role = $role instanceof Role ? $role : Role::where('slug', $role)->firstOrFail();
        $this->roles()->detach($role->id);
        $this->clearPermissionCache();
    }

    public function hasRole(string|Role $role): bool
    {
        $slug = $role instanceof Role ? $role->slug : $role;

        return $this->roles->contains('slug', $slug);
    }

    /** @param array<array-key, string|Role> $roles */
    public function hasAnyRole(array $roles): bool
    {
        return collect($roles)->some(fn ($role) => $this->hasRole($role));
    }

    public function givePermissionTo(string|Permission $permission): void
    {
        $permission = $permission instanceof Permission
            ? $permission
            : Permission::where('slug', $permission)->firstOrFail();
        $this->directPermissions()->syncWithoutDetaching([$permission->id]);
        $this->clearPermissionCache();
    }

    public function revokePermissionTo(string|Permission $permission): void
    {
        $permission = $permission instanceof Permission
            ? $permission
            : Permission::where('slug', $permission)->firstOrFail();
        $this->directPermissions()->detach($permission->id);
        $this->clearPermissionCache();
    }

    public function hasPermissionTo(string $slug): bool
    {
        if ($this->hasRole('super-admin')) {
            return true;
        }

        return $this->getPermissionSlugs()->contains($slug);
    }

    /**
     * Effective permission slugs (role + direct, union), cached per user.
     *
     * Only slug strings are cached — never Eloquent models — so the entry stays
     * safe to unserialize across requests regardless of cache driver.
     *
     * @return Collection<int, string>
     */
    public function getPermissionSlugs(): Collection
    {
        $cacheKey = "user_permissions_{$this->id}";

        return cache()->remember($cacheKey, now()->addMinutes(60), function () {
            $this->loadMissing('roles.permissions', 'directPermissions');

            $rolePermissions = $this->roles->flatMap(fn (Role $r) => $r->permissions->pluck('slug'));

            return $rolePermissions
                ->merge($this->directPermissions->pluck('slug'))
                ->unique()
                ->values();
        });
    }

    /** @return Collection<int, Permission> */
    public function getAllPermissions(): Collection
    {
        $this->loadMissing('roles.permissions', 'directPermissions');

        $rolePermissions = $this->roles->flatMap(fn (Role $r) => $r->permissions);

        return $rolePermissions->merge($this->directPermissions)->unique('id')->values();
    }

    public function clearPermissionCache(): void
    {
        cache()->forget("user_permissions_{$this->id}");
        $this->unsetRelation('roles');
        $this->unsetRelation('directPermissions');
    }
}

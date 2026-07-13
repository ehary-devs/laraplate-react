<?php

namespace Modules\Access\Models;

use App\Core\Models\BaseModel;
use App\Core\Traits\HasPublicId;
use App\Models\User;
use Database\Factories\RoleFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property Collection<int, Permission> $permissions
 * @property Collection<int, User> $users
 */
class Role extends BaseModel
{
    /** @use HasFactory<RoleFactory> */
    use HasFactory, HasPublicId;

    protected $fillable = ['name', 'slug', 'description'];

    protected static function newFactory(): RoleFactory
    {
        return RoleFactory::new();
    }

    /** @return BelongsToMany<Permission, $this, Pivot> */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    /** @return BelongsToMany<User, $this, Pivot> */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    public function givePermissionTo(string|Permission $permission): void
    {
        $permission = $permission instanceof Permission
            ? $permission
            : Permission::where('slug', $permission)->firstOrFail();
        $this->permissions()->syncWithoutDetaching([$permission->id]);
    }

    public function revokePermissionTo(string|Permission $permission): void
    {
        $permission = $permission instanceof Permission
            ? $permission
            : Permission::where('slug', $permission)->firstOrFail();
        $this->permissions()->detach($permission->id);
    }

    /** @param array<int, string> $permissionSlugs */
    public function syncPermissions(array $permissionSlugs): void
    {
        $ids = Permission::whereIn('slug', $permissionSlugs)->pluck('id');
        $this->permissions()->sync($ids);
    }
}

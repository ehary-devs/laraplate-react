<?php

namespace Modules\Access\Models;

use App\Core\Models\BaseModel;
use App\Core\Traits\HasPublicId;
use App\Models\User;
use Database\Factories\PermissionFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property Collection<int, Role> $roles
 * @property Collection<int, User> $users
 */
class Permission extends BaseModel
{
    /** @use HasFactory<PermissionFactory> */
    use HasFactory, HasPublicId;

    protected $fillable = ['name', 'slug', 'group', 'description'];

    protected static function newFactory(): PermissionFactory
    {
        return PermissionFactory::new();
    }

    /** @return BelongsToMany<Role, $this, Pivot> */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_role');
    }

    /** @return BelongsToMany<User, $this, Pivot> */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'permission_user');
    }
}

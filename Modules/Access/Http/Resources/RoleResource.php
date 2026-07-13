<?php

namespace Modules\Access\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Access\Models\Role;

/**
 * @mixin Role
 */
class RoleResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'permissions_count' => $this->whenCounted('permissions'),
            'users_count' => $this->whenCounted('users'),
            'permissions' => $this->whenLoaded(
                'permissions',
                fn () => $this->permissions->pluck('slug')->values(),
            ),
            'created_at' => $this->created_at,
        ];
    }
}

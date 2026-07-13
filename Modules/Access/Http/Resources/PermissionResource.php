<?php

namespace Modules\Access\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Access\Models\Permission;

/**
 * @mixin Permission
 */
class PermissionResource extends JsonResource
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
            'group' => $this->group,
            'description' => $this->description,
        ];
    }
}

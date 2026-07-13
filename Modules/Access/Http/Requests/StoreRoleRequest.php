<?php

namespace Modules\Access\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Access\Models\Permission;
use Modules\Access\Models\Role;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('role.create') ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique(Role::class, 'slug')],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['array'],
            'permissions.*' => ['string', Rule::exists(Permission::class, 'slug')],
        ];
    }
}

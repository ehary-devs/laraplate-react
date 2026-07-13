<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Access\Models\Permission;

/**
 * @extends Factory<Permission>
 */
class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $resource = fake()->unique()->word();
        $action = fake()->randomElement(['view', 'create', 'update', 'delete']);

        return [
            'name' => ucfirst($resource).' '.ucfirst($action),
            'slug' => "{$resource}.{$action}",
            'group' => $resource,
            'description' => fake()->sentence(),
        ];
    }
}

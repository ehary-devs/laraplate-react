<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Access\Models\Permission;
use Modules\Access\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            ['name' => 'Super Admin', 'description' => 'Full access — bypasses all permission checks'],
        );

        $admin = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Admin', 'description' => 'Administrative access'],
        );

        $permissions = [
            ['name' => 'View Users', 'slug' => 'user.view', 'group' => 'User'],
            ['name' => 'Create Users', 'slug' => 'user.create', 'group' => 'User'],
            ['name' => 'Update Users', 'slug' => 'user.update', 'group' => 'User'],
            ['name' => 'Delete Users', 'slug' => 'user.delete', 'group' => 'User'],
            ['name' => 'View Roles', 'slug' => 'role.view', 'group' => 'Role'],
            ['name' => 'Create Roles', 'slug' => 'role.create', 'group' => 'Role'],
            ['name' => 'Update Roles', 'slug' => 'role.update', 'group' => 'Role'],
            ['name' => 'Delete Roles', 'slug' => 'role.delete', 'group' => 'Role'],
            ['name' => 'View Permissions', 'slug' => 'permission.view', 'group' => 'Permission'],
            ['name' => 'Create Permissions', 'slug' => 'permission.create', 'group' => 'Permission'],
            ['name' => 'Update Permissions', 'slug' => 'permission.update', 'group' => 'Permission'],
            ['name' => 'Delete Permissions', 'slug' => 'permission.delete', 'group' => 'Permission'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['slug' => $perm['slug']], $perm);
        }

        $editor = Role::firstOrCreate(
            ['slug' => 'editor'],
            ['name' => 'Editor', 'description' => 'Read-only access to users and roles'],
        );

        // Admin gets all non-delete permissions
        $adminPermissions = Permission::whereNotIn('slug', ['user.delete', 'role.delete', 'permission.delete'])->pluck('slug')->toArray();
        $admin->syncPermissions($adminPermissions);

        // Editor gets view-only permissions
        $editorPermissions = Permission::where('slug', 'like', '%.view')->pluck('slug')->toArray();
        $editor->syncPermissions($editorPermissions);
    }
}

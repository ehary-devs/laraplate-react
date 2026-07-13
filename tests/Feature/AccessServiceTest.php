<?php

use App\Models\User;
use Illuminate\Support\Facades\Event;
use Modules\Access\Events\PermissionGranted;
use Modules\Access\Events\PermissionRevoked;
use Modules\Access\Events\RoleAssigned;
use Modules\Access\Events\RoleRevoked;
use Modules\Access\Models\Permission;
use Modules\Access\Models\Role;
use Modules\Access\Services\AccessService;

beforeEach(function () {
    $this->service = new AccessService;
});

describe('role CRUD', function () {
    it('creates a role', function () {
        $role = $this->service->createRole(['name' => 'Editor', 'slug' => 'editor']);
        expect($role->slug)->toBe('editor');
        expect(Role::where('slug', 'editor')->exists())->toBeTrue();
    });

    it('updates a role and returns a fresh instance', function () {
        $role = Role::create(['name' => 'Old', 'slug' => 'old']);
        $updated = $this->service->updateRole($role, ['name' => 'New']);
        expect($updated->name)->toBe('New');
    });

    it('deletes a role and detaches relations', function () {
        $role = Role::create(['name' => 'Temp', 'slug' => 'temp']);
        $permission = Permission::create(['name' => 'X', 'slug' => 'x.do', 'group' => 'x']);
        $role->givePermissionTo($permission);
        $this->service->deleteRole($role);
        expect(Role::where('slug', 'temp')->exists())->toBeFalse();
    });
});

describe('permission CRUD', function () {
    it('creates a permission', function () {
        $permission = $this->service->createPermission([
            'name' => 'Edit Posts', 'slug' => 'posts.edit', 'group' => 'posts',
        ]);
        expect(Permission::where('slug', 'posts.edit')->exists())->toBeTrue();
    });

    it('deletes a permission and detaches relations', function () {
        $permission = Permission::create(['name' => 'Delete Me', 'slug' => 'delete.me', 'group' => 'test']);
        $role = Role::create(['name' => 'Test Role', 'slug' => 'test-role']);
        $permission->roles()->attach($role->id);
        $this->service->deletePermission($permission);
        expect(Permission::where('slug', 'delete.me')->exists())->toBeFalse();
    });
});

describe('role assignment events', function () {
    it('fires RoleAssigned when assigning a role', function () {
        $user = User::factory()->create();
        Role::create(['name' => 'Editor', 'slug' => 'editor']);
        Event::fake([RoleAssigned::class]);
        $this->service->assignUserRole($user, 'editor');
        Event::assertDispatched(RoleAssigned::class);
    });

    it('fires RoleRevoked when revoking a role', function () {
        $user = User::factory()->create();
        Role::create(['name' => 'Editor', 'slug' => 'editor']);
        $user->assignRole('editor');
        Event::fake([RoleRevoked::class]);
        $this->service->revokeUserRole($user, 'editor');
        Event::assertDispatched(RoleRevoked::class);
    });
});

describe('direct permission events', function () {
    it('grants a direct permission and fires PermissionGranted', function () {
        $user = User::factory()->create();
        Permission::create(['name' => 'View', 'slug' => 'posts.view', 'group' => 'posts']);
        Event::fake([PermissionGranted::class]);
        $this->service->giveDirectPermission($user, 'posts.view');
        expect($user->directPermissions()->where('slug', 'posts.view')->exists())->toBeTrue();
        Event::assertDispatched(PermissionGranted::class);
    });

    it('revokes a direct permission and fires PermissionRevoked', function () {
        $user = User::factory()->create();
        Permission::create(['name' => 'View', 'slug' => 'posts.view', 'group' => 'posts']);
        $user->givePermissionTo('posts.view');
        Event::fake([PermissionRevoked::class]);
        $this->service->revokeDirectPermission($user, 'posts.view');
        Event::assertDispatched(PermissionRevoked::class);
    });
});

it('groups permissions by their group', function () {
    Permission::create(['name' => 'A', 'slug' => 'a.view', 'group' => 'alpha']);
    Permission::create(['name' => 'B', 'slug' => 'b.view', 'group' => 'beta']);
    $grouped = $this->service->getPermissionsGrouped();
    expect($grouped->keys())->toContain('alpha', 'beta');
});

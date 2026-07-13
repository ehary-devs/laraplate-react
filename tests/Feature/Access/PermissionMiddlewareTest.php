<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Modules\Access\Models\Permission;
use Modules\Access\Models\Role;

beforeEach(function () {
    Route::middleware(['web', 'auth', 'permission:user.view'])
        ->get('/__test/users', fn () => 'ok')
        ->name('test.users');

    Route::middleware(['web', 'auth', 'role:admin'])
        ->get('/__test/admin', fn () => 'ok')
        ->name('test.admin');

    Permission::create(['name' => 'View Users', 'slug' => 'user.view', 'group' => 'User']);
});

describe('permission middleware', function () {
    it('blocks a user without the permission with 403', function () {
        $this->actingAs(User::factory()->create());

        $this->get('/__test/users')->assertForbidden();
    });

    it('allows a user with the permission via a role', function () {
        $role = Role::create(['name' => 'Editor', 'slug' => 'editor']);
        $role->givePermissionTo('user.view');

        $user = User::factory()->create();
        $user->assignRole('editor');

        $this->actingAs($user)->get('/__test/users')->assertOk();
    });

    it('allows a user with a direct permission', function () {
        $user = User::factory()->create();
        $user->givePermissionTo('user.view');

        $this->actingAs($user)->get('/__test/users')->assertOk();
    });

    it('lets a super-admin bypass the check', function () {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']));

        $this->actingAs($user)->get('/__test/users')->assertOk();
    });
});

describe('role middleware', function () {
    it('blocks a user without the role with 403', function () {
        $this->actingAs(User::factory()->create());

        $this->get('/__test/admin')->assertForbidden();
    });

    it('allows a user with the role', function () {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'Admin', 'slug' => 'admin']));

        $this->actingAs($user)->get('/__test/admin')->assertOk();
    });
});

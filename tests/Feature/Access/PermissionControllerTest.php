<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Access\Models\Permission;

describe('permissions index', function () {
    it('renders the permission list for a user with permission.view', function () {
        Permission::firstOrCreate(['slug' => 'permission.view'], ['name' => 'View Permissions', 'group' => 'Permission']);
        $user = User::factory()->create();
        $user->givePermissionTo('permission.view');

        $this->actingAs($user)
            ->get(route('permission.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('access/permissions/index')
                ->has('permissions.data')
                ->has('permissions.meta')
                ->has('filters')
            );
    });

    it('forbids a user without permission.view', function () {
        $this->actingAs(User::factory()->create())
            ->get(route('permission.index'))
            ->assertForbidden();
    });
});

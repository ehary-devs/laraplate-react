<?php

use Modules\Access\Models\Permission;
use Modules\Access\Models\Role;

describe('RoleFactory', function () {
    it('creates a role with the required fields', function () {
        $role = Role::factory()->create();
        expect($role->name)->not->toBeEmpty();
        expect($role->slug)->not->toBeEmpty();
        expect($role->uuid)->not->toBeNull();
    });

    it('creates multiple unique roles', function () {
        $roles = Role::factory()->count(3)->create();
        expect($roles->pluck('slug')->unique())->toHaveCount(3);
    });
});

describe('PermissionFactory', function () {
    it('creates a permission with the required fields', function () {
        $permission = Permission::factory()->create();
        expect($permission->name)->not->toBeEmpty();
        expect($permission->slug)->not->toBeEmpty();
        expect($permission->group)->not->toBeEmpty();
        expect($permission->uuid)->not->toBeNull();
    });

    it('creates multiple unique permissions', function () {
        $permissions = Permission::factory()->count(3)->create();
        expect($permissions->pluck('slug')->unique())->toHaveCount(3);
    });
});

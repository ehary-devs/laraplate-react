<?php

use App\Models\User;
use Modules\Access\Models\Permission;
use Modules\Access\Models\Role;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->role = Role::create(['name' => 'Editor', 'slug' => 'editor']);
    $this->permission = Permission::create(['name' => 'Edit Posts', 'slug' => 'posts.edit', 'group' => 'posts']);
});

describe('assignRole / removeRole', function () {
    it('assigns a role to a user', function () {
        $this->user->assignRole('editor');
        expect($this->user->hasRole('editor'))->toBeTrue();
    });

    it('removes a role from a user', function () {
        $this->user->assignRole('editor');
        $this->user->removeRole('editor');
        expect($this->user->hasRole('editor'))->toBeFalse();
    });

    it('does not duplicate a role on repeated assigns', function () {
        $this->user->assignRole('editor');
        $this->user->assignRole('editor');
        expect($this->user->roles()->count())->toBe(1);
    });
});

describe('hasRole / hasAnyRole', function () {
    it('returns false when the user has no roles', function () {
        expect($this->user->hasRole('editor'))->toBeFalse();
    });

    it('returns true when the user has the role', function () {
        $this->user->assignRole('editor');
        expect($this->user->hasRole('editor'))->toBeTrue();
    });

    it('hasAnyRole returns true when the user has at least one matching role', function () {
        $this->user->assignRole('editor');
        expect($this->user->hasAnyRole(['admin', 'editor']))->toBeTrue();
    });

    it('hasAnyRole returns false when the user has none of the given roles', function () {
        expect($this->user->hasAnyRole(['admin', 'editor']))->toBeFalse();
    });
});

describe('givePermissionTo / revokePermissionTo', function () {
    it('gives a direct permission to a user', function () {
        $this->user->givePermissionTo('posts.edit');
        expect($this->user->directPermissions()->where('slug', 'posts.edit')->exists())->toBeTrue();
    });

    it('revokes a direct permission from a user', function () {
        $this->user->givePermissionTo('posts.edit');
        $this->user->revokePermissionTo('posts.edit');
        expect($this->user->directPermissions()->where('slug', 'posts.edit')->exists())->toBeFalse();
    });
});

describe('hasPermissionTo (effective = union)', function () {
    it('resolves permissions inherited from a role', function () {
        $this->role->givePermissionTo($this->permission);
        $this->user->assignRole('editor');
        expect($this->user->hasPermissionTo('posts.edit'))->toBeTrue();
    });

    it('resolves direct permissions', function () {
        $this->user->givePermissionTo('posts.edit');
        expect($this->user->hasPermissionTo('posts.edit'))->toBeTrue();
    });

    it('super-admin bypasses all permission checks', function () {
        Role::create(['name' => 'Super Admin', 'slug' => 'super-admin']);
        $this->user->assignRole('super-admin');
        expect($this->user->hasPermissionTo('anything.at.all'))->toBeTrue();
    });

    it('returns false for a permission the user does not have', function () {
        expect($this->user->hasPermissionTo('posts.edit'))->toBeFalse();
    });
});

describe('getPermissionSlugs (cache is serialization-safe)', function () {
    it('caches plain slug strings, not Eloquent models', function () {
        $this->role->givePermissionTo($this->permission);
        $this->user->assignRole('editor');
        $this->user->givePermissionTo('posts.edit');

        $slugs = $this->user->getPermissionSlugs();

        expect($slugs->all())->toEqual(['posts.edit']);
        $slugs->each(fn ($slug) => expect($slug)->toBeString());

        // Cached entry must round-trip through serialize() cleanly.
        $cached = cache()->get("user_permissions_{$this->user->id}");
        expect(unserialize(serialize($cached))->all())->toEqual(['posts.edit']);
    });
});

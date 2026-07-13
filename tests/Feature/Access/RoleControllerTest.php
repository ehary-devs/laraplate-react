<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Modules\Access\Models\Permission;
use Modules\Access\Models\Role;

/**
 * Create a user holding the given permission slugs as direct permissions.
 *
 * @param  array<int, string>  $slugs
 */
function actorWith(array $slugs): User
{
    foreach ($slugs as $slug) {
        Permission::firstOrCreate(['slug' => $slug], ['name' => $slug, 'group' => 'Access']);
    }

    $user = User::factory()->create();

    foreach ($slugs as $slug) {
        $user->givePermissionTo($slug);
    }

    return $user;
}

describe('roles index', function () {
    it('renders the role list for a user with role.view', function () {
        Role::create(['name' => 'Editor', 'slug' => 'editor']);

        $this->actingAs(actorWith(['role.view']))
            ->get(route('role.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('access/roles/index')
                ->has('roles.data')
                ->has('roles.meta')
                ->has('filters')
            );
    });

    it('forbids a user without role.view', function () {
        $this->actingAs(User::factory()->create())
            ->get(route('role.index'))
            ->assertForbidden();
    });
});

describe('roles store', function () {
    it('creates a role and syncs its permissions', function () {
        Permission::create(['name' => 'View Users', 'slug' => 'user.view', 'group' => 'User']);
        $actor = actorWith(['role.create']);

        $this->actingAs($actor)
            ->post(route('role.store'), [
                'name' => 'Content Editor',
                'slug' => 'content-editor',
                'description' => 'Edits content',
                'permissions' => ['user.view'],
            ])
            ->assertRedirect(route('role.index'));

        $role = Role::where('slug', 'content-editor')->firstOrFail();
        expect($role->permissions->pluck('slug')->all())->toEqual(['user.view']);
    });

    it('validates required fields and slug format', function () {
        $actor = actorWith(['role.create']);

        $this->actingAs($actor)
            ->from(route('role.create'))
            ->post(route('role.store'), ['slug' => 'has spaces'])
            ->assertRedirect(route('role.create'))
            ->assertSessionHasErrors(['name', 'slug']);
    });

    it('forbids a user without role.create', function () {
        $this->actingAs(actorWith(['role.view']))
            ->post(route('role.store'), ['name' => 'X', 'slug' => 'x'])
            ->assertForbidden();
    });
});

describe('roles update', function () {
    it('updates a role and replaces its permissions', function () {
        Permission::create(['name' => 'A', 'slug' => 'a.view', 'group' => 'A']);
        Permission::create(['name' => 'B', 'slug' => 'b.view', 'group' => 'B']);
        $role = Role::create(['name' => 'Old', 'slug' => 'old']);
        $role->givePermissionTo('a.view');

        $this->actingAs(actorWith(['role.update']))
            ->put(route('role.update', $role), [
                'name' => 'New',
                'slug' => 'new',
                'permissions' => ['b.view'],
            ])
            ->assertRedirect(route('role.index'));

        $role->refresh();
        expect($role->name)->toBe('New');
        expect($role->permissions->pluck('slug')->all())->toEqual(['b.view']);
    });

    it('invalidates the permission cache of assigned users', function () {
        Permission::create(['name' => 'A', 'slug' => 'a.view', 'group' => 'A']);
        $role = Role::create(['name' => 'Team', 'slug' => 'team']);
        $member = User::factory()->create();
        $member->assignRole('team');

        // Prime the cache without the permission.
        expect($member->hasPermissionTo('a.view'))->toBeFalse();

        $this->actingAs(actorWith(['role.update']))
            ->put(route('role.update', $role), [
                'name' => 'Team',
                'slug' => 'team',
                'permissions' => ['a.view'],
            ]);

        expect($member->fresh()->hasPermissionTo('a.view'))->toBeTrue();
    });
});

describe('roles destroy', function () {
    it('deletes a role for a user with role.delete', function () {
        $role = Role::create(['name' => 'Temp', 'slug' => 'temp']);

        $this->actingAs(actorWith(['role.delete']))
            ->delete(route('role.destroy', $role))
            ->assertRedirect(route('role.index'));

        expect(Role::where('slug', 'temp')->exists())->toBeFalse();
    });

    it('forbids a user without role.delete', function () {
        $role = Role::create(['name' => 'Temp', 'slug' => 'temp']);

        $this->actingAs(actorWith(['role.view']))
            ->delete(route('role.destroy', $role))
            ->assertForbidden();
    });
});

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
function userWith(array $slugs): User
{
    foreach ($slugs as $slug) {
        Permission::firstOrCreate(['slug' => $slug], ['name' => $slug, 'group' => 'User']);
    }

    $user = User::factory()->create();

    foreach ($slugs as $slug) {
        $user->givePermissionTo($slug);
    }

    return $user;
}

describe('index', function () {
    it('renders the user list for a user with user.view', function () {
        User::factory()->count(3)->create();
        $actor = userWith(['user.view']);

        $this->actingAs($actor)
            ->get(route('user.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('users/index')
                ->has('users.data')
                ->has('users.meta')
                ->has('filters')
            );
    });

    it('forbids a user without user.view', function () {
        $this->actingAs(User::factory()->create())
            ->get(route('user.index'))
            ->assertForbidden();
    });
});

describe('store', function () {
    it('creates a user and syncs roles', function () {
        $role = Role::create(['name' => 'Editor', 'slug' => 'editor']);
        $actor = userWith(['user.create']);

        $this->actingAs($actor)
            ->post(route('user.store'), [
                'name' => 'Jane Doe',
                'email' => 'jane@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
                'roles' => [$role->id],
            ])
            ->assertRedirect(route('user.index'));

        $created = User::where('email', 'jane@example.com')->firstOrFail();
        expect($created->hasRole('editor'))->toBeTrue();
    });

    it('validates required fields', function () {
        $actor = userWith(['user.create']);

        $this->actingAs($actor)
            ->from(route('user.create'))
            ->post(route('user.store'), [])
            ->assertRedirect(route('user.create'))
            ->assertSessionHasErrors(['name', 'email', 'password']);
    });

    it('forbids a user without user.create', function () {
        $this->actingAs(userWith(['user.view']))
            ->post(route('user.store'), [
                'name' => 'X',
                'email' => 'x@example.com',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
            ])
            ->assertForbidden();
    });
});

describe('update', function () {
    it('updates a user without changing the password when left blank', function () {
        $actor = userWith(['user.update']);
        $target = User::factory()->create(['name' => 'Old Name']);
        $originalPassword = $target->password;

        $this->actingAs($actor)
            ->put(route('user.update', $target), [
                'name' => 'New Name',
                'email' => $target->email,
                'password' => '',
                'password_confirmation' => '',
                'roles' => [],
            ])
            ->assertRedirect(route('user.index'));

        $target->refresh();
        expect($target->name)->toBe('New Name');
        expect($target->password)->toBe($originalPassword);
    });
});

describe('destroy', function () {
    it('soft-deletes a user', function () {
        $actor = userWith(['user.delete']);
        $target = User::factory()->create();

        $this->actingAs($actor)
            ->delete(route('user.destroy', $target))
            ->assertRedirect(route('user.index'));

        expect(User::withTrashed()->find($target->id)->trashed())->toBeTrue();
    });

    it('forbids a user without user.delete', function () {
        $target = User::factory()->create();

        $this->actingAs(userWith(['user.view']))
            ->delete(route('user.destroy', $target))
            ->assertForbidden();
    });
});

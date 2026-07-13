<?php

use App\Models\User;
use Modules\Access\Models\Permission;
use Modules\Access\Models\Role;
use Modules\Notification\Models\Notification;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('dashboard renders the inertia page with stats and recent notifications', function () {
    User::factory()->count(2)->create();
    Role::create(['name' => 'Editor', 'slug' => 'editor']);
    Permission::create(['name' => 'View Users', 'slug' => 'user.view', 'group' => 'User']);
    Notification::create([
        'type' => 'test',
        'title' => 'Something happened',
        'message' => 'A test notification.',
        'level' => 'success',
    ]);

    $this->actingAs(User::factory()->create());

    $this->get(route('dashboard'))
        ->assertInertia(fn ($page) => $page
            ->component('dashboard')
            ->where('stats.users', 3)
            ->where('stats.roles', 1)
            ->where('stats.permissions', 1)
            ->where('stats.unread_notifications', 1)
            ->has('recentNotifications', 1)
            ->where('recentNotifications.0.title', 'Something happened')
            ->where('recentNotifications.0.level', 'success')
        );
});

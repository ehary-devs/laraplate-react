<?php

use App\Models\User;
use Modules\Access\Models\Role;
use Modules\Access\Services\AccessService;
use Modules\Auth\Events\UserLoggedIn;
use Modules\Notification\Models\Notification;

it('records a notification when UserLoggedIn fires', function () {
    $user = User::factory()->create();

    event(new UserLoggedIn($user));

    expect(Notification::where('type', 'user.login')->where('notifiable_id', $user->id)->exists())->toBeTrue();
});

it('records a notification when a role is assigned', function () {
    $user = User::factory()->create();
    Role::create(['name' => 'Editor', 'slug' => 'editor']);

    app(AccessService::class)->assignUserRole($user, 'editor');

    expect(Notification::where('type', 'access.role_assigned')->where('notifiable_id', $user->id)->exists())->toBeTrue();
});

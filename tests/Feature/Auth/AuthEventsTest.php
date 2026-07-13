<?php

use App\Models\User;
use Illuminate\Support\Facades\Event;
use Laravel\Fortify\Features;
use Modules\Auth\Events\UserLoggedIn;
use Modules\Auth\Events\UserLoggedOut;
use Modules\Auth\Events\UserRegistered;

test('logging in dispatches the UserLoggedIn domain event', function () {
    Event::fake([UserLoggedIn::class]);

    $user = User::factory()->create();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    Event::assertDispatched(UserLoggedIn::class, fn (UserLoggedIn $event) => $event->user->is($user));
});

test('logging out dispatches the UserLoggedOut domain event', function () {
    Event::fake([UserLoggedOut::class]);

    $user = User::factory()->create();

    $this->actingAs($user)->post(route('logout'));

    Event::assertDispatched(UserLoggedOut::class, fn (UserLoggedOut $event) => $event->user->is($user));
});

test('registering dispatches the UserRegistered domain event', function () {
    $this->skipUnlessFortifyHas(Features::registration());

    Event::fake([UserRegistered::class]);

    $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    Event::assertDispatched(UserRegistered::class);
});

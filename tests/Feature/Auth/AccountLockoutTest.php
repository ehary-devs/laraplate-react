<?php

use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Modules\Auth\Events\AccountLocked;
use Modules\Auth\Services\AccountLockout;

test('account is locked and AccountLocked is dispatched after max failed attempts', function () {
    Event::fake([AccountLocked::class]);
    config(['security.account_lockout.max_attempts' => 3]);

    $user = User::factory()->create();
    $lockout = app(AccountLockout::class);

    foreach (range(1, 3) as $attempt) {
        $lockout->recordFailure($user->email);
    }

    Event::assertDispatched(AccountLocked::class, fn (AccountLocked $event) => $event->user->is($user));

    expect(fn () => $lockout->ensureNotLocked($user->email))
        ->toThrow(ValidationException::class);
});

test('locked account cannot authenticate even with the correct password', function () {
    config(['security.account_lockout.max_attempts' => 3]);

    $user = User::factory()->create();
    $lockout = app(AccountLockout::class);

    foreach (range(1, 3) as $attempt) {
        $lockout->recordFailure($user->email);
    }

    $response = $this->from(route('login'))->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors('email');
});

test('a successful login clears prior failed attempts', function () {
    config(['security.account_lockout.max_attempts' => 3]);

    $user = User::factory()->create();
    $lockout = app(AccountLockout::class);

    $lockout->recordFailure($user->email);
    $lockout->recordFailure($user->email);

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();

    // Counter was reset, so two fresh failures must not re-lock the account.
    $lockout->recordFailure($user->email);
    $lockout->recordFailure($user->email);

    expect(fn () => $lockout->ensureNotLocked($user->email))->not->toThrow(Exception::class);
});

<?php

use App\Models\PasswordHistory;
use App\Models\User;
use Laravel\Fortify\Features;

test('registration records an initial password history entry', function () {
    $this->skipUnlessFortifyHas(Features::registration());

    $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'test@example.com')->firstOrFail();

    expect(PasswordHistory::where('user_id', $user->id)->count())->toBe(1);
});

test('password update is rejected when reusing a recent password', function () {
    config(['security.password.history_count' => 3]);

    $user = User::factory()->create();
    PasswordHistory::record($user->id, $user->password);

    $response = $this->actingAs($user)
        ->from(route('security.edit'))
        ->put(route('user-password.update'), [
            'current_password' => 'password',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

    $response->assertSessionHasErrors('password');
});

test('password update records new history and prunes to the configured count', function () {
    config(['security.password.history_count' => 2]);

    $user = User::factory()->create();
    PasswordHistory::record($user->id, $user->password);

    $this->actingAs($user);

    $sequence = ['password', 'password-one', 'password-two', 'password-three'];

    for ($i = 0; $i < count($sequence) - 1; $i++) {
        $this->from(route('security.edit'))
            ->put(route('user-password.update'), [
                'current_password' => $sequence[$i],
                'password' => $sequence[$i + 1],
                'password_confirmation' => $sequence[$i + 1],
            ])
            ->assertSessionHasNoErrors();
    }

    expect(PasswordHistory::where('user_id', $user->id)->count())->toBe(2);
});

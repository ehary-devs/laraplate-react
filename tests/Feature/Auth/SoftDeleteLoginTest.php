<?php

use App\Models\User;

test('soft deleted users cannot authenticate', function () {
    $user = User::factory()->create();
    $user->delete();

    $response = $this->from(route('login'))->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors();
});

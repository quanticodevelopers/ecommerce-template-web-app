<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

test('customer login screen can be rendered', function () {
    $response = $this->get(route('store.auth.login'));

    $response->assertOk();
});

test('customer users can authenticate using the login screen', function () {
    $user = User::factory()
        ->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('store.home', absolute: false));
});

test('customer users can not authenticate with invalid password', function () {
    $user = User::factory()
        ->create();

    $this
        ->withSession(['auth.area' => 'store'])
        ->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

    $this->assertGuest();
});

test('customer users can logout', function () {
    $user = User::factory()
        ->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('store.home'));

    $this->assertGuest();
});

test('customer users are rate limited', function () {
    $user = User::factory()
        ->create();

    RateLimiter::increment(md5('login'.implode('|', [$user->email, '127.0.0.1'])), amount: 5);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertTooManyRequests();
});

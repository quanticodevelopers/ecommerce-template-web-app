<?php

use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

test('admin login screen can be rendered', function () {
    $response = $this->get(route('admin.auth.login'));

    $response->assertOk();
});

test('admin users can authenticate using the login screen', function () {
    $user = User::factory()
        ->admin()
        ->create();

    $response = $this
        ->withSession(['auth.area' => 'admin'])
        ->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('admin.dashboard', absolute: false));
});

test('admin users can not authenticate with invalid password', function () {
    $user = User::factory()
        ->admin()
        ->create();

    $this
        ->withSession(['auth.area' => 'admin'])
        ->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

    $this->assertGuest();
});

test('admin users can logout', function () {
    $user = User::factory()
        ->admin()
        ->create();

    $response = $this->actingAs($user)->post(route('logout'));

    $response->assertRedirect(route('store.home'));

    $this->assertGuest();
});

test('admin users are rate limited', function () {
    $user = User::factory()
        ->admin()
        ->create();

    RateLimiter::increment(md5('login'.implode('|', [$user->email, '127.0.0.1'])), amount: 5);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertTooManyRequests();
});

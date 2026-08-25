<?php

use App\Models\Administrator;
use App\Models\Customer;

test('customer login screen can be rendered', function () {
    $response = $this->get(route('store.auth.login'));

    $response->assertOk();
});

test('customer users can authenticate using the login screen', function () {
    $user = Customer::factory()
        ->create();

    $response = $this->post(route('store.auth.login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated('store');
    $response->assertRedirect(route('store.home', absolute: false));
});

test('customer users can authenticate with remember selected', function () {
    $customer = Customer::factory()->create();

    $response = $this->post(route('store.auth.login.store'), [
        'email' => $customer->email,
        'password' => 'password',
        'remember' => '1',
    ]);

    $response->assertRedirect(route('store.home', absolute: false));
    $this->assertAuthenticatedAs($customer, 'store');
});

test('customer users can not authenticate with invalid password', function () {
    $user = Customer::factory()
        ->create();

    $this->post(route('store.auth.login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest('store');
});

test('customer users can logout', function () {
    $user = Customer::factory()
        ->create();
    $administrator = Administrator::factory()->create();

    $response = $this->actingAs($administrator, 'admin')
        ->actingAs($user, 'store')
        ->post(route('store.auth.logout'));

    $response->assertRedirect(route('store.home'));

    $this->assertGuest('store');
    $this->assertAuthenticatedAs($administrator, 'admin');
});

test('administrators cannot authenticate in the store environment', function () {
    $administrator = Administrator::factory()->create();

    $this->post(route('store.auth.login.store'), [
        'email' => $administrator->email,
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest('store');
});

test('customer users are rate limited', function () {
    $user = Customer::factory()
        ->create();

    foreach (range(1, 5) as $attempt) {
        $this->post(route('store.auth.login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
    }

    $response = $this->post(route('store.auth.login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertTooManyRequests();
});

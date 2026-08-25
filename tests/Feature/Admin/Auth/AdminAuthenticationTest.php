<?php

use App\Models\Administrator;
use App\Models\Customer;

test('admin login screen can be rendered', function () {
    $this->get(route('admin.auth.login'))->assertOk();
});

test('administrators can authenticate using the admin guard', function () {
    $administrator = Administrator::factory()->create();

    $response = $this->post(route('admin.auth.login.store'), [
        'email' => $administrator->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($administrator, 'admin');
    $this->assertGuest('store');
    $response->assertRedirect(route('admin.dashboard', absolute: false));
});

test('administrators can authenticate with remember selected', function () {
    $administrator = Administrator::factory()->create();

    $response = $this->post(route('admin.auth.login.store'), [
        'email' => $administrator->email,
        'password' => 'password',
        'remember' => '1',
    ]);

    $response->assertRedirect(route('admin.dashboard', absolute: false));
    $this->assertAuthenticatedAs($administrator, 'admin');
});

test('customers cannot authenticate in the admin environment', function () {
    $customer = Customer::factory()->create();

    $this->post(route('admin.auth.login.store'), [
        'email' => $customer->email,
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest('admin');
});

test('administrators cannot authenticate with an invalid password', function () {
    $administrator = Administrator::factory()->create();

    $this->post(route('admin.auth.login.store'), [
        'email' => $administrator->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest('admin');
});

test('administrators can logout from the admin guard', function () {
    $administrator = Administrator::factory()->create();
    $customer = Customer::factory()->create();

    $response = $this->actingAs($customer, 'store')
        ->actingAs($administrator, 'admin')
        ->post(route('admin.auth.logout'));

    $response->assertRedirect(route('admin.auth.login'));
    $this->assertGuest('admin');
    $this->assertAuthenticatedAs($customer, 'store');
});

test('administrator login is rate limited', function () {
    $administrator = Administrator::factory()->create();

    foreach (range(1, 5) as $attempt) {
        $this->post(route('admin.auth.login.store'), [
            'email' => $administrator->email,
            'password' => 'wrong-password',
        ]);
    }

    $this->post(route('admin.auth.login.store'), [
        'email' => $administrator->email,
        'password' => 'wrong-password',
    ])->assertTooManyRequests();
});

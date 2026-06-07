<?php

use App\Models\User;

test('inactive admins are logged out when they try to access admin routes', function () {
    $admin = User::factory()->state([
        'is_active' => false,
    ])->create();

    $response = $this->actingAs($admin)
        ->get(route('admin.dashboard'));

    $response->assertRedirect(route('admin.auth.login'));
    $response->assertSessionHasErrors([
        'email' => __('auth.inactive_admin'),
    ]);

    $this->assertGuest();
});

test('inactive admins cannot authenticate through the admin login', function () {
    $admin = User::factory()->state([
        'is_active' => false,
    ])->create();

    $response = $this->from(route('admin.auth.login'))
        ->withSession(['auth.area' => 'admin'])
        ->post(route('login.store'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);

    $response->assertRedirect(route('admin.auth.login'));
    $response->assertSessionHasErrors([
        'email' => __('auth.inactive_admin'),
    ]);

    $this->assertGuest();
});

test('active admins can authenticate through the admin login', function () {
    $admin = User::factory()->create();

    $response = $this->from(route('admin.auth.login'))
        ->withSession(['auth.area' => 'admin'])
        ->post(route('login.store'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);

    $this->assertAuthenticatedAs($admin);
    $response->assertRedirect(route('admin.dashboard', absolute: false));
});

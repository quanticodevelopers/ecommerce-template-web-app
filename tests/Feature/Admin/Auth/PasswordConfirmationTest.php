<?php

use App\Models\Administrator;
use Inertia\Testing\AssertableInertia as Assert;

test('confirm password screen can be rendered', function () {
    $user = Administrator::factory()
        ->create();

    $response = $this
        ->actingAs($user, 'admin')
        ->get(route('admin.auth.password.confirm'));

    $response->assertOk();

    $response->assertInertia(fn (Assert $page) => $page
        ->component('admin/auth/confirm-password'),
    );
});

test('password confirmation requires authentication', function () {
    $response = $this->get(route('admin.auth.password.confirm'));

    $response->assertRedirect(route('admin.auth.login'));
});

test('administrators can confirm their password using the admin guard', function () {
    $administrator = Administrator::factory()->create();

    $this->actingAs($administrator, 'admin')
        ->post(route('admin.auth.password.confirm.store'), [
            'password' => 'password',
        ])
        ->assertRedirect(route('admin.dashboard', absolute: false));

    expect(session('auth.password_confirmed_at'))->toBeInt();
});

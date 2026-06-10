<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('confirm password screen can be rendered', function () {
    $user = User::factory()
        ->admin()
        ->create();

    $response = $this
        ->actingAs($user)
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

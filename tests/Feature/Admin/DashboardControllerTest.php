<?php

use App\Models\User;

test('guests are redirected to the admin login page when accessing dashboard', function () {
    $response = $this->get(route('admin.dashboard'));

    $response->assertRedirect(route('admin.auth.login'));
});

test('admins can visit the dashboard', function () {
    $user = User::factory()
        ->admin()
        ->create();
    $this->actingAs($user);

    $response = $this->get(route('admin.dashboard'));
    $response->assertOk();
});

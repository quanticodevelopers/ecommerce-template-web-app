<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

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
    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/dashboard/index')
            ->where('auth.user.id', $user->id)
            ->where('auth.user.name', $user->name)
            ->where('auth.user.last_name', $user->last_name)
        );
});

<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the admin login page when accessing customers index', function () {
    $response = $this->get(route('admin.customers.index'));

    $response->assertRedirect(route('admin.auth.login'));
});

test('admins can see customer users', function () {
    $admin = User::factory()
        ->admin()
        ->create();

    User::factory()
        ->create();

    $this->actingAs($admin)
        ->get(route('admin.customers.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/customers/index')
            ->has('customers', 2)
        );
});

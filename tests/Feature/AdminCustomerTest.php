<?php

use App\Enums\UserRole;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the admin login page when accessing customers index', function () {
    $response = $this->get(route('admin.customers.index'));

    $response->assertRedirect(route('admin.auth.login'));
});

test('admins can see only customer users', function () {
    $admin = User::factory()->create();
    $customer = User::factory()->state([
        'role' => UserRole::CUSTOMER->value,
    ])->create();

    $this->actingAs($admin)
        ->get(route('admin.customers.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/customers/index')
            ->has('customers', 1)
            ->where('customers.0.email', $customer->email)
            ->where('customers.0.role.value', UserRole::CUSTOMER->value)
            ->where('customers.0.role.label', UserRole::CUSTOMER->label())
            ->where('customers.0.is_active', true)
            ->where('customers.0.name', $customer->name)
            ->where('customers.0.last_name', $customer->last_name)
            ->missing('customers.0.email_verified')
            ->missing('customers.0.email_verified_at')
            ->missing('customers.1'),
        );
});

test('admin users are not shown in the customers list', function () {
    $admin = User::factory()->create();
    User::factory()->count(2)->create(); // more admins

    $this->actingAs($admin)
        ->get(route('admin.customers.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/customers/index')
            ->has('customers', 0),
        );
});

<?php

use App\Models\Administrator;
use App\Models\Customer;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the admin login page when accessing customers index', function () {
    $this->get(route('admin.customers.index'))
        ->assertRedirect(route('admin.auth.login'));
});

test('customer listing contains only customer identities', function () {
    $administrator = Administrator::factory()->create();
    $customer = Customer::factory()->create();

    $this->actingAs($administrator, 'admin')
        ->get(route('admin.customers.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/customers/index')
            ->has('customers', 1)
            ->where('customers.0.id', $customer->id)
            ->where('customers.0.kind', 'customer')
        );
});

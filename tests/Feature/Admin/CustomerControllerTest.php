<?php

use App\Models\User;
use Illuminate\Support\Collection;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the admin login page when accessing customers index', function () {
    $response = $this->get(route('admin.customers.index'));

    $response->assertRedirect(route('admin.auth.login'));
});

test('customer listing includes customer and administrator users', function () {
    $admin = User::factory()
        ->admin()
        ->create();

    $customer = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('admin.customers.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/customers/index')
            ->has('customers', 2)
            ->where('customers', function (Collection $customers) use ($admin, $customer): bool {
                $listedUserIds = collect($customers)->pluck('id')->sort()->values()->all();
                $expectedUserIds = collect([$admin->id, $customer->id])->sort()->values()->all();

                return $listedUserIds === $expectedUserIds;
            })
        );
});

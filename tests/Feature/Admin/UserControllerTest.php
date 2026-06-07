<?php

use App\Enums\UserRole;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the admin login page', function () {
    $response = $this->get(route('admin.users.index'));

    $response->assertRedirect(route('admin.auth.login'));
});

test('admins can see only administrator users', function () {
    $admin = User::factory()->create();
    $customer = User::factory()->state([
        'role' => UserRole::CUSTOMER->value,
    ])->create();

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/users/index')
            ->has('users', 1)
            ->where('users.0.email', $admin->email)
            ->where('users.0.role.value', UserRole::ADMIN->value)
            ->where('users.0.role.label', UserRole::ADMIN->label())
            ->where('users.0.name', $admin->name)
            ->where('users.0.last_name', $admin->last_name)
            ->missing('users.0.email_verified')
            ->missing('users.0.email_verified_at')
            ->missing('users.1'),
        );

    expect($customer->refresh()->role)->toBe(UserRole::CUSTOMER);
});

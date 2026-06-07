<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
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

test('admins can create administrator users with generated credentials', function () {
    $admin = User::factory()->create();

    $payload = [
        'document_type' => 'dni',
        'document_number' => '12345678',
        'name' => 'Daniel',
        'last_name' => 'Perez',
        'email' => 'daniel.admin@example.com',
        'phone' => '987654321',
    ];

    $response = $this->actingAs($admin)
        ->post(route('admin.users.store'), $payload);

    $response->assertRedirect(route('admin.users.index'));

    $createdUser = User::query()->where('email', $payload['email'])->first();

    expect($createdUser)->not->toBeNull();
    expect($createdUser->role)->toBe(UserRole::ADMIN);
    expect($createdUser->email_verified_at)->not->toBeNull();

    $credentials = $response->baseResponse->getSession()->get('created_user_credentials');

    expect($credentials)->toBeArray();
    expect($credentials['email'])->toBe($payload['email']);
    expect($credentials['password'])->toHaveLength(18);
    expect($credentials['password'])->toMatch('/^[a-zA-Z0-9]+$/');
    expect(Hash::check($credentials['password'], $createdUser->password))->toBeTrue();
});

<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the admin login page when accessing users index', function () {
    $response = $this->get(route('admin.users.index'));

    $response->assertRedirect(route('admin.auth.login'));
});

test('admins can see only administrator users', function () {
    $admin = User::factory()
        ->admin()
        ->create();

    $customer = User::factory()
        ->create();

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/users/index')
            ->has('users', 1)
            ->where('users.0.email', $admin->email)
            ->where('users.0.role.value', UserRole::ADMIN->value)
            ->missing('users.1'),
        );

    expect($customer->refresh()->role)->toBe(UserRole::CUSTOMER);
});

test('admins can create administrator users with generated credentials', function () {
    $admin = User::factory()
        ->admin()
        ->create();

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

    $response
        ->assertRedirect(route('admin.users.index'))
        ->assertInertiaFlash('toast.type', 'success');

    $createdUser = User::query()->where('email', $payload['email'])->firstOrFail();
    $credentials = $response->getSession()->get('created_user_credentials');

    expect($createdUser->role)->toBe(UserRole::ADMIN)
        ->and($createdUser->email_verified_at)->not->toBeNull()
        ->and($credentials)->toBeArray()
        ->and($credentials['email'])->toBe($payload['email'])
        ->and($credentials['password'])->toHaveLength(18)
        ->and($credentials['password'])->toMatch('/^[a-zA-Z0-9]+$/')
        ->and(Hash::check($credentials['password'], $createdUser->password))->toBeTrue();
});

test('administrator creation rejects an email and document already in use', function () {
    $admin = User::factory()->admin()->create();
    $existingUser = User::factory()->create([
        'document_type' => 'dni',
        'document_number' => '12345678',
        'email' => 'existing@example.com',
    ]);

    $this->actingAs($admin)
        ->post(route('admin.users.store'), [
            'document_type' => 'dni',
            'document_number' => $existingUser->document_number,
            'name' => 'Daniel',
            'last_name' => 'Perez',
            'email' => $existingUser->email,
            'phone' => '987654321',
        ])
        ->assertSessionHasErrors(['document_number', 'email']);

    expect(User::query()->count())->toBe(2);
});

test('admins can reset another administrator password with generated credentials', function () {
    $admin = User::factory()
        ->admin()
        ->create();
    $targetAdmin = User::factory()
        ->admin()
        ->create();

    $previousPasswordHash = $targetAdmin->password;

    $response = $this->actingAs($admin)
        ->patch(route('admin.users.reset-password', $targetAdmin), [
            'password' => 'password',
        ]);

    $response->assertRedirect(route('admin.users.index'));

    $credentials = $response->baseResponse->getSession()->get('created_user_credentials');
    $updatedAdmin = $targetAdmin->refresh();

    expect($credentials)->toBeArray()
        ->and($credentials['email'])->toBe($updatedAdmin->email)
        ->and($credentials['password'])->toHaveLength(18)
        ->and($credentials['password'])->toMatch('/^[a-zA-Z0-9]+$/')
        ->and(Hash::check($credentials['password'], $updatedAdmin->password))->toBeTrue()
        ->and($updatedAdmin->password)->not->toBe($previousPasswordHash);
});

test('admins must confirm password to reset an administrator password', function () {
    $admin = User::factory()
        ->admin()
        ->create();
    $targetAdmin = User::factory()
        ->admin()
        ->create();
    $previousPasswordHash = $targetAdmin->password;

    $response = $this->actingAs($admin)
        ->patch(route('admin.users.reset-password', $targetAdmin), [
            'password' => 'incorrect-password',
        ]);

    $response->assertSessionHasErrors('password');

    expect($targetAdmin->refresh()->password)->toBe($previousPasswordHash);
});

test('admins cannot reset a customer password', function () {
    $admin = User::factory()
        ->admin()
        ->create();
    $customer = User::factory()->create();
    $previousPasswordHash = $customer->password;

    $this->actingAs($admin)
        ->patch(route('admin.users.reset-password', $customer), [
            'password' => 'password',
        ])
        ->assertNotFound();

    expect($customer->refresh()->password)->toBe($previousPasswordHash);
});

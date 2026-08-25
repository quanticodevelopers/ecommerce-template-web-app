<?php

use App\Enums\AdministratorRole;
use App\Models\Administrator;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the admin login page when accessing administrators index', function () {
    $this->get(route('admin.admins.index'))
        ->assertRedirect(route('admin.auth.login'));
});

test('administrators can see only administrator identities', function () {
    $administrator = Administrator::factory()->create();
    Customer::factory()->create();

    $this->actingAs($administrator, 'admin')
        ->get(route('admin.admins.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/admins/index')
            ->has('admins', 1)
            ->where('admins.0.email', $administrator->email)
            ->where('admins.0.kind', 'administrator')
            ->where('admins.0.role.value', AdministratorRole::ADMIN->value)
        );
});

test('administrators can create administrator identities with generated credentials', function () {
    $administrator = Administrator::factory()->create();
    $payload = [
        'document_type' => 'dni',
        'document_number' => '12345678',
        'name' => 'Daniel',
        'last_name' => 'Perez',
        'email' => 'daniel.admin@example.com',
        'phone' => '987654321',
    ];

    $response = $this->actingAs($administrator, 'admin')
        ->post(route('admin.admins.store'), $payload);

    $response
        ->assertRedirect(route('admin.admins.index'))
        ->assertInertiaFlash('toast.type', 'success');

    $createdAdministrator = Administrator::query()->where('email', $payload['email'])->firstOrFail();
    $credentials = $response->getSession()->get('created_administrator_credentials');

    expect($createdAdministrator->role)->toBe(AdministratorRole::ADMIN)
        ->and($credentials)->toBeArray()
        ->and($credentials['email'])->toBe($payload['email'])
        ->and($credentials['password'])->toHaveLength(18)
        ->and($credentials['password'])->toMatch('/^[a-zA-Z0-9]+$/')
        ->and(Hash::check($credentials['password'], $createdAdministrator->password))->toBeTrue();
});

test('administrator creation enforces uniqueness inside the administrator domain', function () {
    $administrator = Administrator::factory()->create();
    $existingAdministrator = Administrator::factory()->create([
        'document_type' => 'dni',
        'document_number' => '12345678',
        'email' => 'existing@example.com',
    ]);

    $this->actingAs($administrator, 'admin')
        ->post(route('admin.admins.store'), [
            'document_type' => 'dni',
            'document_number' => $existingAdministrator->document_number,
            'name' => 'Daniel',
            'last_name' => 'Perez',
            'email' => $existingAdministrator->email,
            'phone' => '987654321',
        ])
        ->assertSessionHasErrors(['document_number', 'email']);

    expect(Administrator::query()->count())->toBe(2);
});

test('administrators can reset another administrator password', function () {
    $administrator = Administrator::factory()->create();
    $targetAdministrator = Administrator::factory()->create();
    $previousPasswordHash = $targetAdministrator->password;

    $response = $this->actingAs($administrator, 'admin')
        ->patch(route('admin.admins.reset-password', $targetAdministrator), [
            'password' => 'password',
        ]);

    $response->assertRedirect(route('admin.admins.index'));

    $credentials = $response->getSession()->get('created_administrator_credentials');
    $updatedAdministrator = $targetAdministrator->refresh();

    expect($credentials)->toBeArray()
        ->and($credentials['email'])->toBe($updatedAdministrator->email)
        ->and(Hash::check($credentials['password'], $updatedAdministrator->password))->toBeTrue()
        ->and($updatedAdministrator->password)->not->toBe($previousPasswordHash);
});

test('administrators must confirm their password before resetting another administrator password', function () {
    $administrator = Administrator::factory()->create();
    $targetAdministrator = Administrator::factory()->create();
    $previousPasswordHash = $targetAdministrator->password;

    $this->actingAs($administrator, 'admin')
        ->patch(route('admin.admins.reset-password', $targetAdministrator), [
            'password' => 'incorrect-password',
        ])
        ->assertSessionHasErrors('password');

    expect($targetAdministrator->refresh()->password)->toBe($previousPasswordHash);
});

test('customer ids cannot be resolved as administrator route bindings', function () {
    $administrator = Administrator::factory()->create();
    $customer = Customer::factory()->create();

    $this->actingAs($administrator, 'admin')
        ->patch(route('admin.admins.reset-password', $customer->id), [
            'password' => 'password',
        ])
        ->assertNotFound();
});

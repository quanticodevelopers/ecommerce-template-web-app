<?php

use App\Enums\UserDocumentType;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;

test('registration screen can be rendered', function () {
    $response = $this->get(route('store.auth.register'));

    $response->assertOk();
});

test('new customer users can register', function () {
    $payload = [
        'document_type' => 'dni',
        'document_number' => '87654321',
        'name' => 'Rodrigo',
        'last_name' => 'Quispe',
        'email' => 'rodrigo.customer@example.com',
        'phone' => '963852741',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $response = $this->post(route('store.auth.register.store'), $payload);

    $this->assertAuthenticated('store');
    $response->assertRedirect(route('store.home', absolute: false));

    $user = Customer::query()->where('email', $payload['email'])->firstOrFail();

    expect($user->document_type)->toBe(UserDocumentType::DNI)
        ->and($user->document_number)->toBe($payload['document_number'])
        ->and($user->email_verified_at)->toBeNull()
        ->and(Hash::check($payload['password'], $user->password))->toBeTrue();
});

test('customer registration enforces profile restrictions', function (array $invalidData, string $field) {
    $payload = [
        'document_type' => 'dni',
        'document_number' => '87654321',
        'name' => 'Rodrigo',
        'last_name' => 'Quispe',
        'email' => 'rodrigo.customer@example.com',
        'phone' => '963852741',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $this->post(route('store.auth.register.store'), [...$payload, ...$invalidData])
        ->assertSessionHasErrors($field);

    $this->assertGuest('store');
    expect(Customer::query()->count())->toBe(0);
})->with([
    'document type must be supported' => [['document_type' => 'ruc'], 'document_type'],
    'DNI must contain eight digits' => [['document_number' => '1234567'], 'document_number'],
    'passport must be alphanumeric' => [[
        'document_type' => 'pasaporte',
        'document_number' => 'ABC-123',
    ], 'document_number'],
    'phone must contain nine digits' => [['phone' => '12345678'], 'phone'],
    'email must be valid' => [['email' => 'invalid-email'], 'email'],
]);

test('customer registration rejects an email and document already in use', function () {
    Customer::factory()->create([
        'document_type' => UserDocumentType::DNI,
        'document_number' => '87654321',
        'email' => 'existing@example.com',
    ]);

    $this->post(route('store.auth.register.store'), [
        'document_type' => UserDocumentType::DNI->value,
        'document_number' => '87654321',
        'name' => 'Rodrigo',
        'last_name' => 'Quispe',
        'email' => 'existing@example.com',
        'phone' => '963852741',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors(['document_number', 'email']);

    $this->assertGuest('store');
    expect(Customer::query()->count())->toBe(1);
});

test('customer registration enforces password restrictions', function (array $invalidData) {
    $this->post(route('store.auth.register.store'), [
        'document_type' => 'dni',
        'document_number' => '87654321',
        'name' => 'Rodrigo',
        'last_name' => 'Quispe',
        'email' => 'rodrigo.customer@example.com',
        'phone' => '963852741',
        'password' => 'password',
        'password_confirmation' => 'password',
        ...$invalidData,
    ])->assertSessionHasErrors('password');

    $this->assertGuest('store');
    expect(Customer::query()->count())->toBe(0);
})->with([
    'minimum length' => [[
        'password' => 'short',
        'password_confirmation' => 'short',
    ]],
    'confirmation' => [[
        'password_confirmation' => 'different-password',
    ]],
]);

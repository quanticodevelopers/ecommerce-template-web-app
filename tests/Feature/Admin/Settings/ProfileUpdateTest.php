<?php

use App\Enums\UserDocumentType;
use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()
        ->admin()
        ->create();

    $response = $this
        ->actingAs($user)
        ->get(route('admin.profile.edit'));

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()
        ->admin()
        ->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('admin.profile.update'), [
            'document_type' => 'dni',
            'document_number' => '87654321',
            'name' => 'Rodrigo',
            'last_name' => 'Quispe',
            'email' => 'rodrigo.admin@example.com',
            'phone' => '963852741',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.profile.edit'));

    $user->refresh();

    expect($user->document_type)->toBe(UserDocumentType::DNI)
        ->and($user->document_number)->toBe('87654321')
        ->and($user->name)->toBe('Rodrigo')
        ->and($user->last_name)->toBe('Quispe')
        ->and($user->email)->toBe('rodrigo.admin@example.com')
        ->and($user->email_verified_at)->toBeNull()
        ->and($user->phone)->toBe('963852741');
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()
        ->admin()
        ->create();

    $response = $this
        ->actingAs($user)
        ->patch(route('admin.profile.update'), [
            'document_type' => 'dni',
            'document_number' => '87654321',
            'name' => 'Rodrigo',
            'last_name' => 'Quispe',
            'email' => $user->email,
            'phone' => '963852741',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.profile.edit'));

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('user can delete their account', function () {
    $user = User::factory()
        ->admin()
        ->create();

    $response = $this
        ->actingAs($user)
        ->delete(route('admin.profile.destroy'), [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('store.home'));

    $this->assertGuest();
    expect($user->fresh())->toBeNull();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()
        ->admin()
        ->create();

    $response = $this
        ->actingAs($user)
        ->from(route('admin.profile.edit'))
        ->delete(route('admin.profile.destroy'), [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrors('password')
        ->assertRedirect(route('admin.profile.edit'));

    expect($user->fresh())->not->toBeNull();
});

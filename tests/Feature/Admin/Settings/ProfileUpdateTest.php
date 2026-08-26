<?php

use App\Models\Administrator;

test('profile page is displayed', function () {
    $user = Administrator::factory()
        ->create();

    $response = $this
        ->actingAs($user, 'admin')
        ->get(route('admin.profile.edit'));

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = Administrator::factory()
        ->create();

    $response = $this
        ->actingAs($user, 'admin')
        ->patch(route('admin.profile.update'), [
            'name' => 'Rodrigo',
            'last_name' => 'Quispe',
            'email' => 'rodrigo.admin@example.com',
            'phone' => '963852741',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.profile.edit'));

    $user->refresh();

    expect($user->name)->toBe('Rodrigo')
        ->and($user->last_name)->toBe('Quispe')
        ->and($user->email)->toBe('rodrigo.admin@example.com')
        ->and($user->phone)->toBe('963852741');
});

test('administrator email remains unchanged when the same address is submitted', function () {
    $user = Administrator::factory()
        ->create();

    $response = $this
        ->actingAs($user, 'admin')
        ->patch(route('admin.profile.update'), [
            'name' => 'Rodrigo',
            'last_name' => 'Quispe',
            'email' => $user->email,
            'phone' => '963852741',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.profile.edit'));

    expect($user->refresh()->email)->toBe($user->email);
});

test('account deletion is not available from profile settings', function () {
    $user = Administrator::factory()
        ->create();

    $this
        ->actingAs($user, 'admin')
        ->delete('/admin/settings/profile')
        ->assertMethodNotAllowed();

    $this->assertModelExists($user);
});

<?php

use App\Models\Administrator;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

test('security page requires password confirmation when enabled', function () {
    $user = Administrator::factory()
        ->create();

    $response = $this->actingAs($user, 'admin')
        ->get(route('admin.security.edit'));

    $response->assertRedirect(route('admin.auth.password.confirm'));
});

test('security page is displayed after password confirmation', function () {
    $user = Administrator::factory()
        ->create();

    $response = $this->actingAs($user, 'admin')
        ->withSession(['auth.password_confirmed_at' => now()->timestamp])
        ->get(route('admin.security.edit'));

    $response->assertOk();

    $response->assertInertia(fn (Assert $page) => $page
        ->component('admin/settings/security')
        ->has('passwordRules')
    );
});

test('password can be updated', function () {
    $user = Administrator::factory()
        ->create();

    $response = $this
        ->actingAs($user, 'admin')
        ->from(route('admin.security.edit'))
        ->put(route('admin.user-password.update'), [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.security.edit'));

    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});

test('correct password must be provided to update password', function () {
    $user = Administrator::factory()
        ->create();

    $response = $this
        ->actingAs($user, 'admin')
        ->from(route('admin.security.edit'))
        ->put(route('admin.user-password.update'), [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertSessionHasErrors('current_password')
        ->assertRedirect(route('admin.security.edit'));
});

<?php

use App\Models\Customer;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

test('reset password link screen can be rendered', function () {
    $response = $this->get(route('store.auth.password.request'));

    $response->assertOk();
});

test('reset password link can be requested', function () {
    Notification::fake();

    $user = Customer::factory()
        ->create();

    $this->post(route('store.auth.password.email'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class);
});

test('reset password screen can be rendered', function () {
    Notification::fake();

    $user = Customer::factory()
        ->create();

    $this->post(route('store.auth.password.email'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
        $response = $this->get(route('store.auth.password.reset', $notification->token));

        $response->assertOk();

        return true;
    });
});

test('password can be reset with valid token', function () {
    Notification::fake();

    $user = Customer::factory()->create();
    $previousPasswordHash = $user->password;

    $this->post(route('store.auth.password.email'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user, $previousPasswordHash) {
        $response = $this->post(route('store.auth.password.update'), [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('store.auth.login'));

        $user->refresh();

        expect(Hash::check('new-password', $user->password))->toBeTrue()
            ->and($user->password)->not->toBe($previousPasswordHash);

        return true;
    });
});

test('password cannot be reset with invalid token', function () {
    $user = Customer::factory()->create();
    $previousPasswordHash = $user->password;

    $response = $this->post(route('store.auth.password.update'), [
        'token' => 'invalid-token',
        'email' => $user->email,
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ]);

    $response->assertSessionHasErrors('email');

    expect($user->refresh()->password)->toBe($previousPasswordHash);
});

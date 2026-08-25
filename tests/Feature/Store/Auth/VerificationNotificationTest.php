<?php

use App\Models\Customer;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;

test('sends verification notification', function () {
    Notification::fake();

    $user = Customer::factory()
        ->unverified()
        ->create();

    $this->actingAs($user, 'store')
        ->post(route('store.verification.send'))
        ->assertRedirect(route('store.home'));

    Notification::assertSentTo($user, VerifyEmail::class);
});

test('does not send verification notification if email is verified', function () {
    Notification::fake();

    $user = Customer::factory()
        ->create();

    $this->actingAs($user, 'store')
        ->post(route('store.verification.send'))
        ->assertRedirect(route('store.home', absolute: false));

    Notification::assertNothingSent();
});

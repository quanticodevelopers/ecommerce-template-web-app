<?php

use App\Models\Customer;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

test('email verification screen can be rendered', function () {
    $user = Customer::factory()
        ->unverified()
        ->create();

    $response = $this
        ->actingAs($user, 'store')
        ->get(route('store.verification.notice'));

    $response->assertOk();
});

test('email can be verified', function () {
    $user = Customer::factory()
        ->unverified()
        ->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)],
    );

    $response = $this
        ->actingAs($user, 'store')
        ->get($verificationUrl);

    Event::assertDispatched(Verified::class);

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    $response->assertRedirect(route('store.home', absolute: false).'?verified=1');
});

test('email is not verified with invalid hash', function () {
    $user = Customer::factory()->unverified()->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1('wrong-email')],
    );

    $this->actingAs($user, 'store')->get($verificationUrl);

    Event::assertNotDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('email is not verified with invalid user id', function () {
    $user = Customer::factory()
        ->unverified()
        ->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => 123, 'hash' => sha1($user->email)],
    );

    $this->actingAs($user, 'store')->get($verificationUrl);

    Event::assertNotDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('verified customer user is redirected to dashboard from verification prompt', function () {
    $user = Customer::factory()
        ->create();

    Event::fake();

    $response = $this
        ->actingAs($user, 'store')
        ->get(route('store.verification.notice'));

    Event::assertNotDispatched(Verified::class);
    $response->assertRedirect(route('store.home', absolute: false));
});

test('already verified user visiting verification link is redirected without firing event again', function () {
    $user = Customer::factory()
        ->create();

    Event::fake();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        ['id' => $user->id, 'hash' => sha1($user->email)],
    );

    $this
        ->actingAs($user, 'store')
        ->get($verificationUrl)
        ->assertRedirect(route('store.home', absolute: false).'?verified=1');

    Event::assertNotDispatched(Verified::class);
    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

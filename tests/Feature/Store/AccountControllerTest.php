<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('account overview requires authentication', function () {
    $this->get(route('store.account.overview'))
        ->assertRedirect(route('store.auth.login'));
});

test('account overview requires a verified email', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('store.account.overview'))
        ->assertRedirect(route('store.verification.notice'));
});

test('verified customers can see their account overview', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('store.account.overview'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('store/account/overview'),
        );
});

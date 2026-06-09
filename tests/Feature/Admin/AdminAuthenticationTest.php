<?php

use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

beforeEach(function () {
    $this->withoutMiddleware(ValidateCsrfToken::class);
});

test('active admins can authenticate through the admin login', function () {
    $admin = User::factory()->create();

    $response = $this->from(route('admin.auth.login'))
        ->withSession(['auth.area' => 'admin'])
        ->post(route('login.store'), [
            '_token' => csrf_token(),
            'email' => $admin->email,
            'password' => 'password',
        ]);

    $this->assertAuthenticatedAs($admin);
    $response->assertRedirect(route('admin.dashboard', absolute: false));
});

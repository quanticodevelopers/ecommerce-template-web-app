<?php

use App\Models\Administrator;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the admin login page when accessing dashboard', function () {
    $response = $this->get(route('admin.dashboard'));

    $response->assertRedirect(route('admin.auth.login'));
});

test('admins can visit the dashboard', function () {
    $user = Administrator::factory()
        ->create();
    $this->actingAs($user, 'admin');

    $response = $this->get(route('admin.dashboard'));
    $response
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/dashboard/index')
            ->where('auth.user.id', $user->id)
            ->where('auth.user.name', $user->name)
            ->where('auth.user.last_name', $user->last_name)
            ->where('auth.user.document_type.value', $user->document_type->value)
            ->where('auth.user.document_type.label', $user->document_type->label())
            ->where('auth.user.role.value', $user->role->value)
            ->where('auth.user.role.label', $user->role->label())
        );
});

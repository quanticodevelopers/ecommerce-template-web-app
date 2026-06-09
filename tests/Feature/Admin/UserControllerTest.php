<?php

use App\Enums\UserRole;
use App\Http\Controllers\Admin\UserController;
use App\Http\Requests\Admin\ConfirmAdminPasswordRequest;
use App\Http\Requests\Admin\StoreAdminUserRequest;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->withoutMiddleware(ValidateCsrfToken::class);
});

test('guests are redirected to the admin login page', function () {
    $response = $this->get(route('admin.users.index'));

    $response->assertRedirect(route('admin.auth.login'));
});

test('admins can see only administrator users', function () {
    $admin = User::factory()->create();
    $customer = User::factory()->state([
        'role' => UserRole::CUSTOMER->value,
    ])->create();

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/users/index')
            ->has('users', 1)
            ->where('users.0.email', $admin->email)
            ->where('users.0.role.value', UserRole::ADMIN->value)
            ->where('users.0.role.label', UserRole::ADMIN->label())
            ->where('users.0.name', $admin->name)
            ->where('users.0.last_name', $admin->last_name)
            ->missing('users.0.is_active')
            ->missing('current_user_id')
            ->missing('users.0.email_verified')
            ->missing('users.0.email_verified_at')
            ->missing('users.1'),
        );

    expect($customer->refresh()->role)->toBe(UserRole::CUSTOMER);
});

test('admins can create administrator users with generated credentials', function () {
    $admin = User::factory()->create();

    $payload = [
        'document_type' => 'dni',
        'document_number' => '12345678',
        'name' => 'Daniel',
        'last_name' => 'Perez',
        'email' => 'daniel.admin@example.com',
        'phone' => '987654321',
    ];

    $request = StoreAdminUserRequest::create(route('admin.users.store'), 'POST', $payload);
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));
    $request->setUserResolver(fn () => $admin);
    $request->validateResolved();

    $response = app(UserController::class)->store($request);

    expect($response->getTargetUrl())->toBe(route('admin.users.index'));

    $createdUser = User::query()->where('email', $payload['email'])->first();

    expect($createdUser)->not->toBeNull();
    expect($createdUser->role)->toBe(UserRole::ADMIN);
    expect($createdUser->email_verified_at)->not->toBeNull();

    $credentials = $response->getSession()->get('created_user_credentials');

    expect($credentials)->toBeArray();
    expect($credentials['email'])->toBe($payload['email']);
    expect($credentials['password'])->toHaveLength(18);
    expect($credentials['password'])->toMatch('/^[a-zA-Z0-9]+$/');
    expect(Hash::check($credentials['password'], $createdUser->password))->toBeTrue();
});

test('admins can reset another administrator password with generated credentials', function () {
    $admin = User::factory()->create();
    $targetAdmin = User::factory()->create();
    $previousPasswordHash = $targetAdmin->password;

    $request = ConfirmAdminPasswordRequest::create(route('admin.users.reset-password', $targetAdmin), 'PATCH', [
        'password' => 'password',
    ]);
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));
    $request->setUserResolver(fn () => $admin);
    $this->actingAs($admin);
    $request->validateResolved();

    $response = app(UserController::class)->resetPassword($request, $targetAdmin);

    expect($response->getTargetUrl())->toBe(route('admin.users.index'));

    $credentials = $response->getSession()->get('created_user_credentials');
    $updatedAdmin = $targetAdmin->refresh();

    expect($credentials)->toBeArray();
    expect($credentials['name'])->toBe(trim($updatedAdmin->name.' '.$updatedAdmin->last_name));
    expect($credentials['email'])->toBe($updatedAdmin->email);
    expect($credentials['password'])->toHaveLength(18);
    expect($credentials['password'])->toMatch('/^[a-zA-Z0-9]+$/');
    expect(Hash::check($credentials['password'], $updatedAdmin->password))->toBeTrue();
    expect($updatedAdmin->password)->not->toBe($previousPasswordHash);
});

test('admins must confirm password to reset an administrator password', function () {
    $admin = User::factory()->create();
    $targetAdmin = User::factory()->create();
    $previousPasswordHash = $targetAdmin->password;

    $request = ConfirmAdminPasswordRequest::create(route('admin.users.reset-password', $targetAdmin), 'PATCH', [
        'password' => 'incorrect-password',
    ]);
    $request->setContainer(app());
    $request->setRedirector(app('redirect'));
    $request->setUserResolver(fn () => $admin);
    $this->actingAs($admin);

    try {
        $request->validateResolved();
        expect(true)->toBeFalse();
    } catch (ValidationException $exception) {
        expect($exception->errors())->toHaveKey('password');
    }

    expect($targetAdmin->refresh()->password)->toBe($previousPasswordHash);
});

test('administrator activation routes were removed', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin)
        ->patch("/admin/users/{$admin->id}/deactivate")
        ->assertNotFound();

    $this->actingAs($admin)
        ->patch("/admin/users/{$admin->id}/reactivate")
        ->assertNotFound();
});

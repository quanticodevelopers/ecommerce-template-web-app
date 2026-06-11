<?php

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    SiteSetting::setMany(SiteSetting::defaults());
});

afterEach(function () {
    SiteSetting::forgetCache();
});

test('information page is displayed to admins', function () {
    $user = User::factory()
        ->admin()
        ->create();

    $response = $this
        ->actingAs($user)
        ->get(route('admin.information.edit'));

    $response->assertOk();

    $response->assertInertia(fn (Assert $page) => $page
        ->component('admin/settings/information')
        ->has('settings'));
});

test('information page is forbidden to non-admin users', function () {
    $user = User::factory()
        ->create();

    $this
        ->actingAs($user)
        ->get(route('admin.information.edit'))
        ->assertForbidden();
});

test('site information can be updated', function () {
    $user = User::factory()
        ->admin()
        ->create();

    $response = $this
        ->actingAs($user)
        ->from(route('admin.information.edit'))
        ->post(route('admin.information.update'), [
            '_method' => 'PUT',
            'site_name' => 'Tienda Norte',
            'site_description' => 'Texto descriptivo del sitio.',
            'site_keywords' => 'tienda, ecommerce, peru',
            'footer_credit_name' => 'Equipo Norte',
            'email' => 'hola@tiendanorte.com',
            'phone' => '987654321',
            'address' => 'Av. Principal 123',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.information.edit'));

    expect(SiteSetting::value(SiteSetting::KEY_SITE_NAME))->toBe('Tienda Norte')
        ->and(SiteSetting::value(SiteSetting::KEY_SITE_DESCRIPTION))->toBe('Texto descriptivo del sitio.')
        ->and(SiteSetting::value(SiteSetting::KEY_SITE_KEYWORDS))->toBe('tienda, ecommerce, peru')
        ->and(SiteSetting::value(SiteSetting::KEY_FOOTER_CREDIT_NAME))->toBe('Equipo Norte')
        ->and(SiteSetting::value(SiteSetting::KEY_EMAIL))->toBe('hola@tiendanorte.com')
        ->and(SiteSetting::value(SiteSetting::KEY_PHONE))->toBe('987654321')
        ->and(SiteSetting::value(SiteSetting::KEY_ADDRESS))->toBe('Av. Principal 123');
});

test('site logo can be uploaded as a png with 512 by 512 dimensions', function () {
    Storage::fake('public');

    $user = User::factory()
        ->admin()
        ->create();

    SiteSetting::setMany([
        ...SiteSetting::defaults(),
        SiteSetting::KEY_LOGO_PATH => null,
    ]);

    $response = $this
        ->actingAs($user)
        ->post(route('admin.information.update'), [
            '_method' => 'PUT',
            'logo' => UploadedFile::fake()->image('logo.png', 512, 512),
            'site_name' => 'Tienda Norte',
            'site_description' => 'Texto descriptivo del sitio.',
            'site_keywords' => 'tienda, ecommerce, peru',
            'footer_credit_name' => 'Equipo Norte',
            'email' => 'hola@tiendanorte.com',
            'phone' => '987654321',
            'address' => 'Av. Principal 123',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.information.edit'));

    $logoPath = SiteSetting::value(SiteSetting::KEY_LOGO_PATH);

    expect($logoPath)->not->toBeNull();
    Storage::disk('public')->assertExists($logoPath);
});

test('site logo can be uploaded as an svg with 512 by 512 dimensions', function () {
    Storage::fake('public');

    $user = User::factory()
        ->admin()
        ->create();

    SiteSetting::setMany([
        ...SiteSetting::defaults(),
        SiteSetting::KEY_LOGO_PATH => null,
    ]);

    $svg = <<<'SVG'
<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512">
    <rect width="512" height="512" fill="#000000" />
</svg>
SVG;

    $response = $this
        ->actingAs($user)
        ->post(route('admin.information.update'), [
            '_method' => 'PUT',
            'logo' => UploadedFile::fake()->createWithContent('logo.svg', $svg),
            'site_name' => 'Tienda Norte',
            'site_description' => 'Texto descriptivo del sitio.',
            'site_keywords' => 'tienda, ecommerce, peru',
            'footer_credit_name' => 'Equipo Norte',
            'email' => 'hola@tiendanorte.com',
            'phone' => '987654321',
            'address' => 'Av. Principal 123',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.information.edit'));

    $logoPath = SiteSetting::value(SiteSetting::KEY_LOGO_PATH);

    expect($logoPath)->not->toBeNull();
    Storage::disk('public')->assertExists($logoPath);
});

test('site logo must have the required dimensions', function () {
    Storage::fake('public');

    $user = User::factory()
        ->admin()
        ->create();

    SiteSetting::setMany(SiteSetting::defaults());

    $response = $this
        ->actingAs($user)
        ->from(route('admin.information.edit'))
        ->post(route('admin.information.update'), [
            '_method' => 'PUT',
            'logo' => UploadedFile::fake()->image('logo.png', 400, 400),
            'site_name' => 'Tienda Norte',
            'site_description' => 'Texto descriptivo del sitio.',
            'site_keywords' => 'tienda, ecommerce, peru',
            'footer_credit_name' => 'Equipo Norte',
            'email' => 'hola@tiendanorte.com',
            'phone' => '987654321',
            'address' => 'Av. Principal 123',
        ]);

    $response
        ->assertSessionHasErrors('logo')
        ->assertRedirect(route('admin.information.edit'));
});

test('existing logo can be removed', function () {
    Storage::fake('public');

    $user = User::factory()
        ->admin()
        ->create();

    SiteSetting::setMany([
        ...SiteSetting::defaults(),
        SiteSetting::KEY_LOGO_PATH => 'site-settings/logo.png',
    ]);

    Storage::disk('public')->put('site-settings/logo.png', 'logo');

    $response = $this
        ->actingAs($user)
        ->post(route('admin.information.update'), [
            '_method' => 'PUT',
            'remove_logo' => '1',
            'site_name' => 'Tienda Norte',
            'site_description' => 'Texto descriptivo del sitio.',
            'site_keywords' => 'tienda, ecommerce, peru',
            'footer_credit_name' => 'Equipo Norte',
            'email' => 'hola@tiendanorte.com',
            'phone' => '987654321',
            'address' => 'Av. Principal 123',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('admin.information.edit'));

    expect(SiteSetting::value(SiteSetting::KEY_LOGO_PATH))->toBeNull();
    Storage::disk('public')->assertMissing('site-settings/logo.png');
});

<?php

use App\Models\Brand;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the admin login page when accessing brands index', function () {
    $response = $this->get(route('admin.brands.index'));

    $response->assertRedirect(route('admin.auth.login'));
});

test('admins can see the brands index', function () {
    $admin = User::factory()
        ->admin()
        ->create();

    Brand::factory()->create(['name' => 'Nike']);
    Brand::factory()->inactive()->create(['name' => 'Adidas']);

    $this->actingAs($admin)
        ->get(route('admin.brands.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/brands/index')
            ->has('brands', 2)
            ->where('brands.0.name', 'Adidas')
            ->where('brands.0.code', fn (string $code): bool => Str::startsWith($code, 'BR'))
            ->where('brands.0.is_active', false)
            ->where('brands.1.name', 'Nike')
            ->where('brands.1.is_active', true)
            ->missing('brands.2'),
        );
});

test('guests are redirected to the admin login page when creating brands', function () {
    $response = $this->post(route('admin.brands.store'), [
        'name' => 'Marca Invitada',
        'short_description' => 'Descripción',
        'logo' => UploadedFile::fake()->image('logo.jpg', 600, 400),
    ]);

    $response->assertRedirect(route('admin.auth.login'));
});

test('guests are redirected to the admin login page when updating brands', function () {
    $brand = Brand::factory()->create([
        'name' => 'Puma Peru',
        'code' => 'BR1234',
        'logo_path' => 'images/brands/brand-puma-peru-BR1234.webp',
    ]);

    $response = $this->patch(route('admin.brands.update', $brand), [
        'name' => 'Puma Actualizada',
        'short_description' => 'Ropa deportiva',
        'is_active' => true,
    ]);

    $response->assertRedirect(route('admin.auth.login'));
});

test('admins can create a brand with a logo stored as webp', function () {
    Storage::fake('public');

    $admin = User::factory()
        ->admin()
        ->create();

    $response = $this->actingAs($admin)
        ->post(route('admin.brands.store'), [
            'name' => 'Puma Peru',
            'short_description' => 'Ropa deportiva',
            'logo' => UploadedFile::fake()->image('logo.jpg', 600, 400),
        ]);

    $response->assertRedirect(route('admin.brands.index'));
    $response->assertInertiaFlash('toast.type', 'success');
    $response->assertInertiaFlash('toast.message', __('actions.brands.created'));

    $brand = Brand::query()
        ->where('name', 'Puma Peru')
        ->firstOrFail();

    expect($brand->slug)->toBe('puma-peru')
        ->and($brand->code)->toMatch('/^BR[A-Z0-9]{4}$/')
        ->and($brand->is_active)->toBeTrue()
        ->and($brand->short_description)->toBe('Ropa deportiva')
        ->and($brand->logo_path)->toBe("images/brands/brand-{$brand->slug}-{$brand->code}.webp");

    Storage::disk('public')->assertExists($brand->logo_path);
});

test('admins can update a brand without changing its logo', function () {
    Storage::fake('public');

    $admin = User::factory()
        ->admin()
        ->create();

    $brand = Brand::factory()->create([
        'name' => 'Puma Peru',
        'short_description' => 'Ropa deportiva',
        'code' => 'BR1234',
        'logo_path' => 'images/brands/brand-puma-peru-BR1234.webp',
        'is_active' => true,
    ]);

    Storage::disk('public')->put($brand->logo_path, 'old-logo');

    $response = $this->actingAs($admin)
        ->patch(route('admin.brands.update', $brand), [
            'name' => 'Puma Actualizada',
            'short_description' => 'Texto actualizado',
            'is_active' => false,
        ]);

    $response->assertRedirect(route('admin.brands.index'));
    $response->assertInertiaFlash('toast.type', 'success');
    $response->assertInertiaFlash('toast.message', __('actions.brands.updated'));

    $brand->refresh();

    expect($brand->name)->toBe('Puma Actualizada')
        ->and($brand->short_description)->toBe('Texto actualizado')
        ->and($brand->is_active)->toBeFalse()
        ->and($brand->logo_path)->toBe('images/brands/brand-puma-peru-BR1234.webp');

    Storage::disk('public')->assertExists($brand->logo_path);
    expect(Storage::disk('public')->get($brand->logo_path))->toBe('old-logo');
});

test('admins can replace a brand logo when updating', function () {
    Storage::fake('public');

    $admin = User::factory()
        ->admin()
        ->create();

    $brand = Brand::factory()->create([
        'name' => 'Puma Peru',
        'short_description' => 'Ropa deportiva',
        'code' => 'BR1234',
        'logo_path' => 'images/brands/brand-puma-peru-BR1234.webp',
        'is_active' => true,
    ]);

    Storage::disk('public')->put($brand->logo_path, 'old-logo');

    $response = $this->actingAs($admin)
        ->patch(route('admin.brands.update', $brand), [
            'name' => 'Puma Peru',
            'short_description' => 'Ropa deportiva',
            'is_active' => true,
            'logo' => UploadedFile::fake()->image('logo.png', 600, 400),
        ]);

    $response->assertRedirect(route('admin.brands.index'));
    $response->assertInertiaFlash('toast.type', 'success');
    $response->assertInertiaFlash('toast.message', __('actions.brands.updated'));

    $brand->refresh();

    expect($brand->logo_path)->toBe('images/brands/brand-puma-peru-BR1234.webp');
    Storage::disk('public')->assertExists($brand->logo_path);
    expect(Storage::disk('public')->get($brand->logo_path))->not->toBe('old-logo');
});

test('admins must provide a brand name when creating brands', function () {
    $admin = User::factory()
        ->admin()
        ->create();

    $this->actingAs($admin)
        ->post(route('admin.brands.store'), [
            'name' => '',
            'short_description' => 'Ropa deportiva',
            'logo' => UploadedFile::fake()->image('logo.jpg', 600, 400),
        ])
        ->assertSessionHasErrors('name');
});

test('admins must provide a brand logo when creating brands', function () {
    $admin = User::factory()
        ->admin()
        ->create();

    $this->actingAs($admin)
        ->post(route('admin.brands.store'), [
            'name' => 'Puma Peru',
            'short_description' => 'Ropa deportiva',
        ])
        ->assertSessionHasErrors('logo');
});

test('admins must keep brand short descriptions within the configured limit when creating brands', function () {
    $admin = User::factory()
        ->admin()
        ->create();

    $this->actingAs($admin)
        ->post(route('admin.brands.store'), [
            'name' => 'Puma Peru',
            'short_description' => str_repeat('a', 129),
            'logo' => UploadedFile::fake()->image('logo.jpg', 600, 400),
        ])
        ->assertSessionHasErrors('short_description');
});

test('admins must provide a valid status when updating brands', function () {
    $admin = User::factory()
        ->admin()
        ->create();

    $brand = Brand::factory()->create([
        'name' => 'Puma Peru',
        'code' => 'BR1234',
        'logo_path' => 'images/brands/brand-puma-peru-BR1234.webp',
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.brands.update', $brand), [
            'name' => 'Puma Peru',
            'short_description' => 'Ropa deportiva',
            'is_active' => 'maybe',
        ])
        ->assertSessionHasErrors('is_active');
});

test('admins must provide a valid brand logo when replacing it', function () {
    $admin = User::factory()
        ->admin()
        ->create();

    $brand = Brand::factory()->create([
        'name' => 'Puma Peru',
        'code' => 'BR1234',
        'logo_path' => 'images/brands/brand-puma-peru-BR1234.webp',
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.brands.update', $brand), [
            'name' => 'Puma Peru',
            'short_description' => 'Ropa deportiva',
            'is_active' => true,
            'logo' => UploadedFile::fake()->create('logo.txt', 2, 'text/plain'),
        ])
        ->assertSessionHasErrors('logo');
});

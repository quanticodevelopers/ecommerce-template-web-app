<?php

use App\Models\Brand;
use App\Models\User;
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

test('brands generate slug and code automatically', function () {
    $brand = Brand::factory()->create([
        'name' => 'Puma Peru',
    ]);

    expect($brand->slug)->toBe('puma-peru')
        ->and($brand->code)->toMatch('/^BR[A-Z0-9]{4}$/')
        ->and(Str::startsWith($brand->code, 'BR'))->toBeTrue();
});

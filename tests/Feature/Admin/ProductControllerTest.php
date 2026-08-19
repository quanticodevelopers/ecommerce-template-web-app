<?php

use App\Enums\ProductFlag;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the admin login page when accessing products index', function () {
    $this->get(route('admin.products.index'))
        ->assertRedirect(route('admin.auth.login'));
});

test('admins can see a paginated product listing', function () {
    $admin = User::factory()->admin()->create();
    $brand = Brand::factory()->create(['name' => 'Acme']);
    $category = Category::factory()->create(['name' => 'Calzado']);

    Product::factory()->count(51)->recycle($brand)->recycle($category)->create();

    $this->actingAs($admin)
        ->get(route('admin.products.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/products/index')
            ->has('products.data', 50)
            ->where('products.meta.per_page', 50)
            ->where('products.meta.total', 51)
            ->where('products.meta.last_page', 2)
            ->where('filters.search', ''),
        );
});

test('admins can search products by sku name or barcode', function (string $search) {
    $admin = User::factory()->admin()->create();
    $brand = Brand::factory()->create();
    $category = Category::factory()->create();

    Product::factory()->recycle($brand)->recycle($category)->create([
        'sku' => 'SKU-ALPHA-001',
        'barcode' => '7751234567890',
        'name' => 'Zapatilla Boreal',
    ]);

    Product::factory()->recycle($brand)->recycle($category)->create([
        'sku' => 'SKU-BETA-002',
        'barcode' => '7750987654321',
        'name' => 'Mochila Urbana',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.products.index', ['search' => $search]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('products.data', 1)
            ->where('products.data.0.name', 'Zapatilla Boreal')
            ->where('filters.search', $search),
        );
})->with([
    'sku' => 'ALPHA-001',
    'name' => 'Boreal',
    'barcode' => '7751234567890',
]);

test('the product listing includes classification pricing publication and its primary thumbnail', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();
    $brand = Brand::factory()->create(['name' => 'Andes']);
    $category = Category::factory()->create(['name' => 'Accesorios']);
    $product = Product::factory()->published()->for($brand)->for($category)->create([
        'name' => 'Gorra Técnica',
        'sku' => 'GORRA-001',
        'base_price' => '99.90',
        'sale_price' => '79.90',
        'flag' => ProductFlag::FEATURED,
    ]);

    ProductImage::factory()->for($product)->create([
        'path' => 'images/products/xl/gorra.webp',
        'variants' => [
            'md' => 'images/products/md/gorra.webp',
            'sm' => 'images/products/sm/gorra.webp',
        ],
        'alt' => 'Gorra técnica azul',
        'position' => 0,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.products.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('products.data.0.name', 'Gorra Técnica')
            ->where('products.data.0.brand.name', 'Andes')
            ->where('products.data.0.category.name', 'Accesorios')
            ->where('products.data.0.base_price', '99.90')
            ->where('products.data.0.sale_price', '79.90')
            ->where('products.data.0.flag', ProductFlag::FEATURED->value)
            ->where('products.data.0.thumbnail.alt', 'Gorra técnica azul')
            ->where('products.data.0.thumbnail.url', Storage::disk('public')->url('images/products/sm/gorra.webp')),
        );
});

test('products use ulids enum casts relationships and a slug sourced from name and sku', function () {
    $brand = Brand::factory()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->for($brand)->for($category)->create([
        'name' => 'Casaca Impermeable',
        'sku' => 'CASACA-001',
        'flag' => ProductFlag::NEW,
    ]);

    $image = ProductImage::factory()->for($product)->create();

    expect(Str::isUlid($product->id))->toBeTrue()
        ->and(Str::isUlid($image->id))->toBeTrue()
        ->and($product->slug)->toStartWith('casaca-impermeable-casaca-001')
        ->and($product->flag)->toBe(ProductFlag::NEW)
        ->and($product->brand->is($brand))->toBeTrue()
        ->and($product->category->is($category))->toBeTrue()
        ->and($image->product->is($product))->toBeTrue();
});

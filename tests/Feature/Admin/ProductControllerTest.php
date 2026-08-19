<?php

use App\Enums\ProductFlag;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the admin login page when accessing products index', function () {
    $this->get(route('admin.products.index'))
        ->assertRedirect(route('admin.auth.login'));
});

test('guests are redirected to the admin login page when accessing product creation', function () {
    $this->get(route('admin.products.create'))
        ->assertRedirect(route('admin.auth.login'));
});

test('admins can see the product creation page with active catalog options', function () {
    $admin = User::factory()->admin()->create();
    $activeBrand = Brand::factory()->active()->create(['name' => 'Marca activa']);
    Brand::factory()->inactive()->create(['name' => 'Marca inactiva']);
    $activeCategory = Category::factory()->active()->create(['name' => 'Categoría activa']);
    Category::factory()->inactive()->create(['name' => 'Categoría inactiva']);

    $this->actingAs($admin)
        ->get(route('admin.products.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/products/create')
            ->has('brands', 1)
            ->where('brands.0.id', $activeBrand->id)
            ->where('brands.0.name', 'Marca activa')
            ->has('categories', 1)
            ->where('categories.0.id', $activeCategory->id)
            ->where('categories.0.name', 'Categoría activa'),
        );
});

test('admins can create a product with sanitized content and ordered avif images', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();
    $brand = Brand::factory()->active()->create();
    $category = Category::factory()->active()->create();

    $response = $this->actingAs($admin)->post(route('admin.products.store'), [
        'name' => 'Mochila de viaje',
        'sku' => 'MOCHILA-001',
        'barcode' => '7751234567890',
        'brand_id' => $brand->id,
        'category_id' => $category->id,
        'short_description' => 'Mochila ligera para viajes cortos.',
        'description' => '<h2 style="text-align: center; color: red">Detalles</h2><p><strong>Ligera</strong> y cómoda.</p><script>alert(1)</script><a href="javascript:alert(1)">Enlace</a>',
        'base_price' => '149.90',
        'sale_price' => '119.90',
        'flag' => ProductFlag::FEATURED->value,
        'is_draft' => true,
        'images' => [
            UploadedFile::fake()->image('frontal.jpg', 900, 600),
            UploadedFile::fake()->image('posterior.png', 600, 900),
        ],
    ]);

    $response
        ->assertRedirect(route('admin.products.index'))
        ->assertInertiaFlash('toast.message', __('actions.products.created'));

    $product = Product::query()->where('sku', 'MOCHILA-001')->firstOrFail();
    $images = $product->images()->get();

    expect($product->brand->is($brand))->toBeTrue()
        ->and($product->category->is($category))->toBeTrue()
        ->and($product->flag)->toBe(ProductFlag::FEATURED)
        ->and($product->published_at)->toBeNull()
        ->and($product->description)->toContain('<strong>Ligera</strong>')
        ->and($product->description)->toContain('text-align: center')
        ->and($product->description)->not->toContain('<script')
        ->and($product->description)->not->toContain('javascript:')
        ->and($images)->toHaveCount(2)
        ->and($images->pluck('position')->all())->toBe([0, 1])
        ->and($images[0]->path)->toEndWith('/xl/image-01.avif')
        ->and($images[1]->path)->toEndWith('/xl/image-02.avif')
        ->and(array_keys($images[0]->variants))->toBe(['md', 'sm']);

    foreach ($images as $image) {
        Storage::disk('public')->assertExists($image->path);

        foreach ($image->variants as $variantPath) {
            Storage::disk('public')->assertExists($variantPath);
        }
    }

    $primaryImagePath = Storage::disk('public')->path($images[0]->path);
    $primaryImageSize = getimagesize($primaryImagePath);

    expect(exif_imagetype($primaryImagePath))->toBe(IMAGETYPE_AVIF)
        ->and($primaryImageSize)->not->toBeFalse()
        ->and($primaryImageSize[0])->toBe(1200)
        ->and($primaryImageSize[1])->toBe(1200);
});

test('product creation requires between one and five valid images', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();
    $brand = Brand::factory()->create();
    $category = Category::factory()->create();
    $payload = [
        'name' => 'Producto sin imágenes válidas',
        'sku' => 'INVALID-IMAGES',
        'barcode' => '7751234567890',
        'brand_id' => $brand->id,
        'category_id' => $category->id,
        'sale_price' => '10.00',
        'is_draft' => false,
    ];

    $this->actingAs($admin)
        ->post(route('admin.products.store'), $payload)
        ->assertSessionHasErrors('images');

    $this->actingAs($admin)
        ->post(route('admin.products.store'), [
            ...$payload,
            'images' => array_map(
                fn (int $index): UploadedFile => UploadedFile::fake()->image("image-{$index}.jpg", 100, 100),
                range(1, 6),
            ),
        ])
        ->assertSessionHasErrors('images');

    $this->actingAs($admin)
        ->post(route('admin.products.store'), [
            ...$payload,
            'images' => [UploadedFile::fake()->create('document.txt', 10, 'text/plain')],
        ])
        ->assertSessionHasErrors('images.0');

    expect(Product::query()->where('sku', 'INVALID-IMAGES')->exists())->toBeFalse();
});

test('the base price must be greater than the sale price when provided', function (string $basePrice) {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();
    $brand = Brand::factory()->active()->create();
    $category = Category::factory()->active()->create();

    $this->actingAs($admin)
        ->post(route('admin.products.store'), [
            'name' => 'Producto con precio inválido',
            'sku' => 'INVALID-PRICE',
            'barcode' => '7751234567890',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'base_price' => $basePrice,
            'sale_price' => '100.00',
            'is_draft' => false,
            'images' => [UploadedFile::fake()->image('product.jpg', 100, 100)],
        ])
        ->assertSessionHasErrors([
            'base_price' => 'El precio regular debe ser mayor que el precio de venta.',
        ]);

    expect(Product::query()->where('sku', 'INVALID-PRICE')->exists())->toBeFalse();
})->with([
    'equal to the sale price' => '100.00',
    'lower than the sale price' => '99.99',
]);

test('the base price remains optional when creating a product', function () {
    Storage::fake('public');

    $admin = User::factory()->admin()->create();
    $brand = Brand::factory()->active()->create();
    $category = Category::factory()->active()->create();

    $this->actingAs($admin)
        ->post(route('admin.products.store'), [
            'name' => 'Producto sin precio regular',
            'sku' => 'NO-BASE-PRICE',
            'barcode' => '7751234567890',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'sale_price' => '100.00',
            'is_draft' => true,
            'images' => [UploadedFile::fake()->image('product.jpg', 100, 100)],
        ])
        ->assertRedirect(route('admin.products.index'));

    $product = Product::query()->where('sku', 'NO-BASE-PRICE')->firstOrFail();

    expect($product->base_price)->toBeNull();
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

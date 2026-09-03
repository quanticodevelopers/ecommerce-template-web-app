<?php

use App\Enums\ProductFlag;
use App\Models\Administrator;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
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

test('guests are redirected to the admin login page when accessing product details', function () {
    $product = Product::factory()->create();

    $this->get(route('admin.products.show', $product))
        ->assertRedirect(route('admin.auth.login'));
});

test('admins can see the product creation page with active catalog options', function () {
    $admin = Administrator::factory()->create();
    $activeBrand = Brand::factory()->active()->create(['name' => 'Marca activa']);
    Brand::factory()->inactive()->create(['name' => 'Marca inactiva']);
    $activeCategory = Category::factory()->active()->create(['name' => 'Categoría activa']);
    Category::factory()->inactive()->create(['name' => 'Categoría inactiva']);

    $this->actingAs($admin, 'admin')
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

    $admin = Administrator::factory()->create();
    $brand = Brand::factory()->active()->create();
    $category = Category::factory()->active()->create();

    $response = $this->actingAs($admin, 'admin')->post(route('admin.products.store'), [
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
            ['file' => UploadedFile::fake()->image('frontal.jpg', 900, 600)],
            ['file' => UploadedFile::fake()->image('posterior.png', 600, 900)],
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
        ->and($images[0]->path)->toContain('/xl/')
        ->and($images[0]->path)->toEndWith('.avif')
        ->and($images[1]->path)->toContain('/xl/')
        ->and($images[1]->path)->toEndWith('.avif')
        ->and($images[0]->path)->not->toBe($images[1]->path)
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

    $admin = Administrator::factory()->create();
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

    $this->actingAs($admin, 'admin')
        ->post(route('admin.products.store'), $payload)
        ->assertSessionHasErrors('images');

    $this->actingAs($admin, 'admin')
        ->post(route('admin.products.store'), [
            ...$payload,
            'images' => array_map(
                fn (int $index): array => ['file' => UploadedFile::fake()->image("image-{$index}.jpg", 100, 100)],
                range(1, 6),
            ),
        ])
        ->assertSessionHasErrors('images');

    $this->actingAs($admin, 'admin')
        ->post(route('admin.products.store'), [
            ...$payload,
            'images' => [['file' => UploadedFile::fake()->create('document.txt', 10, 'text/plain')]],
        ])
        ->assertSessionHasErrors('images.0.file');

    expect(Product::query()->where('sku', 'INVALID-IMAGES')->exists())->toBeFalse();
});

test('the base price must be greater than the sale price when provided', function (string $basePrice) {
    Storage::fake('public');

    $admin = Administrator::factory()->create();
    $brand = Brand::factory()->active()->create();
    $category = Category::factory()->active()->create();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.products.store'), [
            'name' => 'Producto con precio inválido',
            'sku' => 'INVALID-PRICE',
            'barcode' => '7751234567890',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'base_price' => $basePrice,
            'sale_price' => '100.00',
            'is_draft' => false,
            'images' => [['file' => UploadedFile::fake()->image('product.jpg', 100, 100)]],
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

    $admin = Administrator::factory()->create();
    $brand = Brand::factory()->active()->create();
    $category = Category::factory()->active()->create();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.products.store'), [
            'name' => 'Producto sin precio regular',
            'sku' => 'NO-BASE-PRICE',
            'barcode' => '7751234567890',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'sale_price' => '100.00',
            'is_draft' => true,
            'images' => [['file' => UploadedFile::fake()->image('product.jpg', 100, 100)]],
        ])
        ->assertRedirect(route('admin.products.index'));

    $product = Product::query()->where('sku', 'NO-BASE-PRICE')->firstOrFail();

    expect($product->base_price)->toBeNull();
});

test('product creation rejects inactive catalog relations', function () {
    Storage::fake('public');

    $admin = Administrator::factory()->create();
    $brand = Brand::factory()->inactive()->create();
    $category = Category::factory()->inactive()->create();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.products.store'), [
            'name' => 'Producto inválido',
            'sku' => 'INACTIVE-RELATIONS',
            'barcode' => '7751234567890',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'sale_price' => '100.00',
            'is_draft' => true,
            'images' => [['file' => UploadedFile::fake()->image('product.jpg')]],
        ])
        ->assertSessionHasErrors(['brand_id', 'category_id']);

    expect(Product::query()->where('sku', 'INACTIVE-RELATIONS')->exists())->toBeFalse();
});

test('product creation requires unique sku and barcode values', function () {
    Storage::fake('public');

    $admin = Administrator::factory()->create();
    $brand = Brand::factory()->active()->create();
    $category = Category::factory()->active()->create();
    $existingProduct = Product::factory()->for($brand)->for($category)->create();

    $this->actingAs($admin, 'admin')
        ->post(route('admin.products.store'), [
            'name' => 'Producto duplicado',
            'sku' => $existingProduct->sku,
            'barcode' => $existingProduct->barcode,
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'sale_price' => '100.00',
            'is_draft' => true,
            'images' => [['file' => UploadedFile::fake()->image('product.jpg')]],
        ])
        ->assertSessionHasErrors(['sku', 'barcode']);

    expect(Product::query()->count())->toBe(1);
});

test('admins can see every product detail with ordered images and timestamps', function () {
    Storage::fake('public');

    $admin = Administrator::factory()->create();
    $brand = Brand::factory()->create(['name' => 'Andes']);
    $category = Category::factory()->create(['name' => 'Accesorios']);
    $product = Product::factory()->published()->for($brand)->for($category)->create([
        'name' => 'Mochila Técnica',
        'sku' => 'MOCHILA-001',
        'barcode' => '7751234567890',
        'short_description' => 'Lista para rutas largas.',
        'description' => '<h2>Detalles</h2><p>Material impermeable.</p>',
        'base_price' => '199.90',
        'sale_price' => '149.90',
        'flag' => ProductFlag::FEATURED,
    ]);
    $secondImage = ProductImage::factory()->for($product)->create([
        'path' => "images/products/{$product->id}/xl/second.avif",
        'variants' => ['sm' => "images/products/{$product->id}/sm/second.avif"],
        'alt' => 'Vista posterior',
        'position' => 1,
    ]);
    $firstImage = ProductImage::factory()->for($product)->create([
        'path' => "images/products/{$product->id}/xl/first.avif",
        'variants' => ['sm' => "images/products/{$product->id}/sm/first.avif"],
        'alt' => 'Vista frontal',
        'position' => 0,
    ]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.products.show', $product))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/products/show')
            ->where('product.id', $product->id)
            ->where('product.name', 'Mochila Técnica')
            ->where('product.sku', 'MOCHILA-001')
            ->where('product.barcode', '7751234567890')
            ->where('product.slug', $product->slug)
            ->where('product.short_description', 'Lista para rutas largas.')
            ->where('product.description', '<h2>Detalles</h2><p>Material impermeable.</p>')
            ->where('product.base_price', '199.90')
            ->where('product.sale_price', '149.90')
            ->where('product.flag.value', ProductFlag::FEATURED->value)
            ->where('product.flag.label', ProductFlag::FEATURED->label())
            ->where('product.brand.name', 'Andes')
            ->where('product.category.name', 'Accesorios')
            ->where('product.published_at', $product->published_at?->toIso8601String())
            ->where('product.created_at', $product->created_at->toIso8601String())
            ->where('product.updated_at', $product->updated_at->toIso8601String())
            ->has('product.images', 2)
            ->where('product.images.0.id', $firstImage->id)
            ->where('product.images.0.position', 0)
            ->where('product.images.0.url', Storage::disk('public')->url($firstImage->path))
            ->where('product.images.0.thumbnail_url', Storage::disk('public')->url($firstImage->variants['sm']))
            ->where('product.images.1.id', $secondImage->id)
            ->where('product.images.1.position', 1),
        );
});

test('guests are redirected to the admin login page when accessing product edition', function () {
    $product = Product::factory()->create();

    $this->get(route('admin.products.edit', $product))
        ->assertRedirect(route('admin.auth.login'));
});

test('admins can see the product edition page with its ordered images', function () {
    Storage::fake('public');

    $admin = Administrator::factory()->create();
    $brand = Brand::factory()->inactive()->create(['name' => 'Marca actual']);
    $category = Category::factory()->inactive()->create(['name' => 'Categoría actual']);
    $product = Product::factory()->draft()->for($brand)->for($category)->create([
        'name' => 'Producto editable',
        'sku' => 'EDIT-001',
        'barcode' => '7751234567890',
        'base_price' => '150.00',
        'sale_price' => '100.00',
        'flag' => ProductFlag::NEW,
    ]);
    $secondImage = ProductImage::factory()->for($product)->create([
        'path' => "images/products/{$product->id}/xl/second.avif",
        'variants' => ['sm' => "images/products/{$product->id}/sm/second.avif"],
        'position' => 1,
    ]);
    $firstImage = ProductImage::factory()->for($product)->create([
        'path' => "images/products/{$product->id}/xl/first.avif",
        'variants' => ['sm' => "images/products/{$product->id}/sm/first.avif"],
        'position' => 0,
    ]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.products.edit', $product))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/products/edit')
            ->where('product.id', $product->id)
            ->where('product.name', 'Producto editable')
            ->where('product.brand_id', $brand->id)
            ->where('product.category_id', $category->id)
            ->where('product.is_draft', true)
            ->where('product.images.0.id', $firstImage->id)
            ->where('product.images.1.id', $secondImage->id)
            ->where('product.images.0.url', Storage::disk('public')->url("images/products/{$product->id}/sm/first.avif"))
            ->has('brands', 1)
            ->where('brands.0.id', $brand->id)
            ->has('categories', 1)
            ->where('categories.0.id', $category->id),
        );
});

test('admins can update product data and mix reordered existing and new images', function () {
    Storage::fake('public');

    $admin = Administrator::factory()->create();
    $brand = Brand::factory()->active()->create();
    $newBrand = Brand::factory()->active()->create();
    $category = Category::factory()->active()->create();
    $newCategory = Category::factory()->active()->create();
    $product = Product::factory()->draft()->for($brand)->for($category)->create([
        'name' => 'Nombre anterior',
        'sku' => 'UPDATE-001',
        'barcode' => '7751234567890',
    ]);

    $existingImages = collect(range(0, 2))->map(function (int $position) use ($product): ProductImage {
        $path = "images/products/{$product->id}/xl/existing-{$position}.avif";
        $variants = [
            'md' => "images/products/{$product->id}/md/existing-{$position}.avif",
            'sm' => "images/products/{$product->id}/sm/existing-{$position}.avif",
        ];

        Storage::disk('public')->put($path, "primary-{$position}");
        Storage::disk('public')->put($variants['md'], "medium-{$position}");
        Storage::disk('public')->put($variants['sm'], "small-{$position}");

        return ProductImage::factory()->for($product)->create([
            'path' => $path,
            'variants' => $variants,
            'position' => $position,
        ]);
    });

    $response = $this->actingAs($admin, 'admin')->post(route('admin.products.update', $product), [
        '_method' => 'patch',
        'name' => 'Nombre actualizado',
        'sku' => 'UPDATE-001',
        'barcode' => '7751234567890',
        'brand_id' => $newBrand->id,
        'category_id' => $newCategory->id,
        'short_description' => 'Descripción renovada',
        'description' => '<p><strong>Contenido</strong></p><script>alert(1)</script>',
        'base_price' => '150.00',
        'sale_price' => '100.00',
        'flag' => ProductFlag::FEATURED->value,
        'is_draft' => false,
        'images' => [
            ['id' => $existingImages[2]->id],
            ['file' => UploadedFile::fake()->image('new-image.jpg', 800, 600)],
            ['id' => $existingImages[0]->id],
        ],
    ]);

    $response
        ->assertRedirect(route('admin.products.index'))
        ->assertInertiaFlash('toast.message', __('actions.products.updated'));

    $product->refresh();
    $updatedImages = $product->images()->get();
    $newImage = $updatedImages->first(fn (ProductImage $image): bool => ! $existingImages->contains('id', $image->id));

    expect($product->name)->toBe('Nombre actualizado')
        ->and($product->sku)->toBe('UPDATE-001')
        ->and($product->brand->is($newBrand))->toBeTrue()
        ->and($product->category->is($newCategory))->toBeTrue()
        ->and($product->flag)->toBe(ProductFlag::FEATURED)
        ->and($product->published_at)->not->toBeNull()
        ->and($product->description)->toContain('<strong>Contenido</strong>')
        ->and($product->description)->not->toContain('<script')
        ->and($updatedImages)->toHaveCount(3)
        ->and($updatedImages->pluck('id')->all())->toBe([
            $existingImages[2]->id,
            $newImage?->id,
            $existingImages[0]->id,
        ])
        ->and($updatedImages->pluck('position')->all())->toBe([0, 1, 2])
        ->and($newImage)->toBeInstanceOf(ProductImage::class)
        ->and($newImage?->path)->toContain('/xl/')
        ->and($newImage?->path)->toEndWith('.avif');

    Storage::disk('public')->assertMissing($existingImages[1]->path);

    foreach ($existingImages[1]->variants as $deletedVariant) {
        Storage::disk('public')->assertMissing($deletedVariant);
    }

    Storage::disk('public')->assertExists($existingImages[0]->path);
    Storage::disk('public')->assertExists($existingImages[2]->path);
    Storage::disk('public')->assertExists($newImage->path);
});

test('product updates require one to five images that belong to the product', function () {
    Storage::fake('public');

    $admin = Administrator::factory()->create();
    $brand = Brand::factory()->active()->create();
    $category = Category::factory()->active()->create();
    $product = Product::factory()->for($brand)->for($category)->create();
    $otherProductImage = ProductImage::factory()->create();
    $payload = [
        '_method' => 'patch',
        'name' => $product->name,
        'sku' => $product->sku,
        'barcode' => $product->barcode,
        'brand_id' => $brand->id,
        'category_id' => $category->id,
        'sale_price' => $product->sale_price,
        'is_draft' => true,
    ];

    $this->actingAs($admin, 'admin')
        ->post(route('admin.products.update', $product), [...$payload, 'images' => []])
        ->assertSessionHasErrors('images');

    $this->actingAs($admin, 'admin')
        ->post(route('admin.products.update', $product), [
            ...$payload,
            'images' => collect(range(1, 6))
                ->map(fn (int $index): array => [
                    'file' => UploadedFile::fake()->image("product-{$index}.jpg"),
                ])
                ->all(),
        ])
        ->assertSessionHasErrors('images');

    $this->actingAs($admin, 'admin')
        ->post(route('admin.products.update', $product), [
            ...$payload,
            'images' => [['id' => $otherProductImage->id]],
        ])
        ->assertSessionHasErrors('images.0.id');
});

test('admins can see a paginated product listing', function () {
    $admin = Administrator::factory()->create();
    $brand = Brand::factory()->create(['name' => 'Acme']);
    $category = Category::factory()->create(['name' => 'Calzado']);

    Product::factory()->count(51)->recycle($brand)->recycle($category)->create();

    $this->actingAs($admin, 'admin')
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
    $admin = Administrator::factory()->create();
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

    $this->actingAs($admin, 'admin')
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

test('the product listing uses the complete product resource contract', function () {
    Storage::fake('public');

    $admin = Administrator::factory()->create();
    $brand = Brand::factory()->create(['name' => 'Andes']);
    $category = Category::factory()->create(['name' => 'Accesorios']);
    $product = Product::factory()->published()->for($brand)->for($category)->create([
        'name' => 'Gorra Técnica',
        'sku' => 'GORRA-001',
        'short_description' => 'Ligera y transpirable.',
        'description' => '<p>Protección para exteriores.</p>',
        'base_price' => '99.90',
        'sale_price' => '79.90',
        'flag' => ProductFlag::FEATURED,
    ]);

    $image = ProductImage::factory()->for($product)->create([
        'path' => 'images/products/xl/gorra.webp',
        'variants' => [
            'md' => 'images/products/md/gorra.webp',
            'sm' => 'images/products/sm/gorra.webp',
        ],
        'alt' => 'Gorra técnica azul',
        'position' => 0,
    ]);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.products.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('products.data.0.name', 'Gorra Técnica')
            ->where('products.data.0.slug', $product->slug)
            ->where('products.data.0.short_description', 'Ligera y transpirable.')
            ->where('products.data.0.description', '<p>Protección para exteriores.</p>')
            ->where('products.data.0.brand.name', 'Andes')
            ->where('products.data.0.category.name', 'Accesorios')
            ->where('products.data.0.base_price', '99.90')
            ->where('products.data.0.sale_price', '79.90')
            ->where('products.data.0.flag.value', ProductFlag::FEATURED->value)
            ->where('products.data.0.flag.label', ProductFlag::FEATURED->label())
            ->where('products.data.0.published_at', $product->published_at?->toIso8601String())
            ->where('products.data.0.created_at', $product->created_at->toIso8601String())
            ->where('products.data.0.updated_at', $product->updated_at->toIso8601String())
            ->where('products.data.0.thumbnail.alt', 'Gorra técnica azul')
            ->where('products.data.0.thumbnail.url', Storage::disk('public')->url('images/products/sm/gorra.webp'))
            ->has('products.data.0.images', 1)
            ->where('products.data.0.images.0.id', $image->id)
            ->where('products.data.0.images.0.url', Storage::disk('public')->url($image->path))
            ->where('products.data.0.images.0.thumbnail_url', Storage::disk('public')->url('images/products/sm/gorra.webp')),
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

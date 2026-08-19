<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Storage;

test('catalog seeders create a coherent deterministic catalog without physical image files', function () {
    Storage::fake('public');
    app()->detectEnvironment(fn (): string => 'local');

    $this->artisan('db:seed', [
        '--class' => DatabaseSeeder::class,
        '--force' => true,
    ])->assertExitCode(0);

    expect(Category::query()->count())->toBe(24)
        ->and(Category::query()->whereNull('parent_id')->count())->toBe(5)
        ->and(Category::query()->whereNotNull('parent_id')->count())->toBe(19)
        ->and(Brand::query()->count())->toBe(20)
        ->and(Product::query()->count())->toBe(70)
        ->and(ProductImage::query()->count())->toBe(70)
        ->and(Product::query()->whereNull('published_at')->count())->toBe(0)
        ->and(Product::query()->distinct()->count('brand_id'))->toBe(20)
        ->and(Product::query()->distinct()->count('category_id'))->toBeGreaterThanOrEqual(15)
        ->and(Product::query()->whereDoesntHave('brand')->count())->toBe(0)
        ->and(Product::query()->whereDoesntHave('category')->count())->toBe(0)
        ->and(Product::query()->whereDoesntHave('images')->count())->toBe(0)
        ->and(Storage::disk('public')->allFiles())->toBe([]);

    $refrigeration = Category::query()->where('slug', 'refrigeracion')->firstOrFail();

    expect($refrigeration->parent?->slug)->toBe('linea-blanca')
        ->and($refrigeration->parent?->parent?->slug)->toBe('hogar');

    $galaxy = Product::query()->where('sku', 'SAM-A55-256')->firstOrFail();

    expect($galaxy->brand->slug)->toBe('samsung')
        ->and($galaxy->category->slug)->toBe('celulares-smartphones')
        ->and($galaxy->images)->toHaveCount(1)
        ->and($galaxy->images->first()->path)->toContain("images/products/{$galaxy->id}/xl/")
        ->and($galaxy->images->first()->variants)->toHaveKeys(['md', 'sm']);

    $this->seed(DatabaseSeeder::class);

    expect(Category::query()->count())->toBe(24)
        ->and(Brand::query()->count())->toBe(20)
        ->and(Product::query()->count())->toBe(70)
        ->and(ProductImage::query()->count())->toBe(70);
});

test('catalog seeders are not invoked in production', function () {
    app()->detectEnvironment(fn (): string => 'production');

    $this->artisan('db:seed', [
        '--class' => DatabaseSeeder::class,
        '--force' => true,
    ])->assertExitCode(0);

    expect(User::query()->count())->toBe(1)
        ->and(Category::query()->count())->toBe(0)
        ->and(Brand::query()->count())->toBe(0)
        ->and(Product::query()->count())->toBe(0)
        ->and(ProductImage::query()->count())->toBe(0);
});

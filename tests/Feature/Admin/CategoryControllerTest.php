<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the admin login page when accessing categories index', function () {
    $response = $this->get(route('admin.categories.index'));

    $response->assertRedirect(route('admin.auth.login'));
});

test('admins can see only root categories', function () {
    $admin = User::factory()
        ->admin()
        ->create();

    $rootCategory = Category::factory()
        ->create(['name' => 'Hogar']);

    $childCategory = Category::factory()
        ->forParent($rootCategory)
        ->create(['name' => 'Decoración']);

    $anotherRootCategory = Category::factory()
        ->create(['name' => 'Tecnología']);

    $this->actingAs($admin)
        ->get(route('admin.categories.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/categories/index')
            ->where('parent_category', null)
            ->has('categories', 2)
            ->where('categories.0.name', 'Hogar')
            ->where('categories.0.parent', null)
            ->where('categories.1.name', 'Tecnología')
            ->where('categories.1.parent', null)
            ->missing('categories.2'),
        );

    expect($childCategory->refresh()->parent_id)->toBe($rootCategory->id)
        ->and($anotherRootCategory->refresh()->parent_id)->toBeNull();
});

test('admins can see the subcategories of a category', function () {
    $admin = User::factory()
        ->admin()
        ->create();

    $rootCategory = Category::factory()
        ->create(['name' => 'Tecnología']);

    $firstChild = Category::factory()
        ->forParent($rootCategory)
        ->create(['name' => 'Accesorios']);

    $secondChild = Category::factory()
        ->forParent($rootCategory)
        ->create(['name' => 'Laptops']);

    Category::factory()
        ->create(['name' => 'Hogar']);

    $this->actingAs($admin)
        ->get(route('admin.categories.subcategories', $rootCategory))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/categories/index')
            ->where('parent_category.name', $rootCategory->name)
            ->has('categories', 2)
            ->where('categories.0.name', 'Accesorios')
            ->where('categories.0.parent.id', $rootCategory->id)
            ->where('categories.1.name', 'Laptops')
            ->where('categories.1.parent.id', $rootCategory->id)
            ->missing('categories.2'),
        );

    expect($firstChild->refresh()->parent_id)->toBe($rootCategory->id)
        ->and($secondChild->refresh()->parent_id)->toBe($rootCategory->id);
});

test('categories generate slug and code automatically', function () {
    $category = Category::factory()->create([
        'name' => 'Moda Hombre',
    ]);

    expect($category->slug)->toBe('moda-hombre')
        ->and($category->code)->toMatch('/^CA[A-Z0-9]{4}$/')
        ->and($category->is_active)->toBeTrue()
        ->and($category->parent_id)->toBeNull();

    expect(Str::startsWith($category->code, 'CA'))->toBeTrue();
});

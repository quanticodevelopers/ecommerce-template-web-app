<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;

test('guests are redirected to the admin login page when accessing categories index', function () {
    $response = $this->get(route('admin.categories.index'));

    $response->assertRedirect(route('admin.auth.login'));
});

test('admins can see only root categories and parent options', function () {
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
            ->has('category_parent_options', 3)
            ->where('categories.0.name', 'Hogar')
            ->where('categories.0.parent', null)
            ->where('categories.1.name', 'Tecnología')
            ->where('categories.1.parent', null)
            ->missing('categories.2'),
        );

    expect($childCategory->refresh()->parent_id)->toBe($rootCategory->id)
        ->and($anotherRootCategory->refresh()->parent_id)->toBeNull();
});

test('admins can see the subcategories of a category and its parent is preselected', function () {
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
            ->has('category_parent_options', 4)
            ->where('categories.0.name', 'Accesorios')
            ->where('categories.0.parent.id', $rootCategory->id)
            ->where('categories.1.name', 'Laptops')
            ->where('categories.1.parent.id', $rootCategory->id)
            ->missing('categories.2'),
        );

    expect($firstChild->refresh()->parent_id)->toBe($rootCategory->id)
        ->and($secondChild->refresh()->parent_id)->toBe($rootCategory->id);
});

test('admins can create a root category with generated slug and code', function () {
    $admin = User::factory()
        ->admin()
        ->create();

    $response = $this->actingAs($admin)
        ->post(route('admin.categories.store'), [
            'name' => 'Moda Hombre',
            'parent_id' => '__root__',
            'short_description' => 'Ropa masculina',
        ]);

    $response->assertRedirect(route('admin.categories.index'));
    $response->assertInertiaFlash('toast.type', 'success');
    $response->assertInertiaFlash('toast.message', __('actions.categories.created'));

    $category = Category::query()
        ->where('name', 'Moda Hombre')
        ->firstOrFail();

    expect($category->slug)->toBe('moda-hombre')
        ->and($category->code)->toMatch('/^CA[A-Z0-9]{4}$/')
        ->and($category->is_active)->toBeTrue()
        ->and($category->parent_id)->toBeNull();

    expect(Str::startsWith($category->code, 'CA'))->toBeTrue();
});

test('admins can create a subcategory and return to its parent listing', function () {
    $admin = User::factory()
        ->admin()
        ->create();

    $parentCategory = Category::factory()
        ->create(['name' => 'Tecnología']);

    $response = $this->actingAs($admin)
        ->post(route('admin.categories.store'), [
            'name' => 'Periféricos',
            'parent_id' => $parentCategory->id,
            'short_description' => 'Accesorios para equipo',
        ]);

    $response->assertRedirect(route('admin.categories.subcategories', $parentCategory));
    $response->assertInertiaFlash('toast.type', 'success');
    $response->assertInertiaFlash('toast.message', __('actions.categories.created'));

    $category = Category::query()
        ->where('name', 'Periféricos')
        ->firstOrFail();

    expect($category->parent_id)->toBe($parentCategory->id)
        ->and($category->is_active)->toBeTrue();
});

test('admins must provide a valid category name when creating categories', function () {
    $admin = User::factory()
        ->admin()
        ->create();

    $this->actingAs($admin)
        ->post(route('admin.categories.store'), [
            'name' => '',
            'parent_id' => '__root__',
            'short_description' => 'Ropa masculina',
        ])
        ->assertSessionHasErrors('name');
});

test('admins must keep category short descriptions within the configured limit', function () {
    $admin = User::factory()
        ->admin()
        ->create();

    $this->actingAs($admin)
        ->post(route('admin.categories.store'), [
            'name' => 'Moda Hombre',
            'parent_id' => '__root__',
            'short_description' => str_repeat('a', 129),
        ])
        ->assertSessionHasErrors('short_description');
});

<?php

use App\Http\Controllers\Admin\Auth\ConfirmPasswordController as AdminConfirmPasswordController;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::middleware(['guest'])
            ->group(function () {
                Route::get('/login', AdminLoginController::class)
                    ->name('auth.login');
            });

        Route::middleware(['auth', 'can:access-admin'])
            ->group(function () {
                Route::redirect('/', 'admin/dashboard');
                Route::get('/dashboard', DashboardController::class)
                    ->name('dashboard');

                Route::controller(AdminCategoryController::class)
                    ->group(function () {
                        Route::get('/categories', 'index')
                            ->name('categories.index');
                        Route::post('/categories', 'store')
                            ->name('categories.store');
                        Route::patch('/categories/{category}', 'update')
                            ->name('categories.update');
                        Route::get('/categories/{category}/subcategories', 'subcategories')
                            ->name('categories.subcategories');
                    });

                Route::controller(AdminBrandController::class)
                    ->group(function () {
                        Route::get('/brands', 'index')
                            ->name('brands.index');
                        Route::post('/brands', 'store')
                            ->name('brands.store');
                        Route::patch('/brands/{brand}', 'update')
                            ->name('brands.update');
                    });

                Route::controller(AdminProductController::class)
                    ->group(function () {
                        Route::get('/products', 'index')
                            ->name('products.index');
                        Route::get('/products/create', 'create')
                            ->name('products.create');
                        Route::post('/products', 'store')
                            ->name('products.store');
                        Route::get('/products/{product}', 'show')
                            ->name('products.show');
                        Route::get('/products/{product}/edit', 'edit')
                            ->name('products.edit');
                        Route::patch('/products/{product}', 'update')
                            ->name('products.update');
                    });

                Route::controller(AdminUserController::class)
                    ->group(function () {
                        Route::get('/users', 'index')
                            ->name('users.index');
                        Route::post('/users', 'store')
                            ->name('users.store');
                        Route::patch('/users/{user}/reset-password', 'resetPassword')
                            ->name('users.reset-password');
                    });

                Route::controller(AdminCustomerController::class)
                    ->group(function () {
                        Route::get('/customers', 'index')
                            ->name('customers.index');
                    });

                Route::get('/confirm-password', AdminConfirmPasswordController::class)
                    ->name('auth.password.confirm');
            });
    });

require __DIR__.'/settings.php';

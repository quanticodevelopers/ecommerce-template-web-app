<?php

use App\Http\Controllers\Admin\AdministratorController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController as AdminAuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\ConfirmPasswordController as AdminConfirmPasswordController;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\BrandController as AdminBrandController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::middleware(['guest:admin'])
            ->group(function () {
                Route::get('/login', AdminLoginController::class)
                    ->name('auth.login');
                Route::post('/login', [AdminAuthenticatedSessionController::class, 'store'])
                    ->middleware('throttle:admin-login')
                    ->name('auth.login.store');
            });

        Route::middleware(['auth:admin'])
            ->group(function () {
                Route::post('/logout', [AdminAuthenticatedSessionController::class, 'destroy'])
                    ->name('auth.logout');
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

                Route::controller(AdministratorController::class)
                    ->group(function () {
                        Route::get('/admins', 'index')
                            ->name('admins.index');
                        Route::post('/admins', 'store')
                            ->name('admins.store');
                        Route::patch('/admins/{administrator}/reset-password', 'resetPassword')
                            ->name('admins.reset-password');
                    });

                Route::controller(AdminCustomerController::class)
                    ->group(function () {
                        Route::get('/customers', 'index')
                            ->name('customers.index');
                    });

                Route::get('/confirm-password', [AdminConfirmPasswordController::class, 'create'])
                    ->name('auth.password.confirm');
                Route::post('/confirm-password', [AdminConfirmPasswordController::class, 'store'])
                    ->name('auth.password.confirm.store');
            });
    });

require __DIR__.'/settings.php';

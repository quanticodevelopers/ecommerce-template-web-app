<?php

use App\Http\Controllers\Admin\Auth\ConfirmPasswordController as AdminConfirmPasswordController;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController as AdminForgotPasswordController;
use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController as AdminResetPasswordController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Middleware\EnsureActiveAdminUser;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::middleware(['guest'])
            ->group(function () {
                Route::get('/login', AdminLoginController::class)
                    ->name('auth.login');
                Route::get('/forgot-password', AdminForgotPasswordController::class)
                    ->name('auth.password.request');
                Route::get('/reset-password/{token}', AdminResetPasswordController::class)
                    ->name('auth.password.reset');
            });

        Route::middleware(['auth', EnsureActiveAdminUser::class, 'can:access-admin'])
            ->group(function () {
                Route::redirect('/', 'admin/dashboard');
                Route::get('/dashboard', DashboardController::class)
                    ->name('dashboard');

                Route::controller(AdminUserController::class)
                    ->group(function () {
                        Route::get('/users', 'index')
                            ->name('users.index');
                        Route::post('/users', 'store')
                            ->name('users.store');
                        Route::patch('/users/{user}/deactivate', 'deactivate')
                            ->name('users.deactivate');
                        Route::patch('/users/{user}/reactivate', 'reactivate')
                            ->name('users.reactivate');
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

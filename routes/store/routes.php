<?php

use App\Http\Controllers\Store\AccountController;
use App\Http\Controllers\Store\Auth\AuthenticatedSessionController as StoreAuthenticatedSessionController;
use App\Http\Controllers\Store\Auth\EmailVerificationNotificationController as StoreEmailVerificationNotificationController;
use App\Http\Controllers\Store\Auth\ForgotPasswordController as StoreForgotPasswordController;
use App\Http\Controllers\Store\Auth\LoginController as StoreLoginController;
use App\Http\Controllers\Store\Auth\NewPasswordController as StoreNewPasswordController;
use App\Http\Controllers\Store\Auth\PasswordResetLinkController as StorePasswordResetLinkController;
use App\Http\Controllers\Store\Auth\RegisterController as StoreRegisterController;
use App\Http\Controllers\Store\Auth\RegisteredCustomerController as StoreRegisteredCustomerController;
use App\Http\Controllers\Store\Auth\ResetPasswordController as StoreResetPasswordController;
use App\Http\Controllers\Store\Auth\VerifyEmailController as StoreVerifyEmailController;
use App\Http\Controllers\Store\Auth\VerifyEmailLinkController as StoreVerifyEmailLinkController;
use App\Http\Controllers\Store\HomeController;
use App\Http\Middleware\EnsureEmailIsVerified;
use Illuminate\Support\Facades\Route;

Route::get('/email/verify/{id}/{hash}', StoreVerifyEmailLinkController::class)
    ->middleware(['auth:store', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::name('store.')
    ->group(function () {
        Route::get('/', HomeController::class)
            ->name('home');

        Route::middleware(['guest:store'])
            ->name('auth.')
            ->group(function () {
                Route::get('/login', StoreLoginController::class)
                    ->name('login');
                Route::post('/login', [StoreAuthenticatedSessionController::class, 'store'])
                    ->middleware('throttle:store-login')
                    ->name('login.store');
                Route::get('/register', StoreRegisterController::class)
                    ->name('register');
                Route::post('/register', [StoreRegisteredCustomerController::class, 'store'])
                    ->name('register.store');
                Route::get('/forgot-password', StoreForgotPasswordController::class)
                    ->name('password.request');
                Route::post('/forgot-password', [StorePasswordResetLinkController::class, 'store'])
                    ->middleware('throttle:6,1')
                    ->name('password.email');
                Route::get('/reset-password/{token}', StoreResetPasswordController::class)
                    ->name('password.reset');
                Route::post('/reset-password', [StoreNewPasswordController::class, 'store'])
                    ->name('password.update');
            });

        Route::middleware(['auth:store'])
            ->group(function () {
                Route::post('/logout', [StoreAuthenticatedSessionController::class, 'destroy'])
                    ->name('auth.logout');
                Route::get('/email/verify', StoreVerifyEmailController::class)
                    ->name('verification.notice');

                Route::post('/email/verification-notification', StoreEmailVerificationNotificationController::class)
                    ->middleware(['throttle:6,1'])
                    ->name('verification.send');

                Route::controller(AccountController::class)
                    ->middleware([EnsureEmailIsVerified::class])
                    ->prefix('account')
                    ->name('account.')
                    ->group(function () {
                        Route::get('/overview', 'overview')->name('overview');
                    });
            });
    });

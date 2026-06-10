<?php

use App\Http\Controllers\Store\AccountController;
use App\Http\Controllers\Store\Auth\EmailVerificationNotificationController as StoreEmailVerificationNotificationController;
use App\Http\Controllers\Store\Auth\ForgotPasswordController as StoreForgotPasswordController;
use App\Http\Controllers\Store\Auth\LoginController as StoreLoginController;
use App\Http\Controllers\Store\Auth\RegisterController as StoreRegisterController;
use App\Http\Controllers\Store\Auth\ResetPasswordController as StoreResetPasswordController;
use App\Http\Controllers\Store\Auth\VerifyEmailController as StoreVerifyEmailController;
use App\Http\Controllers\Store\HomeController;
use App\Http\Middleware\EnsureEmailIsVerified;
use Illuminate\Support\Facades\Route;

Route::name('store.')
    ->group(function () {
        Route::get('/', HomeController::class)
            ->name('home');

        Route::middleware(['guest'])
            ->name('auth.')
            ->group(function () {
                Route::get('/login', StoreLoginController::class)
                    ->name('login');
                Route::get('/register', StoreRegisterController::class)
                    ->name('register');
                Route::get('/forgot-password', StoreForgotPasswordController::class)
                    ->name('password.request');
                Route::get('/reset-password/{token}', StoreResetPasswordController::class)
                    ->name('password.reset');
            });

        Route::middleware(['auth'])
            ->group(function () {
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

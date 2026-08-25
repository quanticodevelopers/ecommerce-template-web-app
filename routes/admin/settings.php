<?php

use App\Http\Controllers\Admin\Settings\InformationController;
use App\Http\Controllers\Admin\Settings\ProfileController;
use App\Http\Controllers\Admin\Settings\SecurityController;
use App\Http\Middleware\RequirePassword;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::redirect('settings', '/admin/settings/profile');

        Route::get('settings/information', [InformationController::class, 'edit'])
            ->name('information.edit');

        Route::put('settings/information', [InformationController::class, 'update'])
            ->name('information.update');

        Route::get('settings/profile', [ProfileController::class, 'edit'])
            ->name('profile.edit');
        Route::patch('settings/profile', [ProfileController::class, 'update'])
            ->name('profile.update');

        Route::get('settings/security', [SecurityController::class, 'edit'])
            ->middleware(RequirePassword::class)
            ->name('security.edit');

        Route::put('settings/password', [SecurityController::class, 'update'])
            ->middleware('throttle:6,1')
            ->name('user-password.update');

        Route::inertia('settings/appearance', 'admin/settings/appearance')
            ->name('appearance.edit');
    });

<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureRateLimiting();
        $this->configureCustomPasswordResetUrl();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    private function configureRateLimiting(): void
    {
        foreach (['store-login', 'admin-login'] as $limiter) {
            RateLimiter::for($limiter, function (Request $request): Limit {
                $email = Str::lower($request->string('email')->toString());

                return Limit::perMinute(5)->by(Str::transliterate($email.'|'.$request->ip()));
            });
        }
    }

    /**
     * Configure a custom password reset URL for the application.
     */
    public function configureCustomPasswordResetUrl(): void
    {
        ResetPassword::createUrlUsing(function ($user, string $token) {
            return url(route('store.auth.password.reset', [
                'token' => $token,
                'email' => $user->email,
            ], false));
        });
    }
}

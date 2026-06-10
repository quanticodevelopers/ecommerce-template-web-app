<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified as LaravelEnsureEmailIsVerified;

class EnsureEmailIsVerified extends LaravelEnsureEmailIsVerified
{
    public function handle($request, Closure $next, $redirectToRoute = null)
    {
        return parent::handle($request, $next, 'store.verification.notice');
    }
}

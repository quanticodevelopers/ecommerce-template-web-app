<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\RequirePassword as LaravelRequirePassword;

class RequirePassword extends LaravelRequirePassword
{
    public function handle($request, Closure $next, $redirectToRoute = null, $passwordTimeoutSeconds = null)
    {
        return parent::handle($request, $next, 'admin.auth.password.confirm');
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveAdminUser
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->isAdmin() && ! $request->user()->is_active) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return to_route('admin.auth.login')->withErrors([
                'email' => __('auth.inactive_admin'),
            ]);
        }

        return $next($request);
    }
}

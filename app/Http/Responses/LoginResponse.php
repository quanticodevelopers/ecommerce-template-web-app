<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        if ($request->wantsJson()) {
            return new JsonResponse(['two_factor' => false]);
        }

        return redirect()->intended($this->redirectTo($request));
    }

    private function redirectTo(Request $request): string
    {
        $intendedPath = parse_url((string) $request->session()->get('url.intended'), PHP_URL_PATH);

        if (is_string($intendedPath) && str_starts_with($intendedPath, '/admin') && $request->user()?->can('access-admin')) {
            return route('admin.dashboard');
        }

        if ($request->session()->pull('auth.area') === 'admin' && $request->user()?->can('access-admin')) {
            return route('admin.dashboard');
        }

        return route('store.home');
    }
}

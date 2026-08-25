<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ConfirmAdminPasswordRequest;
use App\Models\Administrator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ConfirmPasswordController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('admin/auth/confirm-password');
    }

    public function store(ConfirmAdminPasswordRequest $request): RedirectResponse
    {
        $administrator = $request->user('admin');

        if (! $administrator instanceof Administrator || ! Hash::check($request->string('password')->toString(), $administrator->password)) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('admin.dashboard', absolute: false));
    }
}

<?php

namespace App\Http\Controllers\Store\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Store\Auth\ResetPasswordRequest;
use App\Models\Customer;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class NewPasswordController extends Controller
{
    public function store(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::broker('customers')->reset(
            $request->safe()->only(['email', 'password', 'password_confirmation', 'token']),
            function (Customer $customer, string $password): void {
                $customer->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($customer));
            },
        );

        if ($status === Password::PASSWORD_RESET) {
            return to_route('store.auth.login')->with('status', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    }
}

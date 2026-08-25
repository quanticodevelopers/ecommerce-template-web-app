<?php

namespace App\Http\Controllers\Store\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $customer = $request->user('store');

        abort_unless($customer instanceof Customer, 403);

        if ($customer->hasVerifiedEmail()) {
            return to_route('store.home');
        }

        $customer->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}

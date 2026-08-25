<?php

namespace App\Http\Controllers\Store\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request): RedirectResponse|Response
    {
        $customer = $request->user('store');

        if ($customer instanceof Customer && $customer->hasVerifiedEmail()) {
            return to_route('store.home');
        }

        return Inertia::render('store/auth/verify-email', [
            'status' => $request->session()->get('status'),
        ]);
    }
}

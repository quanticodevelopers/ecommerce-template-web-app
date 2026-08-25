<?php

namespace App\Http\Controllers\Store\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailLinkController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $customer = $request->user('store');

        abort_unless($customer instanceof Customer, 403);
        abort_unless(hash_equals((string) $request->route('id'), (string) $customer->getKey()), 403);
        abort_unless(hash_equals((string) $request->route('hash'), sha1($customer->getEmailForVerification())), 403);

        if (! $customer->hasVerifiedEmail()) {
            $customer->markEmailAsVerified();
            event(new Verified($customer));
        }

        return redirect(route('store.home', absolute: false).'?verified=1');
    }
}

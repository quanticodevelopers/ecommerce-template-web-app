<?php

namespace App\Http\Controllers\Store\Auth;

use Illuminate\Http\Request;
use Laravel\Fortify\Http\Controllers\EmailVerificationNotificationController as Controller;

class EmailVerificationNotificationController extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('store.home'));
        }

        return parent::store($request);
    }
}

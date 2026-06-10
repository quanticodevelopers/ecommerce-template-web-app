<?php

namespace App\Http\Controllers\Store\Auth;

use App\Http\Controllers\Controller;
use App\Http\Responses\VerifyEmailResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return to_route('store.home');
        }

        return Inertia::render('store/auth/verify-email', [
            'status' => $request->session()->get('status'),
        ]);
    }
}

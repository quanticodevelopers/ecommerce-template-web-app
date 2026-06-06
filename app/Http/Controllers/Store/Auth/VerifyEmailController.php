<?php

namespace App\Http\Controllers\Store\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('store/auth/verify-email', [
            'status' => $request->session()->get('status'),
        ]);
    }
}

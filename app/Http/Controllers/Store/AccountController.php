<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class AccountController extends Controller
{
    public function overview()
    {
        return Inertia::render('store/account/overview');
    }
}

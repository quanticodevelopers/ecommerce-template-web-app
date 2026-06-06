<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class ConfirmPasswordController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('admin/auth/confirm-password');
    }
}

<?php

namespace App\Http\Controllers\Store\Auth;

use App\Enums\UserDocumentType;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class RegisterController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('store/auth/register', [
            'document_type_options' => UserDocumentType::options(),
            'passwordRules' => Password::defaults()->toPasswordRulesString(),
        ]);
    }
}

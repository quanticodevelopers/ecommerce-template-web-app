<?php

namespace App\Actions\Users;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Str;

class ResetAdminUserPasswordAction
{
    public function handle(User $user): string
    {
        if ($user->getAttribute('role') !== UserRole::ADMIN) {
            abort(404);
        }

        $generatedPassword = Str::random(18);

        $user->forceFill([
            'password' => $generatedPassword,
        ])->save();

        return $generatedPassword;
    }
}

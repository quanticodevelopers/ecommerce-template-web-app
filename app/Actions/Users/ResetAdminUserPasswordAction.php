<?php

namespace App\Actions\Users;

use App\Enums\AdministratorRole;
use App\Models\Administrator;
use Illuminate\Support\Str;

class ResetAdminUserPasswordAction
{
    public function handle(Administrator $administrator): string
    {
        if ($administrator->getAttribute('role') !== AdministratorRole::ADMIN) {
            abort(404);
        }

        $generatedPassword = Str::random(18);

        $administrator->forceFill([
            'password' => $generatedPassword,
        ])->save();

        return $generatedPassword;
    }
}

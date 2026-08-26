<?php

namespace App\Actions\Users;

use App\Enums\AdministratorRole;
use App\Models\Administrator;
use Illuminate\Support\Str;

class CreateAdminUserAction
{
    /**
     * @param  array<string, mixed>  $validated
     * @return array{administrator: Administrator, password: string}
     */
    public function handle(array $validated): array
    {
        $generatedPassword = Str::random(18);

        $administrator = Administrator::query()->create([
            'name' => $validated['name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => $generatedPassword,
            'role' => AdministratorRole::ADMIN,
        ]);

        return [
            'administrator' => $administrator,
            'password' => $generatedPassword,
        ];
    }
}

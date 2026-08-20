<?php

namespace App\Actions\Users;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Str;

class CreateAdminUserAction
{
    /**
     * @param  array<string, mixed>  $validated
     * @return array{user: User, password: string}
     */
    public function handle(array $validated): array
    {
        $generatedPassword = Str::random(18);

        $user = User::query()->create([
            'document_type' => $validated['document_type'],
            'document_number' => $validated['document_number'],
            'name' => $validated['name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => $generatedPassword,
            'role' => UserRole::ADMIN,
        ]);

        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        return [
            'user' => $user,
            'password' => $generatedPassword,
        ];
    }
}

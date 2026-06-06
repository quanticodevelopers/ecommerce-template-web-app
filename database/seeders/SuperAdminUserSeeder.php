<?php

namespace Database\Seeders;

use App\Enums\UserDocumentType;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'root@quanticodevelopers.pe'],
            [
                'document_type' => UserDocumentType::DNI,
                'document_number' => '12345678',
                'name' => 'Super Admin',
                'last_name' => config('app.name'),
                'email_verified_at' => now(),
                'phone' => '987654321',
                'password' => config('super-admin.password', 'password'),
                'role' => UserRole::SUPER_ADMIN,
            ]
        );
    }
}

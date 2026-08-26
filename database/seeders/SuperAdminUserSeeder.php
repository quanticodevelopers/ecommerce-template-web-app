<?php

namespace Database\Seeders;

use App\Enums\AdministratorRole;
use App\Models\Administrator;
use Illuminate\Database\Seeder;

class SuperAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Administrator::query()->updateOrCreate(
            ['email' => 'root@quanticodevelopers.pe'],
            [
                'name' => 'Super Admin',
                'last_name' => config('app.name'),
                'phone' => '987654321',
                'password' => config('super-admin.password', 'password'),
                'role' => AdministratorRole::SUPER_ADMIN,
            ]
        );
    }
}

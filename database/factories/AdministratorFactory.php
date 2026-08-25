<?php

namespace Database\Factories;

use App\Enums\AdministratorRole;
use App\Enums\UserDocumentType;
use App\Models\Administrator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/** @extends Factory<Administrator> */
class AdministratorFactory extends Factory
{
    protected static ?string $password;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        $documentType = fake()->randomElement(UserDocumentType::cases());

        return [
            'document_type' => $documentType,
            'document_number' => match ($documentType) {
                UserDocumentType::DNI => fake()->numerify('########'),
                UserDocumentType::CE => fake()->numerify('############'),
                UserDocumentType::PASAPORTE => fake()->bothify('??##########'),
            },
            'name' => fake()->name(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('9########'),
            'password' => static::$password ??= Hash::make('password'),
            'role' => AdministratorRole::ADMIN,
            'remember_token' => Str::random(10),
        ];
    }

    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes): array => [
            'role' => AdministratorRole::SUPER_ADMIN,
        ]);
    }
}

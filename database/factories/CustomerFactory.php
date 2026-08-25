<?php

namespace Database\Factories;

use App\Enums\UserDocumentType;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/** @extends Factory<Customer> */
class CustomerFactory extends Factory
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
            'email_verified_at' => now(),
            'phone' => fake()->numerify('9########'),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes): array => [
            'email_verified_at' => null,
        ]);
    }
}

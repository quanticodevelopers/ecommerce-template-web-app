<?php

namespace Database\Factories;

use App\Enums\UserDocumentType;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $documentTypes = array_column(UserDocumentType::cases(), 'value');
        $documentType = fake()->randomElement($documentTypes);
        $documentNumber = match ($documentType) {
            UserDocumentType::DNI->value => fake()->numerify('########'),
            UserDocumentType::CE->value => fake()->numerify('############'),
            UserDocumentType::PASAPORTE->value => fake()->bothify('??##########'),
            default => null,
        };

        return [
            'document_type' => $documentType,
            'document_number' => $documentNumber,
            'name' => fake()->name(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'phone' => fake()->numerify('9########'),
            'password' => static::$password ??= Hash::make('password'),
            'role' => UserRole::CUSTOMER,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Indicate that the model's role should be Administrator
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::ADMIN,
        ]);
    }

    /**
     * Indicate that the model has two-factor authentication configured.
     */
    public function withTwoFactor(): static {}
}

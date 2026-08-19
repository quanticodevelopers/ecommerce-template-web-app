<?php

namespace Database\Factories;

use App\Enums\ProductFlag;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'brand_id' => Brand::factory(),
            'category_id' => Category::factory(),
            'sku' => fake()->unique()->bothify('SKU-########'),
            'barcode' => fake()->unique()->ean13(),
            'name' => fake()->words(3, true),
            'short_description' => fake()->optional()->sentence(),
            'description' => fake()->optional()->paragraph(),
            'base_price' => fake()->optional()->randomFloat(2, 10, 900),
            'sale_price' => fake()->randomFloat(2, 10, 900),
            'flag' => fake()->optional()->randomElement(ProductFlag::cases()),
            'published_at' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes): array => ['published_at' => now()->subDay()]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes): array => ['published_at' => null]);
    }
}

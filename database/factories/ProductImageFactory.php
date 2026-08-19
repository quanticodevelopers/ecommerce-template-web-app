<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Config;

/**
 * @extends Factory<ProductImage>
 */
class ProductImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $filename = fake()->uuid().'.webp';
        $primaryVariant = Config::string('product-images.primary_variant');
        $configuredVariants = Config::array('product-images.variants');
        $variants = [];

        foreach (array_keys($configuredVariants) as $variant) {
            if ($variant !== $primaryVariant) {
                $variants[$variant] = "images/products/{$variant}/{$filename}";
            }
        }

        return [
            'product_id' => Product::factory(),
            'path' => "images/products/{$primaryVariant}/{$filename}",
            'variants' => $variants,
            'alt' => fake()->sentence(3),
            'position' => 0,
        ];
    }
}

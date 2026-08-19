<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CreateProductAction
{
    public function __construct(
        private ProcessProductImageAction $processProductImage,
        private SanitizeProductDescriptionAction $sanitizeDescription,
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public function handle(array $validated): Product
    {
        $product = null;

        try {
            return DB::transaction(function () use ($validated, &$product): Product {
                $images = Arr::pull($validated, 'images', []);
                $isDraft = (bool) Arr::pull($validated, 'is_draft', false);
                $validated['description'] = $this->sanitizeDescription->handle(
                    is_string($validated['description'] ?? null) ? $validated['description'] : null,
                );
                $validated['published_at'] = $isDraft ? null : now();

                $product = Product::query()->create($validated);

                foreach ($images as $position => $image) {
                    if ($image instanceof UploadedFile) {
                        $this->processProductImage->handle($image, $product, $position);
                    }
                }

                return $product;
            });
        } catch (Throwable $exception) {
            if ($product instanceof Product) {
                Storage::disk('public')->deleteDirectory("images/products/{$product->id}");
            }

            throw $exception;
        }
    }
}

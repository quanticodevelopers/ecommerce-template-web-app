<?php

namespace App\Actions\Products;

use App\Exceptions\ProductImageProcessingException;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UpdateProductAction
{
    public function __construct(
        private ProcessProductImageAction $processProductImage,
        private SanitizeProductDescriptionAction $sanitizeDescription,
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public function handle(Product $product, array $validated): Product
    {
        $createdImages = [];
        $pathsToDelete = [];

        try {
            $updatedProduct = DB::transaction(function () use ($product, $validated, &$createdImages, &$pathsToDelete): Product {
                $lockedProduct = Product::query()->lockForUpdate()->findOrFail($product->id);
                $slots = Arr::pull($validated, 'images', []);
                $isDraft = (bool) Arr::pull($validated, 'is_draft', false);
                $validated['description'] = $this->sanitizeDescription->handle(
                    is_string($validated['description'] ?? null) ? $validated['description'] : null,
                );
                $validated['published_at'] = $isDraft ? null : ($lockedProduct->published_at ?? now());

                /** @var Collection<string, ProductImage> $existingImages */
                $existingImages = $lockedProduct->images()->get()->keyBy(fn (ProductImage $image): string => $image->id);
                $retainedIds = [];

                foreach ($slots as $slot) {
                    $existingId = is_array($slot) ? ($slot['id'] ?? null) : null;

                    if (is_string($existingId)) {
                        $retainedIds[] = $existingId;
                    }
                }

                $imagesToDelete = $existingImages->reject(
                    fn (ProductImage $image): bool => in_array($image->id, $retainedIds, true),
                );

                foreach ($imagesToDelete as $image) {
                    $pathsToDelete = [...$pathsToDelete, ...$this->pathsForImage($image)];
                    $image->delete();
                }

                $temporaryPosition = 65535;

                foreach ($existingImages->except($imagesToDelete->keys()->all()) as $image) {
                    while ($existingImages->contains(fn (ProductImage $candidate): bool => $candidate->position === $temporaryPosition)) {
                        $temporaryPosition--;
                    }

                    $image->update(['position' => $temporaryPosition]);
                    $temporaryPosition--;
                }

                $lockedProduct->update($validated);

                foreach ($slots as $position => $slot) {
                    if (! is_array($slot)) {
                        continue;
                    }

                    $existingId = $slot['id'] ?? null;

                    if (is_string($existingId)) {
                        $existingImage = $existingImages->get($existingId);

                        if (! $existingImage instanceof ProductImage) {
                            throw new ProductImageProcessingException('No se pudo encontrar una imagen existente del producto.');
                        }

                        $existingImage->update([
                            'alt' => $lockedProduct->name,
                            'position' => $position,
                        ]);

                        continue;
                    }

                    $uploadedImage = $slot['file'] ?? null;

                    if ($uploadedImage instanceof UploadedFile) {
                        $createdImages[] = $this->processProductImage->handle($uploadedImage, $lockedProduct, $position);
                    }
                }

                return $lockedProduct;
            });
        } catch (Throwable $exception) {
            foreach ($createdImages as $createdImage) {
                Storage::disk('public')->delete($this->pathsForImage($createdImage));
            }

            throw $exception;
        }

        Storage::disk('public')->delete($pathsToDelete);

        return $updatedProduct->refresh();
    }

    /** @return array<int, string> */
    private function pathsForImage(ProductImage $image): array
    {
        $variants = $image->getAttribute('variants');
        $variantPaths = is_array($variants) ? array_values(array_filter($variants, 'is_string')) : [];

        return [$image->path, ...$variantPaths];
    }
}

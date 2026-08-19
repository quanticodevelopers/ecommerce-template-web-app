<?php

namespace App\Actions\Products;

use App\Exceptions\ProductImageProcessingException;
use App\Models\Product;
use App\Models\ProductImage;
use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ProcessProductImageAction
{
    public function handle(UploadedFile $uploadedImage, Product $product, int $position): ProductImage
    {
        $configuredVariants = Config::array('product-images.variants');
        $primaryVariant = Config::string('product-images.primary_variant');
        $quality = Config::integer('product-images.quality');
        $storedPaths = [];
        $sourceImage = null;

        try {
            $sourcePath = $uploadedImage->getRealPath();

            if ($sourcePath === false) {
                throw new ProductImageProcessingException('No se pudo leer la imagen subida.');
            }

            $contents = file_get_contents($sourcePath);
            $sourceImage = is_string($contents) ? imagecreatefromstring($contents) : false;

            if (! $sourceImage instanceof GdImage) {
                throw new ProductImageProcessingException('El archivo subido no contiene una imagen válida.');
            }

            $paths = [];
            $filename = Str::lower((string) Str::ulid()).'.avif';

            foreach ($configuredVariants as $variant => $dimensions) {
                if (! is_string($variant) || ! is_array($dimensions)) {
                    continue;
                }

                $width = $dimensions['width'] ?? null;
                $height = $dimensions['height'] ?? null;

                if (! is_int($width) || $width < 1 || ! is_int($height) || $height < 1) {
                    continue;
                }

                $path = $this->variantPath($product, $variant, $filename);
                $variantImage = $this->centerCrop($sourceImage, $width, $height);
                $encodedImage = $this->encodeAsAvif($variantImage, $quality);
                imagedestroy($variantImage);

                $stored = Storage::disk('public')->put($path, $encodedImage);

                if (! $stored) {
                    throw new ProductImageProcessingException('No se pudo guardar una variante de imagen.');
                }

                $storedPaths[] = $path;
                $paths[$variant] = $path;
            }

            $primaryPath = $paths[$primaryVariant] ?? null;

            if (! is_string($primaryPath)) {
                throw new ProductImageProcessingException('La variante principal de imagen no está configurada correctamente.');
            }

            unset($paths[$primaryVariant]);

            return $product->images()->create([
                'path' => $primaryPath,
                'variants' => $paths,
                'alt' => $product->name,
                'position' => $position,
            ]);
        } catch (ProductImageProcessingException $exception) {
            Storage::disk('public')->delete($storedPaths);

            throw $exception;
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);

            throw new ProductImageProcessingException(
                'No se pudo procesar una de las imágenes del producto.',
                previous: $exception,
            );
        } finally {
            if ($sourceImage instanceof GdImage) {
                imagedestroy($sourceImage);
            }
        }
    }

    private function centerCrop(GdImage $sourceImage, int $targetWidth, int $targetHeight): GdImage
    {
        if ($targetWidth < 1 || $targetHeight < 1) {
            throw new ProductImageProcessingException('Las dimensiones de una variante no son válidas.');
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);
        $cropSize = min($sourceWidth, $sourceHeight);
        $sourceX = (int) floor(($sourceWidth - $cropSize) / 2);
        $sourceY = (int) floor(($sourceHeight - $cropSize) / 2);
        $variantImage = imagecreatetruecolor($targetWidth, $targetHeight);

        if (! $variantImage instanceof GdImage) {
            throw new ProductImageProcessingException('No se pudo preparar una variante de imagen.');
        }

        imagealphablending($variantImage, false);
        imagesavealpha($variantImage, true);
        imagecopyresampled(
            $variantImage,
            $sourceImage,
            0,
            0,
            $sourceX,
            $sourceY,
            $targetWidth,
            $targetHeight,
            $cropSize,
            $cropSize,
        );

        return $variantImage;
    }

    private function encodeAsAvif(GdImage $image, int $quality): string
    {
        ob_start();
        $encoded = imageavif($image, null, $quality);
        $contents = ob_get_clean();

        if (! $encoded || ! is_string($contents)) {
            throw new ProductImageProcessingException('No se pudo convertir una variante al formato AVIF.');
        }

        return $contents;
    }

    private function variantPath(Product $product, string $variant, string $filename): string
    {
        return "images/products/{$product->id}/{$variant}/{$filename}";
    }
}

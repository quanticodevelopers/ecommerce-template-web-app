<?php

namespace App\Actions\Brands;

use App\Models\Brand;
use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Imagick;
use RuntimeException;

class ProcessBrandLogoAction
{
    public function handle(UploadedFile $logo, Brand $brand): string
    {
        $path = $this->pathFor($brand);

        if ($this->resolveImageDriver() === 'imagick') {
            $this->storeWithImagick($logo, $path);
        } else {
            $this->storeWithGd($logo, $path);
        }

        return $path;
    }

    public function delete(Brand $brand): void
    {
        Storage::disk('public')->delete($this->pathFor($brand));
    }

    private function resolveImageDriver(): string
    {
        if (extension_loaded('imagick') && class_exists(Imagick::class)) {
            return 'imagick';
        }

        return 'gd';
    }

    private function pathFor(Brand $brand): string
    {
        return "images/brands/brand-{$brand->slug}-{$brand->code}.webp";
    }

    private function storeWithImagick(UploadedFile $logo, string $path): void
    {
        try {
            $imagick = new Imagick($logo->getRealPath());
            $imagick->setImageFormat('webp');
            $imagick->setImageCompressionQuality(85);

            Storage::disk('public')->put($path, $imagick->getImageBlob());

            $imagick->clear();
            $imagick->destroy();
        } catch (\Throwable) {
            $this->storeWithGd($logo, $path);
        }
    }

    private function storeWithGd(UploadedFile $logo, string $path): void
    {
        $image = $this->createGdImage($logo);
        $temporaryPath = tempnam(sys_get_temp_dir(), 'brand-logo-');

        if ($temporaryPath === false) {
            throw new RuntimeException('Unable to allocate a temporary file for the brand logo.');
        }

        $temporaryWebpPath = $temporaryPath.'.webp';
        @unlink($temporaryPath);

        imagewebp($image, $temporaryWebpPath, 85);

        $contents = file_get_contents($temporaryWebpPath);

        if ($contents === false) {
            imagedestroy($image);
            @unlink($temporaryWebpPath);

            throw new RuntimeException('Unable to write the brand logo as WebP.');
        }

        Storage::disk('public')->put($path, $contents);

        imagedestroy($image);
        @unlink($temporaryWebpPath);
    }

    private function createGdImage(UploadedFile $logo): GdImage
    {
        $path = $logo->getRealPath();

        if ($path === false) {
            throw new RuntimeException('Unable to read the uploaded brand logo.');
        }

        $mimeType = $logo->getMimeType();

        $image = match ($mimeType) {
            'image/jpeg' => imagecreatefromjpeg($path),
            'image/png' => imagecreatefrompng($path),
            'image/webp' => imagecreatefromwebp($path),
            'image/avif' => imagecreatefromavif($path),
            default => false,
        };

        if ($image === false) {
            throw new RuntimeException('Unable to convert the uploaded brand logo.');
        }

        if (in_array($mimeType, ['image/png', 'image/webp', 'image/avif'], true)) {
            imagepalettetotruecolor($image);
            imagealphablending($image, false);
            imagesavealpha($image, true);
        }

        return $image;
    }
}

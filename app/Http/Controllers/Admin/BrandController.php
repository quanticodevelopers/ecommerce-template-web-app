<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBrandRequest;
use App\Http\Resources\Admin\BrandResource;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Imagick;
use Inertia\Inertia;
use Inertia\Response;

class BrandController extends Controller
{
    /**
     * Display a listing of brands.
     */
    public function index(): Response
    {
        $brands = Brand::query()
            ->select(['id', 'name', 'slug', 'code', 'short_description', 'logo_path', 'is_active', 'created_at'])
            ->orderBy('name')
            ->get();

        return Inertia::render('admin/brands/index', [
            'brands' => BrandResource::collection($brands)->resolve(),
        ]);
    }

    /**
     * Store a newly created brand.
     */
    public function store(StoreBrandRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $brand = Brand::query()->create([
            'name' => $validated['name'],
            'short_description' => $validated['short_description'] ?? null,
            'is_active' => true,
        ]);

        $brand->refresh();
        $logoPath = $this->brandLogoPath($brand);

        try {
            $this->storeLogoAsWebp($request->file('logo'), $logoPath);

            $brand->update([
                'logo_path' => $logoPath,
            ]);
        } catch (\Throwable) {
            $this->deleteLogoIfExists($logoPath);
            $brand->delete();

            throw ValidationException::withMessages([
                'logo' => 'No se pudo procesar el logo de la marca.',
            ]);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('actions.brands.created'),
        ]);

        return to_route('admin.brands.index');
    }

    /**
     * Resolve the image driver to use for logo conversion.
     */
    private function resolveImageDriver(): string
    {
        if (extension_loaded('imagick') && class_exists(Imagick::class)) {
            return 'imagick';
        }

        return 'gd';
    }

    /**
     * Build the brand logo path.
     */
    private function brandLogoPath(Brand $brand): string
    {
        return "images/brands/brand-{$brand->slug}-{$brand->code}.webp";
    }

    /**
     * Store the uploaded logo as WebP.
     */
    private function storeLogoAsWebp(UploadedFile $logo, string $path): void
    {
        if ($this->resolveImageDriver() === 'imagick') {
            $this->storeLogoWithImagick($logo, $path);

            return;
        }

        $this->storeLogoWithGd($logo, $path);
    }

    /**
     * Delete the stored logo if it exists.
     */
    private function deleteLogoIfExists(string $path): void
    {
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Store the logo with Imagick.
     */
    private function storeLogoWithImagick(UploadedFile $logo, string $path): void
    {
        try {
            $imagick = new Imagick($logo->getRealPath());
            $imagick->setImageFormat('webp');
            $imagick->setImageCompressionQuality(85);

            Storage::disk('public')->put($path, $imagick->getImageBlob());

            $imagick->clear();
            $imagick->destroy();
        } catch (\Throwable) {
            $this->storeLogoWithGd($logo, $path);
        }
    }

    /**
     * Store the logo with GD.
     */
    private function storeLogoWithGd(UploadedFile $logo, string $path): void
    {
        $image = $this->createGdImage($logo);
        $temporaryPath = tempnam(sys_get_temp_dir(), 'brand-logo-');

        if ($temporaryPath === false) {
            throw new \RuntimeException('Unable to allocate a temporary file for the brand logo.');
        }

        $temporaryWebpPath = $temporaryPath.'.webp';
        @unlink($temporaryPath);

        imagewebp($image, $temporaryWebpPath, 85);

        $contents = file_get_contents($temporaryWebpPath);

        if ($contents === false) {
            imagedestroy($image);
            @unlink($temporaryWebpPath);

            throw new \RuntimeException('Unable to write the brand logo as WebP.');
        }

        Storage::disk('public')->put($path, $contents);

        imagedestroy($image);
        @unlink($temporaryWebpPath);
    }

    /**
     * Create a GD image from the uploaded file.
     */
    private function createGdImage(UploadedFile $logo): \GdImage
    {
        $path = $logo->getRealPath();

        if ($path === false) {
            throw new \RuntimeException('Unable to read the uploaded brand logo.');
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
            throw new \RuntimeException('Unable to convert the uploaded brand logo.');
        }

        if ($mimeType === 'image/png' || $mimeType === 'image/webp' || $mimeType === 'image/avif') {
            imagepalettetotruecolor($image);
            imagealphablending($image, false);
            imagesavealpha($image, true);
        }

        return $image;
    }
}

<?php

namespace App\Actions\Brands;

use App\Models\Brand;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use RuntimeException;
use Throwable;

class CreateBrandAction
{
    public function __construct(private ProcessBrandLogoAction $processBrandLogo) {}

    /** @param array<string, mixed> $validated */
    public function handle(array $validated): Brand
    {
        $logo = Arr::pull($validated, 'logo');
        $brand = Brand::query()->create([
            'name' => $validated['name'],
            'short_description' => $validated['short_description'] ?? null,
            'is_active' => true,
        ]);

        $brand->refresh();

        try {
            if (! $logo instanceof UploadedFile) {
                throw new RuntimeException('A brand logo is required.');
            }

            $brand->update([
                'logo_path' => $this->processBrandLogo->handle($logo, $brand),
            ]);
        } catch (Throwable $exception) {
            $this->processBrandLogo->delete($brand);
            $brand->delete();

            throw $exception;
        }

        return $brand;
    }
}

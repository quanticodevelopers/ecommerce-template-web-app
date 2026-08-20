<?php

namespace App\Actions\Brands;

use App\Models\Brand;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

class UpdateBrandAction
{
    public function __construct(private ProcessBrandLogoAction $processBrandLogo) {}

    /** @param array<string, mixed> $validated */
    public function handle(Brand $brand, array $validated): Brand
    {
        $logo = Arr::pull($validated, 'logo');
        $updateData = [
            'name' => $validated['name'],
            'short_description' => $validated['short_description'] ?? null,
            'is_active' => $validated['is_active'],
        ];

        if ($logo instanceof UploadedFile) {
            $updateData['logo_path'] = $this->processBrandLogo->handle($logo, $brand);
        }

        $brand->update($updateData);

        return $brand;
    }
}

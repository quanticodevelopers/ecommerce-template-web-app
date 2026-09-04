<?php

namespace App\Http\Resources\Admin;

use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

/** @mixin ProductImage */
class ProductImageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $disk = Storage::disk('public');
        $variants = $this->getAttribute('variants');
        $variants = is_array($variants) ? $variants : [];
        $variants[Config::string('product-images.primary_variant')] = $this->path;

        return [
            'id' => $this->id,
            'url' => $disk->url($this->path),
            'variants' => collect($variants)->map(fn (string $path): string => $disk->url($path))->all(),
            'listing_variant' => Config::string('product-images.listing_variant'),
            'alt' => $this->alt,
            'position' => $this->position,
        ];
    }
}

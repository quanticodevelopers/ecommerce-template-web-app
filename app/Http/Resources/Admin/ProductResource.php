<?php

namespace App\Http\Resources\Admin;

use App\Enums\ProductFlag;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

/** @mixin Product */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $flag = $this->getAttribute('flag');
        $primaryImage = $this->images->first();

        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'name' => $this->name,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'base_price' => $this->base_price,
            'sale_price' => $this->sale_price,
            'flag' => $flag instanceof ProductFlag ? [
                'value' => $flag->value,
                'label' => $flag->label(),
            ] : null,
            'brand' => [
                'id' => $this->brand->id,
                'name' => $this->brand->name,
            ],
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ],
            'published_at' => $this->publishedAtIso8601(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'thumbnail' => $primaryImage === null ? null : [
                'url' => Storage::disk('public')->url(
                    $primaryImage->pathForVariant(Config::string('product-images.listing_variant')),
                ),
                'alt' => $primaryImage->alt,
            ],
            'images' => $this->images->map(fn (ProductImage $image): array => [
                'id' => $image->id,
                'url' => Storage::disk('public')->url($image->path),
                'thumbnail_url' => Storage::disk('public')->url(
                    $image->pathForVariant(Config::string('product-images.listing_variant')),
                ),
                'alt' => $image->alt,
                'position' => $image->position,
            ])->values(),
        ];
    }
}

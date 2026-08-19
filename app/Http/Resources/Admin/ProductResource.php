<?php

namespace App\Http\Resources\Admin;

use App\Models\Product;
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
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'name' => $this->name,
            'brand' => [
                'id' => $this->brand->id,
                'name' => $this->brand->name,
            ],
            'category' => [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ],
            'base_price' => $this->base_price,
            'sale_price' => $this->sale_price,
            'flag' => $this->flagValue(),
            'published_at' => $this->publishedAtIso8601(),
            'thumbnail' => $this->primaryImage === null ? null : [
                'url' => Storage::disk('public')->url(
                    $this->primaryImage->pathForVariant(Config::string('product-images.listing_variant')),
                ),
                'alt' => $this->primaryImage->alt,
            ],
        ];
    }
}

<?php

namespace App\Http\Resources\Admin;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

/** @mixin Product */
class ProductFormResource extends JsonResource
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
            'brand_id' => $this->brand_id,
            'category_id' => $this->category_id,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'name' => $this->name,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'base_price' => $this->base_price,
            'sale_price' => $this->sale_price,
            'flag' => $this->flagValue(),
            'is_draft' => $this->published_at === null,
            'images' => $this->images->map(fn (ProductImage $image): array => [
                'id' => $image->id,
                'url' => Storage::disk('public')->url(
                    $image->pathForVariant(Config::string('product-images.listing_variant')),
                ),
                'alt' => $image->alt,
            ])->values(),
        ];
    }
}

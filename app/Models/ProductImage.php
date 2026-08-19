<?php

namespace App\Models;

use Database\Factories\ProductImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['product_id', 'path', 'variants', 'alt', 'position'])]
class ProductImage extends Model
{
    /** @use HasFactory<ProductImageFactory> */
    use HasFactory, HasUlids;

    protected function casts(): array
    {
        return [
            'variants' => 'array',
            'position' => 'integer',
        ];
    }

    public function pathForVariant(string $variant): string
    {
        $variants = $this->getAttribute('variants');

        if (! is_array($variants)) {
            return $this->getAttribute('path');
        }

        $path = $variants[$variant] ?? null;

        return is_string($path) ? $path : $this->getAttribute('path');
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

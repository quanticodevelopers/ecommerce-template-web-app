<?php

namespace App\Models;

use App\Enums\ProductFlag;
use Carbon\CarbonInterface;
use Cviebrock\EloquentSluggable\Sluggable;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'brand_id', 'category_id', 'sku', 'barcode', 'name', 'slug',
    'short_description', 'description', 'base_price', 'sale_price',
    'flag', 'published_at',
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, HasUlids, Sluggable;

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'flag' => ProductFlag::class,
            'published_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Brand, $this> */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /** @return BelongsTo<Category, $this> */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** @return HasMany<ProductImage, $this> */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('position');
    }

    /** @return HasOne<ProductImage, $this> */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->ofMany('position', 'min');
    }

    public function flagValue(): ?string
    {
        $flag = $this->getAttribute('flag');

        return $flag instanceof ProductFlag ? $flag->value : null;
    }

    public function publishedAtIso8601(): ?string
    {
        $publishedAt = $this->getAttribute('published_at');

        return $publishedAt instanceof CarbonInterface ? $publishedAt->toIso8601String() : null;
    }

    /** @return array<string, mixed> */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['name', 'sku'],
                'onUpdate' => false,
                'maxLength' => 255,
            ],
        ];
    }
}

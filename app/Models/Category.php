<?php

namespace App\Models;

use App\Concerns\GeneratesCode;
use Cviebrock\EloquentSluggable\Sluggable;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'code', 'parent_id', 'short_description', 'is_active'])]
class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use GeneratesCode, HasFactory, HasUlids, Sluggable;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected function codePrefix(): string
    {
        return 'CA';
    }

    protected function codeLength(): int
    {
        return 6;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                'onUpdate' => false,
                'maxLength' => 128,
            ],
        ];
    }
}

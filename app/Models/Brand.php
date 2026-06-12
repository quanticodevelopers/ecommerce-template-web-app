<?php

namespace App\Models;

use App\Concerns\GeneratesCode;
use Cviebrock\EloquentSluggable\Sluggable;
use Database\Factories\BrandFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'slug', 'code', 'short_description', 'logo_path', 'is_active'])]
class Brand extends Model
{
    /** @use HasFactory<BrandFactory> */
    use GeneratesCode, HasFactory, HasUlids, Sluggable;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected function codePrefix(): string
    {
        return 'BR';
    }

    protected function codeLength(): int
    {
        return 6;
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

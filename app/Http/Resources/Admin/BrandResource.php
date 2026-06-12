<?php

namespace App\Http\Resources\Admin;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin Brand */
class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array{
     *     id: string,
     *     name: string,
     *     slug: string,
     *     code: string,
     *     short_description: string|null,
     *     logo_url: string|null,
     *     is_active: bool,
     *     created_at: string|null
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'code' => $this->code,
            'short_description' => $this->short_description,
            'logo_url' => $this->logo_path === null ? null : Storage::disk('public')->url($this->logo_path),
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

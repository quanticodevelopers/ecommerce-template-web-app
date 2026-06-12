<?php

namespace App\Http\Resources\Admin;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Category */
class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array{
     *     id: string,
     *     name: string,
     *     slug: string,
     *     code: string,
     *     parent: array{id: string, name: string}|null,
     *     short_description: string|null,
     *     is_active: bool,
     *     children_count: int,
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
            'parent' => $this->whenLoaded('parent', function (): ?array {
                if ($this->parent === null) {
                    return null;
                }

                return [
                    'id' => $this->parent->id,
                    'name' => $this->parent->name,
                ];
            }),
            'short_description' => $this->short_description,
            'is_active' => $this->is_active,
            'children_count' => (int) ($this->children_count ?? 0),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

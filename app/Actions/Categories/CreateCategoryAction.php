<?php

namespace App\Actions\Categories;

use App\Models\Category;

class CreateCategoryAction
{
    /** @param array<string, mixed> $validated */
    public function handle(array $validated): Category
    {
        return Category::query()->create([
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'is_active' => true,
        ]);
    }
}

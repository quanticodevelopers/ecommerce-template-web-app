<?php

namespace App\Actions\Categories;

use App\Models\Category;

class UpdateCategoryAction
{
    /** @param array<string, mixed> $validated */
    public function handle(Category $category, array $validated): Category
    {
        $category->update([
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'is_active' => $validated['is_active'],
        ]);

        return $category;
    }
}

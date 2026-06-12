<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CategoryResource;
use App\Models\Category;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of root categories.
     */
    public function index(): Response
    {
        return $this->renderIndex(null);
    }

    /**
     * Display a listing of subcategories for the given category.
     */
    public function subcategories(Category $category): Response
    {
        return $this->renderIndex($category);
    }

    private function renderIndex(?Category $parentCategory): Response
    {
        $query = Category::query()
            ->select(['id', 'name', 'slug', 'code', 'parent_id', 'short_description', 'is_active', 'created_at'])
            ->with('parent:id,name')
            ->withCount('children')
            ->orderBy('name');

        if ($parentCategory === null) {
            $query->whereNull('parent_id');
        } else {
            $query->where('parent_id', $parentCategory->getKey());
        }

        $categories = $query->get();

        return Inertia::render('admin/categories/index', [
            'categories' => CategoryResource::collection($categories)->resolve(),
            'parent_category' => $parentCategory === null ? null : [
                'id' => $parentCategory->id,
                'name' => $parentCategory->name,
            ],
        ]);
    }
}

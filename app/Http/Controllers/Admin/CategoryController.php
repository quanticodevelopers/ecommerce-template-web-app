<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Http\Resources\Admin\CategoryResource;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
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

    /**
     * Store a newly created category.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $category = Category::query()->create([
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'is_active' => true,
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('actions.categories.created'),
        ]);

        if ($category->parent_id === null) {
            return to_route('admin.categories.index');
        }

        $parentCategory = Category::query()->findOrFail($category->parent_id);

        return to_route('admin.categories.subcategories', $parentCategory);
    }

    /**
     * Update the specified category.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $validated = $request->validated();

        $category->update([
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'] ?? null,
            'short_description' => $validated['short_description'] ?? null,
            'is_active' => $validated['is_active'],
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('actions.categories.updated'),
        ]);

        if ($category->parent_id === null) {
            return to_route('admin.categories.index');
        }

        $parentCategory = Category::query()->findOrFail($category->parent_id);

        return to_route('admin.categories.subcategories', $parentCategory);
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

        $categoryParentOptions = Category::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get()
            ->map(static fn (Category $category): array => [
                'value' => $category->id,
                'label' => $category->name,
            ])
            ->all();

        return Inertia::render('admin/categories/index', [
            'categories' => CategoryResource::collection($categories)->resolve(),
            'parent_category' => $parentCategory === null ? null : [
                'id' => $parentCategory->id,
                'name' => $parentCategory->name,
            ],
            'category_parent_options' => $categoryParentOptions,
        ]);
    }
}

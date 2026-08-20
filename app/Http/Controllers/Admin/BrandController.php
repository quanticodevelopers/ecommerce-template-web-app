<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Brands\CreateBrandAction;
use App\Actions\Brands\UpdateBrandAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBrandRequest;
use App\Http\Requests\Admin\UpdateBrandRequest;
use App\Http\Resources\Admin\BrandResource;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class BrandController extends Controller
{
    /**
     * Display a listing of brands.
     */
    public function index(): Response
    {
        $brands = Brand::query()
            ->select(['id', 'name', 'slug', 'code', 'short_description', 'logo_path', 'is_active', 'created_at'])
            ->orderBy('name')
            ->get();

        return Inertia::render('admin/brands/index', [
            'brands' => BrandResource::collection($brands)->resolve(),
        ]);
    }

    /**
     * Store a newly created brand.
     */
    public function store(StoreBrandRequest $request, CreateBrandAction $createBrand): RedirectResponse
    {
        try {
            $createBrand->handle($request->validated());
        } catch (\Throwable) {
            throw ValidationException::withMessages([
                'logo' => 'No se pudo procesar el logo de la marca.',
            ]);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('actions.brands.created'),
        ]);

        return to_route('admin.brands.index');
    }

    /**
     * Update the specified brand.
     */
    public function update(
        UpdateBrandRequest $request,
        Brand $brand,
        UpdateBrandAction $updateBrand,
    ): RedirectResponse {
        try {
            $updateBrand->handle($brand, $request->validated());
        } catch (\Throwable) {
            throw ValidationException::withMessages([
                'logo' => 'No se pudo procesar el logo de la marca.',
            ]);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('actions.brands.updated'),
        ]);

        return to_route('admin.brands.index');
    }
}

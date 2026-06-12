<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\BrandResource;
use App\Models\Brand;
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
}

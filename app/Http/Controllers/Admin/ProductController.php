<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Display a paginated listing of products.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->substr(0, 128)->toString();

        $products = Product::query()
            ->select([
                'id', 'brand_id', 'category_id', 'sku', 'barcode', 'name',
                'base_price', 'sale_price', 'flag', 'published_at', 'created_at',
            ])
            ->with([
                'brand:id,name',
                'category:id,name',
                'primaryImage' => fn ($query) => $query->select([
                    'product_images.id',
                    'product_images.product_id',
                    'product_images.path',
                    'product_images.variants',
                    'product_images.alt',
                    'product_images.position',
                ]),
            ])
            ->when($search !== '', function ($query) use ($search): void {
                $query->whereAny(['sku', 'name', 'barcode'], 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(50)
            ->withQueryString();

        return Inertia::render('admin/products/index', [
            'products' => ProductResource::collection($products),
            'filters' => [
                'search' => $search,
            ],
        ]);
    }
}

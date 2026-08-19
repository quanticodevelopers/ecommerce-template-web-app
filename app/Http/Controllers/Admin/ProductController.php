<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Products\CreateProductAction;
use App\Actions\Products\UpdateProductAction;
use App\Exceptions\ProductImageProcessingException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Http\Resources\Admin\ProductDetailResource;
use App\Http\Resources\Admin\ProductFormResource;
use App\Http\Resources\Admin\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Show the form for creating a product.
     */
    public function create(): Response
    {
        return Inertia::render('admin/products/create', [
            'brands' => Brand::query()
                ->select(['id', 'name'])
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'categories' => Category::query()
                ->select(['id', 'name'])
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request, CreateProductAction $createProduct): RedirectResponse
    {
        try {
            $createProduct->handle($request->validated());
        } catch (ProductImageProcessingException $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'images' => 'No se pudieron procesar las imágenes. Inténtalo nuevamente.',
            ]);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('actions.products.created'),
        ]);

        return to_route('admin.products.index');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product): Response
    {
        $product->load([
            'brand:id,name',
            'category:id,name',
            'images:id,product_id,path,variants,alt,position',
        ]);

        return Inertia::render('admin/products/show', [
            'product' => ProductDetailResource::make($product)->resolve(),
        ]);
    }

    /**
     * Show the form for editing a product.
     */
    public function edit(Product $product): Response
    {
        return Inertia::render('admin/products/edit', [
            'product' => ProductFormResource::make($product->load('images'))->resolve(),
            'brands' => Brand::query()
                ->select(['id', 'name'])
                ->where(function ($query) use ($product): void {
                    $query->where('is_active', true)
                        ->orWhere('id', $product->brand_id);
                })
                ->orderBy('name')
                ->get(),
            'categories' => Category::query()
                ->select(['id', 'name'])
                ->where(function ($query) use ($product): void {
                    $query->where('is_active', true)
                        ->orWhere('id', $product->category_id);
                })
                ->orderBy('name')
                ->get(),
        ]);
    }

    /**
     * Update the specified product.
     */
    public function update(
        UpdateProductRequest $request,
        Product $product,
        UpdateProductAction $updateProduct,
    ): RedirectResponse {
        try {
            $updateProduct->handle($product, $request->validated());
        } catch (ProductImageProcessingException $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'images' => 'No se pudieron procesar las imágenes. Inténtalo nuevamente.',
            ]);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('actions.products.updated'),
        ]);

        return to_route('admin.products.index');
    }

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

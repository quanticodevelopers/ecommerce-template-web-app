<?php

namespace App\Http\Requests\Admin;

use App\Enums\ProductFlag;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $nullableFields = ['short_description', 'description', 'base_price', 'flag'];
        $prepared = [];

        foreach (['name', 'sku', 'barcode', ...$nullableFields] as $field) {
            $value = $this->input($field);

            if (! is_string($value)) {
                continue;
            }

            $trimmedValue = trim($value);
            $prepared[$field] = in_array($field, $nullableFields, true) && $trimmedValue === ''
                ? null
                : $trimmedValue;
        }

        $this->merge($prepared);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $product = $this->route('product');
        $productId = $product instanceof Product ? $product->id : null;
        $brandId = $product instanceof Product ? $product->brand_id : null;
        $categoryId = $product instanceof Product ? $product->category_id : null;

        return [
            'name' => ['required', 'string', 'max:128'],
            'sku' => ['required', 'string', 'max:24', Rule::unique(Product::class, 'sku')->ignore($product)],
            'barcode' => ['required', 'digits:13', Rule::unique(Product::class, 'barcode')->ignore($product)],
            'brand_id' => [
                'required',
                'ulid',
                Rule::exists(Brand::class, 'id')->where(function (Builder $query) use ($brandId): void {
                    $query->where('is_active', true);

                    if (is_string($brandId)) {
                        $query->orWhere('id', $brandId);
                    }
                }),
            ],
            'category_id' => [
                'required',
                'ulid',
                Rule::exists(Category::class, 'id')->where(function (Builder $query) use ($categoryId): void {
                    $query->where('is_active', true);

                    if (is_string($categoryId)) {
                        $query->orWhere('id', $categoryId);
                    }
                }),
            ],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:100000'],
            'base_price' => ['nullable', 'numeric', 'decimal:0,2', 'min:0', 'gt:sale_price'],
            'sale_price' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'flag' => ['nullable', Rule::enum(ProductFlag::class)],
            'is_draft' => ['required', 'boolean'],
            'images' => ['required', 'array', 'min:1', 'max:5'],
            'images.*' => ['required', 'array:id,file'],
            'images.*.id' => [
                'nullable',
                'ulid',
                'distinct:strict',
                Rule::exists(ProductImage::class, 'id')->where('product_id', $productId),
            ],
            'images.*.file' => [
                'nullable',
                'file',
                'max:2048',
                'mimes:jpg,jpeg,png,webp,avif',
                'mimetypes:image/jpeg,image/png,image/webp,image/avif',
                'extensions:jpg,jpeg,png,webp,avif',
            ],
        ];
    }

    /**
     * Run the post-validation checks.
     *
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $images = $this->all()['images'] ?? null;

                if (! is_array($images)) {
                    return;
                }

                foreach ($images as $index => $image) {
                    if (! is_array($image)) {
                        continue;
                    }

                    $hasId = isset($image['id']) && is_string($image['id']) && $image['id'] !== '';
                    $hasFile = ($image['file'] ?? null) instanceof UploadedFile;

                    if ($hasId === $hasFile) {
                        $validator->errors()->add(
                            "images.{$index}",
                            'Cada posición debe contener una imagen existente o un archivo nuevo.',
                        );
                    }
                }
            },
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'base_price.gt' => 'El precio regular debe ser mayor que el precio de venta.',
            'images.required' => 'El producto debe conservar al menos una imagen.',
            'images.min' => 'El producto debe conservar al menos una imagen.',
            'images.max' => 'El producto puede tener como máximo 5 imágenes.',
            'images.*.id.distinct' => 'No puedes repetir una imagen existente.',
            'images.*.id.exists' => 'Una de las imágenes seleccionadas no pertenece al producto.',
            'images.*.file.max' => 'Cada imagen debe pesar como máximo 2 MB.',
            'images.*.file.mimes' => 'Las imágenes deben ser JPG, JPEG, PNG, WebP o AVIF.',
            'images.*.file.mimetypes' => 'Las imágenes deben ser JPG, JPEG, PNG, WebP o AVIF.',
            'images.*.file.extensions' => 'Las imágenes deben ser JPG, JPEG, PNG, WebP o AVIF.',
        ];
    }
}

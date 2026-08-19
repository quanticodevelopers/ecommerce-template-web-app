<?php

namespace App\Http\Requests\Admin;

use App\Enums\ProductFlag;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:128'],
            'sku' => ['required', 'string', 'max:24', Rule::unique(Product::class, 'sku')],
            'barcode' => ['required', 'digits:13', Rule::unique(Product::class, 'barcode')],
            'brand_id' => ['required', 'ulid', Rule::exists(Brand::class, 'id')->where('is_active', true)],
            'category_id' => ['required', 'ulid', Rule::exists(Category::class, 'id')->where('is_active', true)],
            'short_description' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:100000'],
            'base_price' => ['nullable', 'numeric', 'decimal:0,2', 'min:0', 'gt:sale_price'],
            'sale_price' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'flag' => ['nullable', Rule::enum(ProductFlag::class)],
            'is_draft' => ['required', 'boolean'],
            'images' => ['required', 'array', 'min:1', 'max:5'],
            'images.*' => [
                'required',
                'file',
                'max:2048',
                'mimes:jpg,jpeg,png,webp,avif',
                'mimetypes:image/jpeg,image/png,image/webp,image/avif',
                'extensions:jpg,jpeg,png,webp,avif',
            ],
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
            'images.required' => 'Debes seleccionar al menos una imagen.',
            'images.min' => 'Debes seleccionar al menos una imagen.',
            'images.max' => 'Puedes seleccionar un máximo de 5 imágenes.',
            'images.*.max' => 'Cada imagen debe pesar como máximo 2 MB.',
            'images.*.mimes' => 'Las imágenes deben ser JPG, JPEG, PNG, WebP o AVIF.',
            'images.*.mimetypes' => 'Las imágenes deben ser JPG, JPEG, PNG, WebP o AVIF.',
            'images.*.extensions' => 'Las imágenes deben ser JPG, JPEG, PNG, WebP o AVIF.',
        ];
    }
}

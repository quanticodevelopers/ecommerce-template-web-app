<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandRequest extends FormRequest
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
        $name = $this->input('name');
        $shortDescription = $this->input('short_description');

        $this->merge([
            'name' => is_string($name) ? trim($name) : $name,
            'short_description' => is_string($shortDescription)
                ? (trim($shortDescription) === '' ? null : trim($shortDescription))
                : $shortDescription,
        ]);
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
            'short_description' => ['nullable', 'string', 'max:128'],
            'is_active' => ['required', 'boolean'],
            'logo' => ['nullable', 'file', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp,avif'],
        ];
    }
}

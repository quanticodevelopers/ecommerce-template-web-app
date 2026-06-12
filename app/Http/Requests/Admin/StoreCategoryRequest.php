<?php

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
        $parentId = $this->input('parent_id');
        $shortDescription = $this->input('short_description');
        $name = $this->input('name');

        $this->merge([
            'parent_id' => $parentId === '__root__' || $parentId === '' ? null : $parentId,
            'short_description' => is_string($shortDescription)
                ? (trim($shortDescription) === '' ? null : trim($shortDescription))
                : $shortDescription,
            'name' => is_string($name)
                ? trim($name)
                : $name,
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
            'parent_id' => ['nullable', 'exists:categories,id'],
            'short_description' => ['nullable', 'string', 'max:128'],
        ];
    }
}

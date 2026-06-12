<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateCategoryRequest extends FormRequest
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
            'is_active' => ['required', 'boolean'],
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
                $category = $this->route('category');
                $parentId = $this->input('parent_id');

                if (! $category instanceof Category || $parentId === null) {
                    return;
                }

                if ($parentId === $category->getKey()) {
                    $validator->errors()->add('parent_id', __('validation.category_parent_self'));

                    return;
                }

                if (in_array($parentId, $category->descendantIds(), true)) {
                    $validator->errors()->add('parent_id', __('validation.category_parent_descendant'));
                }
            },
        ];
    }
}

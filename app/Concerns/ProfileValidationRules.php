<?php

namespace App\Concerns;

use App\Enums\UserDocumentType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

trait ProfileValidationRules
{
    /**
     * Get the validation rules used to validate user profiles.
     *
     * @param  class-string<Model>  $model
     * @param  array<string, mixed>  $input
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    protected function profileRules(string $model, ?string $userId = null, array $input = []): array
    {
        return [
            'document_type' => $this->documentTypeRules(),
            'document_number' => $this->documentNumberRules($model, $input, $userId),
            'name' => $this->nameRules(),
            'last_name' => $this->lastNameRules(),
            'email' => $this->emailRules($model, $userId),
            'phone' => $this->phoneRules(),
        ];
    }

    /**
     * Get the validation rules for document type.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function documentTypeRules(): array
    {
        return ['required', 'string', 'max:9', Rule::in(array_map(fn ($c) => $c->value, UserDocumentType::cases()))];
    }

    /**
     * Get the validation rules for document number.
     *
     * @param  class-string<Model>  $model
     * @param  array<string, mixed>  $input
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function documentNumberRules(string $model, array $input = [], ?string $userId = null): array
    {
        $type = $this->resolveDocumentType($input);

        $uniqueRule = Rule::unique($model)
            ->where(fn ($query) => $query->where('document_type', $type));

        if ($userId !== null) {
            $uniqueRule = $uniqueRule->ignore($userId);
        }

        if ($type === UserDocumentType::DNI->value) {
            // DNI: exactly 8 numeric digits
            $rules = ['required', 'digits:8'];
        } elseif ($type === UserDocumentType::CE->value || $type === UserDocumentType::PASAPORTE->value) {
            // CE and Pasaporte: alphanumeric up to 12 characters
            $rules = ['required', 'alpha_num', 'max:12'];
        } else {
            // Fallback: keep previous validation
            $rules = ['required', 'string', 'max:12'];
        }

        return array_merge($rules, [$uniqueRule]);
    }

    /**
     * @param  array<string, mixed>  $input
     */
    protected function resolveDocumentType(array $input = []): string
    {
        $type = $input['document_type'] ?? null;

        if ($type === null) {
            $type = $this->input('document_type');
        }

        return is_string($type) ? $type : '';
    }

    /**
     * Get the validation rules used to validate user names.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function nameRules(): array
    {
        return ['required', 'string', 'max:64'];
    }

    /**
     * Get the validation rules for last name.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function lastNameRules(): array
    {
        return ['required', 'string', 'max:64'];
    }

    /**
     * Get the validation rules used to validate user emails.
     *
     * @param  class-string<Model>  $model
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function emailRules(string $model, ?string $userId = null): array
    {
        return [
            'required',
            'string',
            'email',
            'max:128',
            $userId === null
                ? Rule::unique($model)
                : Rule::unique($model)->ignore($userId),
        ];
    }

    /**
     * Get the validation rules for phone.
     *
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function phoneRules(): array
    {
        return ['required', 'digits:9'];
    }
}

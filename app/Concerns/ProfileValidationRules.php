<?php

namespace App\Concerns;

use App\Enums\UserDocumentType;
use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

trait ProfileValidationRules
{
    /**
     * Get the validation rules used to validate user profiles.
     *
     * @return array<string, array<int, ValidationRule|array<mixed>|string>>
     */
    /**
     * @param  array<string, mixed>  $input
     */
    protected function profileRules(?string $userId = null, array $input = []): array
    {
        return [
            'document_type' => $this->documentTypeRules(),
            'document_number' => $this->documentNumberRules($input, $userId),
            'name' => $this->nameRules(),
            'last_name' => $this->lastNameRules(),
            'email' => $this->emailRules($userId),
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
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    /**
     * @param  array<string, mixed>  $input
     */
    protected function documentNumberRules(array $input = [], ?string $userId = null): array
    {
        $type = $this->resolveDocumentType($input);

        $uniqueRule = Rule::unique(User::class)
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

        if ($type === null && method_exists($this, 'input')) {
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
     * @return array<int, ValidationRule|array<mixed>|string>
     */
    protected function emailRules(?string $userId = null): array
    {
        return [
            'required',
            'string',
            'email',
            'max:128',
            $userId === null
                ? Rule::unique(User::class)
                : Rule::unique(User::class)->ignore($userId),
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

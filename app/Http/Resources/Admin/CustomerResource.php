<?php

namespace App\Http\Resources\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array{
     *     id: string,
     *     document_type: array{label: string, value: string},
     *     document_number: string,
     *     name: string,
     *     last_name: string,
     *     email: string,
     *     phone: string,
     *     role: array{label: string, value: string},
     *     is_active: bool,
     *     created_at: string|null
     * }
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'document_type' => [
                'label' => $this->document_type->label(),
                'value' => $this->document_type->value,
            ],
            'document_number' => $this->document_number,
            'name' => $this->name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => [
                'label' => $this->role->label(),
                'value' => $this->role->value,
            ],
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

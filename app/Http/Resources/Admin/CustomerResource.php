<?php

namespace App\Http\Resources\Admin;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use LogicException;

class CustomerResource extends JsonResource
{
    /**
     * @return array{
     *     id: string,
     *     kind: string,
     *     document_type: array{label: string, value: string},
     *     document_number: string,
     *     name: string,
     *     last_name: string,
     *     email: string,
     *     phone: string,
     *     created_at: string|null
     * }
     */
    public function toArray(Request $request): array
    {
        $customer = $this->customer();

        return [
            'id' => $customer->id,
            'kind' => 'customer',
            'document_type' => [
                'label' => $customer->document_type->label(),
                'value' => $customer->document_type->value,
            ],
            'document_number' => $customer->document_number,
            'name' => $customer->name,
            'last_name' => $customer->last_name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'created_at' => $customer->created_at?->toIso8601String(),
        ];
    }

    private function customer(): Customer
    {
        if (! $this->resource instanceof Customer) {
            throw new LogicException('CustomerResource requires a Customer model.');
        }

        return $this->resource;
    }
}

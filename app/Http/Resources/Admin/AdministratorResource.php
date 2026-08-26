<?php

namespace App\Http\Resources\Admin;

use App\Models\Administrator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use LogicException;

class AdministratorResource extends JsonResource
{
    /**
     * @return array{
     *     id: string,
     *     kind: string,
     *     name: string,
     *     last_name: string,
     *     email: string,
     *     phone: string,
     *     role: array{label: string, value: string},
     *     created_at: string|null
     * }
     */
    public function toArray(Request $request): array
    {
        $administrator = $this->administrator();

        return [
            'id' => $administrator->id,
            'kind' => 'administrator',
            'name' => $administrator->name,
            'last_name' => $administrator->last_name,
            'email' => $administrator->email,
            'phone' => $administrator->phone,
            'role' => [
                'label' => $administrator->role->label(),
                'value' => $administrator->role->value,
            ],
            'created_at' => $administrator->created_at?->toIso8601String(),
        ];
    }

    private function administrator(): Administrator
    {
        if (! $this->resource instanceof Administrator) {
            throw new LogicException('AdministratorResource requires an Administrator model.');
        }

        return $this->resource;
    }
}

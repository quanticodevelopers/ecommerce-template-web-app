<?php

namespace App\Enums;

use App\Concerns\HasLabelOptions;

enum UserRole: string
{
    use HasLabelOptions;

    case CUSTOMER = 'customer';
    case ADMIN = 'admin';
    case SUPER_ADMIN = 'super_admin';

    /**
     * @return array<string, string>
     */
    protected static function labels(): array
    {
        return [
            self::CUSTOMER->value => 'Cliente',
            self::ADMIN->value => 'Administrador',
            self::SUPER_ADMIN->value => 'Super Administrador',
        ];
    }
}

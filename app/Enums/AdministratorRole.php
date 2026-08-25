<?php

namespace App\Enums;

use App\Concerns\HasLabelOptions;

enum AdministratorRole: string
{
    use HasLabelOptions;

    case ADMIN = 'admin';
    case SUPER_ADMIN = 'super_admin';

    /** @return array<string, string> */
    protected static function labels(): array
    {
        return [
            self::ADMIN->value => 'Administrador',
            self::SUPER_ADMIN->value => 'Super Administrador',
        ];
    }
}

<?php

namespace App\Enums;

enum UserRole: string
{
    case CUSTOMER = 'customer';
    case ADMIN = 'admin';
    case SUPER_ADMIN = 'super_admin';

    public function label(): string
    {
        return match ($this) {
            self::CUSTOMER => 'Cliente',
            self::ADMIN => 'Administrador',
            self::SUPER_ADMIN => 'Super Administrador',
        };
    }
}

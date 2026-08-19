<?php

namespace App\Enums;

use App\Concerns\HasLabelOptions;

enum ProductFlag: string
{
    use HasLabelOptions;

    case FEATURED = 'featured';
    case NEW = 'new';

    /** @return array<string, string> */
    protected static function labels(): array
    {
        return [
            self::FEATURED->value => 'Destacado',
            self::NEW->value => 'Nuevo',
        ];
    }
}

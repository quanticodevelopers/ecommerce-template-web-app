<?php

namespace App\Enums;

use App\Concerns\HasLabelOptions;

enum UserDocumentType: string
{
    use HasLabelOptions;

    case DNI = 'dni';
    case CE = 'ce';
    case PASAPORTE = 'pasaporte';

    /**
     * @return array<string, string>
     */
    protected static function labels(): array
    {
        return [
            self::DNI->value => 'DNI',
            self::CE->value => 'Carnet de Extranjería',
            self::PASAPORTE->value => 'Pasaporte',
        ];
    }
}

<?php

namespace App\Enums;

enum UserDocumentType: string
{
    case DNI = 'dni';
    case CE = 'ce';
    case PASAPORTE = 'pasaporte';

    public function label(): string
    {
        return match ($this) {
            self::DNI => 'DNI',
            self::CE => 'Carnet de Extranjería',
            self::PASAPORTE => 'Pasaporte',
        };
    }

    public static function options(): array
    {
        return array_map(
            fn (self $case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases(),
        );
    }
}

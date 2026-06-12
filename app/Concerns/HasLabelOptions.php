<?php

namespace App\Concerns;

trait HasLabelOptions
{
    public function label(): string
    {
        return static::labels()[$this->value];
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases(),
        );
    }

    /**
     * @return array<string, string>
     */
    abstract protected static function labels(): array;
}

<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use LogicException;

trait GeneratesCode
{
    protected static function bootGeneratesCode(): void
    {
        static::creating(function (Model $model): void {
            if ($model->getAttribute($model->codeColumn()) !== null) {
                return;
            }

            $model->setAttribute($model->codeColumn(), static::generateUniqueCode($model));
        });
    }

    protected static function generateUniqueCode(Model $model): string
    {
        $prefix = $model->codePrefix();
        $suffixLength = $model->codeLength() - strlen($prefix);

        if ($suffixLength < 1) {
            throw new LogicException(sprintf(
                'The code length for %s must be greater than the prefix length.',
                $model::class,
            ));
        }

        do {
            $code = $prefix.Str::upper(Str::random($suffixLength));
        } while ($model::query()->where($model->codeColumn(), $code)->exists());

        return $code;
    }

    protected function codeColumn(): string
    {
        return 'code';
    }

    abstract protected function codePrefix(): string;

    abstract protected function codeLength(): int;
}

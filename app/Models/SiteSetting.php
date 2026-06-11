<?php

namespace App\Models;

use Database\Factories\SiteSettingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

#[Fillable(['key', 'value'])]
class SiteSetting extends Model
{
    /** @use HasFactory<SiteSettingFactory> */
    use HasFactory;

    public const string KEY_LOGO_PATH = 'logo_path';
    public const string KEY_SITE_NAME = 'site_name';
    public const string KEY_SITE_DESCRIPTION = 'site_description';
    public const string KEY_SITE_KEYWORDS = 'site_keywords';
    public const string KEY_FOOTER_CREDIT_NAME = 'footer_credit_name';
    public const string KEY_EMAIL = 'email';
    public const string KEY_PHONE = 'phone';
    public const string KEY_ADDRESS = 'address';

    /**
     * Get all stored settings as cached rows.
     *
     * @return array<int, array{key: string, value: string|null}>
     */
    public static function cachedRows(): array
    {
        return Cache::rememberForever(self::cacheKey(), function (): array {
            return self::query()
                ->orderBy('key')
                ->get(['key', 'value'])
                ->map(static fn (self $setting): array => [
                    'key' => $setting->key,
                    'value' => $setting->value,
                ])
                ->all();
        });
    }

    /**
     * Forget the cached configuration rows.
     */
    public static function forgetCache(): void
    {
        Cache::forget(self::cacheKey());
    }

    /**
     * Get the application defaults for every known setting.
     *
     * @return array<string, string|null>
     */
    public static function defaults(): array
    {
        return [
            self::KEY_LOGO_PATH => null,
            self::KEY_SITE_NAME => config('app.name'),
            self::KEY_SITE_DESCRIPTION => '',
            self::KEY_SITE_KEYWORDS => '',
            self::KEY_FOOTER_CREDIT_NAME => config('app.name'),
            self::KEY_EMAIL => '',
            self::KEY_PHONE => '',
            self::KEY_ADDRESS => '',
        ];
    }

    /**
     * Get a map of stored values keyed by setting name.
     *
     * @return Collection<string, string|null>
     */
    public static function values(): Collection
    {
        return collect(self::cachedRows())->pluck('value', 'key');
    }

    /**
     * Get a single setting value.
     */
    public static function value(string $key, mixed $default = null): mixed
    {
        return self::values()->get($key, $default);
    }

    /**
     * Insert or update multiple settings in one pass.
     *
     * @param  array<string, string|null>  $values
     */
    public static function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            self::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value],
            );
        }

        self::forgetCache();
    }

    /**
     * Build the cache key used for the rows payload.
     */
    protected static function cacheKey(): string
    {
        return Str::snake(class_basename(static::class)).'_rows_v3';
    }
}

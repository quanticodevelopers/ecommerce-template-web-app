<?php

namespace App\Http\Resources\Admin;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SiteSettingsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, string|null>
     */
    public function toArray(Request $request): array
    {
        $settings = collect($this->resource)
            ->mapWithKeys(function (mixed $setting): array {
                if (is_array($setting)) {
                    return [
                        (string) ($setting['key'] ?? '') => $setting['value'] ?? null,
                    ];
                }

                return [
                    (string) ($setting->key ?? '') => $setting->value ?? null,
                ];
            });

        $values = collect(SiteSetting::defaults())->mapWithKeys(
            fn (mixed $default, string $key): array => [
                $key => $settings->get($key, $default),
            ],
        );

        $logoPath = $values->get(SiteSetting::KEY_LOGO_PATH);

        return [
            'logo_url' => filled($logoPath) ? Storage::disk('public')->url($logoPath) : null,
            'site_name' => (string) $values->get(SiteSetting::KEY_SITE_NAME, config('app.name')),
            'site_description' => (string) $values->get(SiteSetting::KEY_SITE_DESCRIPTION, ''),
            'site_keywords' => (string) $values->get(SiteSetting::KEY_SITE_KEYWORDS, ''),
            'footer_credit_name' => (string) $values->get(SiteSetting::KEY_FOOTER_CREDIT_NAME, config('app.name')),
            'email' => (string) $values->get(SiteSetting::KEY_EMAIL, ''),
            'phone' => (string) $values->get(SiteSetting::KEY_PHONE, ''),
            'address' => (string) $values->get(SiteSetting::KEY_ADDRESS, ''),
        ];
    }
}

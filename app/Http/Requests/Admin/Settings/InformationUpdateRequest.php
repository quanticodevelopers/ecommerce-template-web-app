<?php

namespace App\Http\Requests\Admin\Settings;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class InformationUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string|Closure>
     */
    public function rules(): array
    {
        return [
            'logo' => [
                'nullable',
                'file',
                'max:2048',
                $this->logoRule(),
            ],
            'remove_logo' => ['sometimes', 'boolean'],
            'site_name' => ['required', 'string', 'max:128'],
            'site_description' => ['required', 'string', 'max:255'],
            'site_keywords' => ['required', 'string', 'max:255'],
            'footer_credit_name' => ['required', 'string', 'max:128'],
            'email' => ['required', 'string', 'email:rfc', 'max:128'],
            'phone' => ['required', 'string', 'digits:9'],
            'address' => ['required', 'string', 'max:128'],
        ];
    }

    /**
     * Build the validation rule for logo uploads.
     */
    private function logoRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (! $value instanceof UploadedFile) {
                return;
            }

            $extension = strtolower((string) ($value->getClientOriginalExtension() ?: $value->extension()));

            if ($extension === 'png') {
                $this->validatePngLogo($value, $fail);

                return;
            }

            if ($extension === 'svg') {
                $this->validateSvgLogo($value, $fail);

                return;
            }

            $fail('El logo debe ser un archivo PNG o SVG.');
        };
    }

    /**
     * Validate a PNG logo has the required dimensions.
     */
    private function validatePngLogo(UploadedFile $file, Closure $fail): void
    {
        $imageSize = @getimagesize($file->getRealPath());

        if ($imageSize === false) {
            $fail('El logo PNG debe ser una imagen valida.');

            return;
        }

        if ($imageSize[0] !== 512 || $imageSize[1] !== 512) {
            $fail('El logo PNG debe medir exactamente 512 x 512 px.');
        }
    }

    /**
     * Validate an SVG logo has the required dimensions.
     */
    private function validateSvgLogo(UploadedFile $file, Closure $fail): void
    {
        $contents = file_get_contents($file->getRealPath());

        if ($contents === false || ! $this->svgMatchesExpectedSize($contents)) {
            $fail('El logo SVG debe declarar medidas de 512 x 512 px.');
        }
    }

    /**
     * Determine whether the SVG declares 512 x 512 dimensions.
     */
    private function svgMatchesExpectedSize(string $contents): bool
    {
        if (! preg_match('/<svg\b[^>]*>/i', $contents, $svgTag)) {
            return false;
        }

        $tag = $svgTag[0];
        $width = $this->extractSvgNumericAttribute($tag, 'width');
        $height = $this->extractSvgNumericAttribute($tag, 'height');

        if ($width === 512 && $height === 512) {
            return true;
        }

        if (! preg_match('/viewBox=["\']([^"\']+)["\']/i', $tag, $viewBoxMatch)) {
            return false;
        }

        $viewBoxParts = preg_split('/\s+/', trim($viewBoxMatch[1]));

        if ($viewBoxParts === false || count($viewBoxParts) !== 4) {
            return false;
        }

        return (float) $viewBoxParts[0] === 0.0
            && (float) $viewBoxParts[1] === 0.0
            && (int) $viewBoxParts[2] === 512
            && (int) $viewBoxParts[3] === 512;
    }

    /**
     * Extract a numeric SVG attribute value.
     */
    private function extractSvgNumericAttribute(string $tag, string $attribute): ?int
    {
        if (! preg_match('/'.preg_quote($attribute, '/').'=["\']([^"\']+)["\']/i', $tag, $match)) {
            return null;
        }

        $value = preg_replace('/[^0-9.]/', '', $match[1]);

        if ($value === '' || ! is_numeric($value)) {
            return null;
        }

        return (int) round((float) $value);
    }
}

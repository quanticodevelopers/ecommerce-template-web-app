<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Settings\InformationUpdateRequest;
use App\Http\Resources\Admin\SiteSettingsResource;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class InformationController extends Controller
{
    /**
     * Show the site information form.
     */
    public function edit(): Response
    {
        return Inertia::render('admin/settings/information', [
            'settings' => SiteSettingsResource::make(SiteSetting::cachedRows())->resolve(),
        ]);
    }

    /**
     * Update the site information.
     */
    public function update(InformationUpdateRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $logo = $request->file('logo');
        $removeLogo = (bool) ($validated['remove_logo'] ?? false);
        $currentLogoPath = SiteSetting::value(SiteSetting::KEY_LOGO_PATH);

        unset($validated['logo'], $validated['remove_logo']);
        $validated['site_keywords'] = $this->normalizeKeywords($validated['site_keywords']);

        if ($logo instanceof UploadedFile) {
            $validated[SiteSetting::KEY_LOGO_PATH] = $this->storeLogo($logo);
        } elseif ($removeLogo) {
            $validated[SiteSetting::KEY_LOGO_PATH] = null;
        }

        SiteSetting::setMany($validated);

        if (($logo instanceof UploadedFile || $removeLogo) && filled($currentLogoPath)) {
            Storage::disk('public')->delete($currentLogoPath);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Site information updated.'),
        ]);

        return to_route('admin.information.edit');
    }

    /**
     * Store the uploaded logo on the public disk.
     */
    private function storeLogo(UploadedFile $logo): string
    {
        $extension = strtolower((string) ($logo->getClientOriginalExtension() ?: $logo->extension() ?: 'png'));
        $fileName = Str::uuid()->toString().'.'.$extension;

        return $logo->storePubliclyAs('site-settings', $fileName, 'public');
    }

    /**
     * Normalize the comma-separated keywords string.
     */
    private function normalizeKeywords(string $keywords): string
    {
        return collect(explode(',', $keywords))
            ->map(static fn (string $keyword): string => trim($keyword))
            ->filter()
            ->implode(', ');
    }
}

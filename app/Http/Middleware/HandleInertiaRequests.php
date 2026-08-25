<?php

namespace App\Http\Middleware;

use App\Http\Resources\Admin\AdministratorResource;
use App\Http\Resources\Admin\CustomerResource;
use App\Http\Resources\Admin\SiteSettingsResource;
use App\Models\Administrator;
use App\Models\Customer;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'url' => config('app.url'),
            'auth' => [
                'user' => match (true) {
                    $user instanceof Administrator => AdministratorResource::make($user)->resolve($request),
                    $user instanceof Customer => CustomerResource::make($user)->resolve($request),
                    default => null,
                },
            ],
            'site' => SiteSettingsResource::make(SiteSetting::cachedRows())->resolve($request),
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}

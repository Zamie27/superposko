<?php

namespace App\Http\Middleware;

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
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user() ? array_merge($request->user()->toArray(), [
                    'display_role' => $request->user()->host_id ? ucfirst($request->user()->role) : ($request->user()->role === 'host' ? 'Host' : 'User'),
                    'is_subscribed' => $request->user()->role === 'host', // Assuming host role means subscribed for now
                ]) : null,
            ],
            'immich' => \Illuminate\Support\Facades\Cache::remember('immich_storage', 300, function () {
                $url = rtrim(config('services.immich.url', ''), '/');
                $apiKey = config('services.immich.api_key', '');
                if (!$apiKey) return null;
                try {
                    $response = \Illuminate\Support\Facades\Http::withHeaders([
                        'x-api-key' => $apiKey,
                        'Accept' => 'application/json',
                    ])->get("{$url}/api/users/me");
                    if ($response->successful()) {
                        $data = $response->json();
                        return [
                            'quotaSizeInBytes' => $data['quotaSizeInBytes'] ?? 0,
                            'quotaUsageInBytes' => $data['quotaUsageInBytes'] ?? 0,
                        ];
                    }
                } catch (\Exception $e) {}
                return null;
            }),
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}

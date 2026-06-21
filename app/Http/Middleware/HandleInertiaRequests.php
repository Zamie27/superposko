<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
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
            'preorder_promo_active' => filter_var(Setting::get('preorder_promo_active', '1'), FILTER_VALIDATE_BOOLEAN),
            'otp_sent' => $request->session()->get('otp_sent', false),
            'new_email_attempt' => $request->session()->get('new_email_attempt', ''),
            'auth' => [
                'user' => $request->user() ? array_merge($request->user()->toArray(), [
                    'display_role' => $request->user()->role === 'admin' ? 'Admin' : ($request->user()->host_id ? ucfirst($request->user()->role) : ($request->user()->role === 'host' ? 'Host' : 'User')),
                    'is_subscribed' => $request->user()->role === 'admin' ||
                        ($request->user()->role === 'host' && ($request->user()->subscription_expires_at === null || $request->user()->subscription_expires_at->isFuture())) ||
                        ($request->user()->host_id && $request->user()->host && ($request->user()->host->subscription_expires_at === null || $request->user()->host->subscription_expires_at->isFuture())),
                ]) : null,
            ],
            'immich' => Cache::remember('immich_storage', 300, function () {
                $url = rtrim(config('services.immich.url', ''), '/');
                $apiKey = config('services.immich.api_key', '');
                if (! $apiKey) {
                    return null;
                }
                try {
                    $response = Http::withHeaders([
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
                } catch (\Exception $e) {
                }

                return null;
            }),
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}

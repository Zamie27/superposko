<?php

namespace App\Http\Middleware;

use App\Helpers\RoleConfig;
use App\Models\Setting;
use App\Models\User;
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
            'footer_phone' => Setting::get('footer_phone', '6285171739232'),
            'preorder_promo_active' => filter_var(Setting::get('preorder_promo_active', '1'), FILTER_VALIDATE_BOOLEAN),
            'otp_sent' => $request->session()->get('otp_sent', false),
            'new_email_attempt' => $request->session()->get('new_email_attempt', ''),
            'vapid_public_key' => config('services.vapid.public_key'),
            'auth' => [
                'user' => $request->user() ? array_merge($request->user()->toArray(), [
                    'display_role' => $this->resolveDisplayRole($request->user()),
                    'is_subscribed' => $request->user()->role === 'admin' ||
                        $request->user()->role === 'trial' ||
                        (is_null($request->user()->host_id) && ($request->user()->subscription_expires_at === null || $request->user()->subscription_expires_at->isFuture())) ||
                        ($request->user()->host_id && $request->user()->host && ($request->user()->host->subscription_expires_at === null || $request->user()->host->subscription_expires_at->isFuture())),
                ]) : null,
            ],
            'dpl' => $request->user() && in_array($request->user()->role, ['dpl', 'admin']) ? [
                'active_host_id' => $request->user()->host_id,
                'poskos' => User::whereNull('host_id')
                    ->whereIn('role', ['host', 'ketua', 'trial'])
                    ->select('id', 'name', 'university', 'group_number')
                    ->get(),
            ] : null,
            'immich' => $request->user() ? Cache::remember('immich_storage_'.($request->user()->host_id ?? $request->user()->id), 30, function () use ($request) {
                $user = $request->user();
                $hostId = $user->host_id ?? $user->id;
                $host = User::find($hostId);

                if (! $host || ! $host->immich_api_key) {
                    return null;
                }

                $url = rtrim(Setting::get('immich_url', config('services.immich.url', '')), '/');
                $apiKey = $host->immich_api_key;

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
            }) : null,
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }

    /**
     * Resolve a human-friendly display role name for the user.
     */
    private function resolveDisplayRole(User $user): string
    {
        if ($user->role === 'admin') {
            return 'Admin';
        }

        if ($user->role === 'trial') {
            $daysLeft = (int) max(0, ceil(now()->diffInSeconds($user->trial_ends_at ?? $user->created_at->addDays(5), false) / 86400));

            return 'Trial (' . $daysLeft . ' hari)';
        }

        if ($user->role === 'user') {
            return 'User';
        }

        return RoleConfig::getRoleLabel($user->role);
    }
}

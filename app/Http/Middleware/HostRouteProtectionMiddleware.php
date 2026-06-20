<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HostRouteProtectionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect()->route('login');
        }

        // Admins can do everything
        if ($user->role === 'admin') {
            return $next($request);
        }

        // If preorder promo is disabled, allow access to payment test routes for users to pay/subscribe
        $preorderPromoActive = filter_var(Setting::get('preorder_promo_active', '1'), FILTER_VALIDATE_BOOLEAN);
        if (! $preorderPromoActive && $request->is('host/payment/test*')) {
            return $next($request);
        }

        // Host and Member can access all host routes
        if (in_array($user->role, ['host', 'member'])) {
            return $next($request);
        }

        // User role is restricted:
        // Allow GET requests (to view locked/dummy views in frontend)
        // Restrict POST/PUT/PATCH/DELETE requests
        if ($request->isMethod('GET')) {
            return $next($request);
        }

        return $request->expectsJson()
            ? response()->json(['message' => 'Layanan ini hanya tersedia setelah berlangganan.'], 403)
            : abort(403, 'Layanan ini hanya tersedia setelah berlangganan.');
    }
}

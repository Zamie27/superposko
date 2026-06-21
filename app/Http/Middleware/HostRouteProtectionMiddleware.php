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

        // Host and Member can access all host routes
        $isHostOrMember = $user->role === 'host' || !is_null($user->host_id);
        if ($isHostOrMember) {
            return $next($request);
        }

        // For other users (unsubscribed):
        // Allow GET requests for dashboard and documentation index/files
        $allowedRoutes = ['host.dashboard', 'host.documentation.index', 'host.documentation.file', 'host.documentation.thumbnail'];
        if ($request->isMethod('GET') && in_array($request->route()?->getName(), $allowedRoutes)) {
            return $next($request);
        }

        return $request->expectsJson()
            ? response()->json(['message' => 'Layanan ini hanya tersedia setelah berlangganan.'], 403)
            : abort(403, 'Layanan ini hanya tersedia setelah berlangganan.');
    }
}

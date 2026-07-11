<?php

namespace App\Http\Middleware;

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

        // Check if user is trial and trying to access locked features (Documentation & Members)
        if ($user->role === 'trial') {
            $routeName = $request->route()?->getName();
            if ($routeName && (
                str_starts_with($routeName, 'host.documentation') ||
                str_starts_with($routeName, 'management.members')
            )) {
                if (! $request->isMethod('GET')) {
                    return $request->expectsJson()
                        ? response()->json(['message' => 'Fitur ini tidak tersedia selama masa trial.'], 403)
                        : abort(403, 'Fitur ini tidak tersedia selama masa trial.');
                }
            }
        }

        // Owner (host_id is null), trial, and Member (host_id is set) can access all host routes
        $isHostOrMember = is_null($user->host_id) || $user->role === 'trial' || ! is_null($user->host_id);
        if ($isHostOrMember) {
            return $next($request);
        }

        // For other users (unsubscribed):
        // Allow GET requests only for dashboard and documentation to show lock screens/notices
        if ($request->isMethod('GET')) {
            $routeName = $request->route()?->getName();
            if ($routeName === 'host.dashboard' || ($routeName && str_starts_with($routeName, 'host.documentation'))) {
                return $next($request);
            }
        }

        return $request->expectsJson()
            ? response()->json(['message' => 'Layanan ini hanya tersedia setelah berlangganan.'], 403)
            : abort(403, 'Layanan ini hanya tersedia setelah berlangganan.');
    }
}

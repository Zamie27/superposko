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

        // Host, trial, and Member can access all host routes
        $isHostOrMember = $user->role === 'host' || $user->role === 'trial' || ! is_null($user->host_id);
        if ($isHostOrMember) {
            return $next($request);
        }

        // For other users (unsubscribed):
        // Allow GET requests for all host routes so they can see the layout-level premium lock overlay on the frontend
        if ($request->isMethod('GET')) {
            return $next($request);
        }

        return $request->expectsJson()
            ? response()->json(['message' => 'Layanan ini hanya tersedia setelah berlangganan.'], 403)
            : abort(403, 'Layanan ini hanya tersedia setelah berlangganan.');
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->banned_at !== null) {
            // Allow logout and the banned page itself
            if (! $request->is('banned') && ! $request->is('logout')) {
                return redirect()->route('banned');
            }
        }

        return $next($request);
    }
}

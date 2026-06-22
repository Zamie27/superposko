<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTrialExpiration
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'trial') {
            if ($user->created_at->addDays(5)->isPast()) {
                $user->update(['role' => 'user']);
            }
        }

        return $next($request);
    }
}

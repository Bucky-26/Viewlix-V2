<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    /**
     * Handle an incoming request and track authenticated user's presence.
     *
     * - Sets a short-lived "user-is-online-{id}" cache key (5 minutes) so
     *   we can quickly determine online presence.
     * - Updates a longer-lived "user-last-active-{id}" cache key with the
     *   latest timestamp for display in admin UI.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        try {
            $user = $request->user();
            if ($user) {
                $onlineKey = 'user-is-online-' . $user->id;
                $lastKey = 'user-last-active-' . $user->id;

                // Mark user as online for 5 minutes
                Cache::put($onlineKey, true, now()->addMinutes(5));

                // Store a human-readable timestamp (kept for longer for stats)
                Cache::put($lastKey, now()->toDateTimeString(), now()->addDays(7));
            }
        } catch (\Throwable $e) {
            // Do not interrupt the request on caching errors.
        }

        return $response;
    }
}

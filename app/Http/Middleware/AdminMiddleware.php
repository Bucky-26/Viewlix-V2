<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $secretParam = config('admin.secret_param', 'token');
        $staticToken = config('admin.secret_token');

        // Check if the secret gate token is supplied via query parameter
        if ($request->filled($secretParam)) {
            $providedToken = (string) $request->query($secretParam);

            // 1. Check one-time dynamic token generated on button click (consumed immediately)
            $cachedUserId = Cache::pull('admin_gate_token:' . $providedToken);

            if ($cachedUserId && $request->user() && $cachedUserId === $request->user()->id && $request->user()->isAdmin()) {
                $request->session()->put('admin_gate_unlocked', true);
                $request->session()->put('admin_gate_unlocked_user', $request->user()->id);
            }
            // 2. Check static token fallback if configured in .env
            elseif (!empty($staticToken) && hash_equals((string) $staticToken, $providedToken)) {
                if ($request->user() && $request->user()->isAdmin()) {
                    $request->session()->put('admin_gate_unlocked', true);
                    $request->session()->put('admin_gate_unlocked_user', $request->user()->id);
                }
            }
        }

        // If not authenticated, abort 404 (do not redirect to login so pentester cannot find it)
        if (!$request->user()) {
            abort(404);
        }

        // If not admin, abort 404
        if (!$request->user()->isAdmin()) {
            abort(404);
        }

        // Auto-verify email for authenticated admin if not already set
        if ($request->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&
            !$request->user()->hasVerifiedEmail()) {
            $request->user()->markEmailAsVerified();
        }

        // Auto-unlock gate for authenticated administrator
        $request->session()->put('admin_gate_unlocked', true);
        $request->session()->put('admin_gate_unlocked_user', $request->user()->id);

        return $next($request);
    }
}

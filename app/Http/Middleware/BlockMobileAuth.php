<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockMobileAuth
{
    /**
     * Block access to auth pages on mobile devices.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->isMobile($request)) {
            abort(404);
        }

        return $next($request);
    }

    /**
     * Detect if the request is from a mobile device.
     */
    protected function isMobile(Request $request): bool
    {
        $userAgent = $request->header('User-Agent', '');

        // Common mobile device patterns
        $mobilePatterns = [
            '/Mobile/i',
            '/Android.*Mobile/i',
            '/iPhone/i',
            '/iPod/i',
            '/Windows Phone/i',
            '/BlackBerry/i',
            '/Opera Mini/i',
            '/IEMobile/i',
            '/webOS/i',
        ];

        foreach ($mobilePatterns as $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return true;
            }
        }

        return false;
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\UserSession;
use App\Services\DeviceDetectionService;
use App\Services\GeoIPService;

class LogUserSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log if user is authenticated
        if (auth()->check()) {
            $user = auth()->user();
            $ipAddress = $request->ip();
            $userAgent = $request->userAgent();

            // Get device detection service
            $deviceService = new DeviceDetectionService();
            $deviceInfo = $deviceService->parse($userAgent);

            // Get geo IP information
            $geoIPService = new GeoIPService();
            $geoInfo = $geoIPService->getLocationFromIP($ipAddress);

            // Check if there's an active session for this user
            $session = UserSession::where('user_id', $user->id)
                ->where('ip_address', $ipAddress)
                ->where('is_active', true)
                ->first();

            if ($session) {
                // Update last activity
                $session->updateActivity();
            } else {
                // Create new session record
                UserSession::create([
                    'user_id' => $user->id,
                    'ip_address' => $ipAddress,
                    'device_type' => $deviceInfo['device_type'],
                    'browser' => $deviceInfo['browser'],
                    'os' => $deviceInfo['os'],
                    'user_agent' => $userAgent,
                    'login_at' => now(),
                    'last_activity_at' => now(),
                    'is_active' => true,
                    'country' => $geoInfo['country'],
                    'city' => $geoInfo['city'],
                ]);
            }
        }

        return $response;
    }
}

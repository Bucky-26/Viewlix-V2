<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GeoIPService
{
    /**
     * Get location information from IP address
     * Uses ip-api.com free service (no API key needed)
     */
    public function getLocationFromIP(string $ipAddress): array
    {
        // Skip if localhost
        if (in_array($ipAddress, ['127.0.0.1', '::1', 'localhost'])) {
            return [
                'country' => 'Local',
                'city' => 'Localhost',
            ];
        }

        // Try to get from cache first
        $cacheKey = "geo_ip_{$ipAddress}";
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            // Call free GeoIP API
            $response = Http::timeout(3)
                ->get('http://ip-api.com/json/' . $ipAddress, [
                    'fields' => 'country,city,status',
                ])
                ->json();

            if ($response['status'] === 'success') {
                $data = [
                    'country' => $response['country'] ?? 'Unknown',
                    'city' => $response['city'] ?? 'Unknown',
                ];
            } else {
                $data = [
                    'country' => 'Unknown',
                    'city' => 'Unknown',
                ];
            }
        } catch (\Exception $e) {
            // If API fails, return unknown
            $data = [
                'country' => 'Unknown',
                'city' => 'Unknown',
            ];
        }

        // Cache for 7 days
        Cache::put($cacheKey, $data, now()->addDays(7));

        return $data;
    }

    /**
     * Alternative method using database lookup (if you have geoip database installed)
     * Install with: composer require geoip2/geoip2
     */
    public function getLocationFromIPDatabase(string $ipAddress): array
    {
        try {
            if (!class_exists('GeoIp2\Database\Reader')) {
                return [
                    'country' => 'Unknown',
                    'city' => 'Unknown',
                ];
            }

            $reader = new \GeoIp2\Database\Reader(database_path('geoip/GeoLite2-City.mmdb'));
            $response = $reader->city($ipAddress);

            return [
                'country' => $response->country->name ?? 'Unknown',
                'city' => $response->city->name ?? 'Unknown',
            ];
        } catch (\Exception $e) {
            return [
                'country' => 'Unknown',
                'city' => 'Unknown',
            ];
        }
    }
}

<?php

namespace App\Services;

class DeviceDetectionService
{
    /**
     * Parse user agent to detect device, browser, and OS
     */
    public function parse(string $userAgent): array
    {
        $deviceType = $this->detectDeviceType($userAgent);
        $browser = $this->detectBrowser($userAgent);
        $os = $this->detectOS($userAgent);

        return [
            'device_type' => $deviceType,
            'browser' => $browser,
            'os' => $os,
        ];
    }

    /**
     * Detect device type from user agent
     */
    private function detectDeviceType(string $userAgent): string
    {
        $userAgent = strtolower($userAgent);

        if (preg_match('/iphone|ipod|ipad/', $userAgent)) {
            return 'ios';
        }
        if (preg_match('/android/', $userAgent)) {
            return 'mobile';
        }
        if (preg_match('/tablet|ipad|playbook|silk/', $userAgent)) {
            return 'tablet';
        }
        if (preg_match('/mobile|android|webos/', $userAgent)) {
            return 'mobile';
        }

        return 'desktop';
    }

    /**
     * Detect browser from user agent
     */
    private function detectBrowser(string $userAgent): string
    {
        $userAgent = strtolower($userAgent);

        if (preg_match('/firefox/', $userAgent)) {
            return 'Firefox';
        }
        if (preg_match('/chrome/', $userAgent)) {
            return 'Chrome';
        }
        if (preg_match('/safari/', $userAgent)) {
            return 'Safari';
        }
        if (preg_match('/edg/', $userAgent)) {
            return 'Edge';
        }
        if (preg_match('/opera|opr/', $userAgent)) {
            return 'Opera';
        }
        if (preg_match('/msie|trident/', $userAgent)) {
            return 'Internet Explorer';
        }

        return 'Unknown';
    }

    /**
     * Detect OS from user agent
     */
    private function detectOS(string $userAgent): string
    {
        $userAgent = strtolower($userAgent);

        if (preg_match('/windows|win32/', $userAgent)) {
            return 'Windows';
        }
        if (preg_match('/macintosh|macintel/', $userAgent)) {
            return 'macOS';
        }
        if (preg_match('/iphone|ipod|ipad|ios/', $userAgent)) {
            return 'iOS';
        }
        if (preg_match('/android/', $userAgent)) {
            return 'Android';
        }
        if (preg_match('/linux/', $userAgent)) {
            return 'Linux';
        }

        return 'Unknown';
    }
}

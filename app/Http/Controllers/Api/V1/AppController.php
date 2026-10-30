<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\AppSetting;
use App\Models\Banner;
use App\Models\MobileApp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppController extends BaseApiController
{
    /**
     * Get essential application configuration, metadata, and service parameters.
     * GET /api/v1/app/config
     */
    public function config(): JsonResponse
    {
        $fb = config('services.firebase', []);

        $firebaseConfig = [
            'apiKey'            => $fb['api_key'] ?? null,
            'authDomain'        => $fb['auth_domain'] ?? null,
            'databaseURL'       => $fb['database_url'] ?? null,
            'projectId'         => $fb['project_id'] ?? null,
            'storageBucket'     => $fb['storage_bucket'] ?? null,
            'messagingSenderId' => $fb['messaging_sender_id'] ?? null,
            'appId'             => $fb['app_id'] ?? null,
        ];

        $latestAndroid = null;
        $latestIos = null;
        $banners = collect();
        $maintenanceMode = false;

        try {
            $latestAndroid = MobileApp::android();
            $latestIos = MobileApp::ios();
            $banners = Banner::active()->map(function ($banner) {
                return [
                    'id'          => $banner->id,
                    'type'        => $banner->type,
                    'title'       => $banner->title,
                    'description' => $banner->description,
                    'image'       => $banner->image,
                    'icon'        => $banner->icon,
                    'button_text' => $banner->button_text,
                    'button_url'  => $banner->button_url,
                    'hotlinks'    => $banner->hotlinks ?? [],
                ];
            });
            $maintenanceMode = (bool) AppSetting::getVal('maintenance_mode', false);
            $forceUpdate = (bool) AppSetting::getVal('force_update', false);
            $playerUrl = AppSetting::getVal('player_url', config('app.player_url', 'https://content1.viewlix.online/'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('AppConfig DB warning: ' . $e->getMessage());
            $forceUpdate = false;
            $playerUrl = config('app.player_url', 'https://content1.viewlix.online/');
        }

        $androidDownloadUrl = $latestAndroid?->play_store_url ?: url('/download/android');
        $androidLatestApk = ($latestAndroid && $latestAndroid->file_path)
            ? asset('storage/' . $latestAndroid->file_path)
            : url('/download/android');

        return $this->success([
            'app' => [
                'name'             => config('app.name', 'Viewlix'),
                'url'              => config('app.url'),
                'player_url'       => $playerUrl,
                'maintenance_mode' => $maintenanceMode,
                'force_update'     => $forceUpdate,
            ],
            'player_url' => $playerUrl,
            'images' => [
                'tmdb_base_url'    => config('services.tmdb.image_base_url', 'https://image.tmdb.org/t/p'),
                'poster_sizes'     => ['w92', 'w154', 'w185', 'w342', 'w500', 'w780', 'original'],
                'backdrop_sizes'   => ['w300', 'w780', 'w1280', 'original'],
                'proxy_enabled'    => true,
            ],
            'firebase' => [
                'config'   => $firebaseConfig,
                'vapidKey' => $fb['vapid_key'] ?? null,
            ],
            'mobile_versions' => [
                'android' => [
                    'version'        => $latestAndroid?->version ?? '1.0.0',
                    'download_url'   => $androidDownloadUrl,
                    'latest_apk'     => $androidLatestApk,
                    'play_store_url' => $latestAndroid?->play_store_url,
                    'file_size'      => $latestAndroid?->file_size,
                    'force_update'   => $forceUpdate,
                ],
                'ios' => $latestIos ? [
                    'version'       => $latestIos->version,
                    'app_store_url' => $latestIos->app_store_url,
                    'force_update'  => $forceUpdate,
                ] : null,
            ],
            'banners' => $banners,
        ]);
    }

    /**
     * Check if a mobile app update is available.
     * GET /api/v1/app/check-update
     */
    public function checkUpdate(Request $request): JsonResponse
    {
        $platform = strtolower($request->query('platform', 'android'));
        $currentVersion = $request->query('current_version', '0.0.0');

        try {
            $app = match ($platform) {
                'ios' => MobileApp::ios(),
                default => MobileApp::android(),
            };
        } catch (\Throwable $e) {
            $app = null;
        }

        if (!$app) {
            return $this->success([
                'update_available' => false,
                'force_update'     => false,
                'latest_version'   => $currentVersion,
                'message'          => 'No release information found for this platform.',
            ]);
        }

        $latestVersion = $app->version;
        $isNewer = version_compare($latestVersion, $currentVersion, '>');

        // Check for major version leap which could indicate breaking changes requiring force update
        $currentMajor = (int) explode('.', $currentVersion)[0];
        $latestMajor = (int) explode('.', $latestVersion)[0];
        $forceUpdate = $isNewer && ($latestMajor > $currentMajor || (bool) AppSetting::getVal("force_update_{$platform}", false));

        $downloadUrl = $platform === 'ios' 
            ? $app->app_store_url 
            : ($app->play_store_url ?: ($app->file_path ? url('download/android') : null));

        return $this->success([
            'update_available' => $isNewer,
            'force_update'     => $forceUpdate,
            'current_version'  => $currentVersion,
            'latest_version'   => $latestVersion,
            'platform'         => $platform,
            'download_url'     => $downloadUrl,
            'file_size'        => $app->file_size,
            'download_count'   => $app->download_count,
        ]);
    }

    /**
     * Get active banners / carousel promos.
     * GET /api/v1/banners
     */
    public function banners(): JsonResponse
    {
        $banners = Banner::active()->map(function ($banner) {
            return [
                'id'          => $banner->id,
                'type'        => $banner->type,
                'title'       => $banner->title,
                'description' => $banner->description,
                'image'       => $banner->image,
                'icon'        => $banner->icon,
                'button_text' => $banner->button_text,
                'button_url'  => $banner->button_url,
                'hotlinks'    => $banner->hotlinks ?? [],
            ];
        });

        return $this->success($banners);
    }
}

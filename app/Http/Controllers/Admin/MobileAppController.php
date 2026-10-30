<?php

namespace App\Http\Controllers\Admin;

use App\Models\AppSetting;
use App\Models\MobileApp;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MobileAppController extends Controller
{
    /**
     * Show the Android app management page
     */
    public function index()
    {
        $androidApp = MobileApp::android();
        $androidDownloads = $androidApp?->download_count ?? 0;
        $playerUrl = AppSetting::getVal('player_url', config('app.player_url', 'https://content1.viewlix.online/'));
        $maintenanceMode = (bool) AppSetting::getVal('maintenance_mode', false);
        $forceUpdate = (bool) AppSetting::getVal('force_update', false);

        return view('admin.mobile-app', compact(
            'androidApp',
            'androidDownloads',
            'playerUrl',
            'maintenanceMode',
            'forceUpdate'
        ));
    }

    /**
     * Store or update Android app
     */
    public function store(Request $request, $platform)
    {
        // Only allow android
        if ($platform !== 'android') {
            return redirect()->back()->with('error', 'Only Android apps are supported');
        }

        $validated = $request->validate([
            'version' => 'required|string|max:20',
            'app_file' => 'nullable|file',
            'player_url' => 'nullable|string|max:500',
        ]);

        // Validate APK file extension
        if ($request->hasFile('app_file')) {
            $file = $request->file('app_file');
            if ($file->getClientOriginalExtension() !== 'apk') {
                return back()->withErrors(['app_file' => 'The file must be an APK file (Android Package)']);
            }
        }

        // Find or create
        $app = MobileApp::where('platform', 'android')->first();
        if (!$app) {
            $app = new MobileApp();
            $app->platform = 'android';
        }

        // Update version
        $app->version = $validated['version'];

        // Handle file upload
        if ($request->hasFile('app_file')) {
            $file = $request->file('app_file');
            $path = $file->store('mobile-apps/android', 'public');
            $app->file_path = $path;
            $app->file_size = $file->getSize();
        }

        $app->save();

        // Update app settings for player_url, maintenance_mode, and force_update
        if ($request->filled('player_url')) {
            AppSetting::setVal('player_url', $request->input('player_url'));
        }
        AppSetting::setVal('maintenance_mode', $request->boolean('maintenance_mode'));
        AppSetting::setVal('force_update', $request->boolean('force_update'));

        // Automatically sync to Firebase RTDB
        try {
            app(FirebaseService::class)->syncMobileAppConfig();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Firebase sync warning: ' . $e->getMessage());
        }

        return redirect()->route('admin.mobile-app.index')->with('success', "Android app and Firebase RTDB configuration updated successfully!");
    }

    /**
     * Manually trigger a sync of current mobile app configuration to Firebase RTDB
     */
    public function syncRtdb()
    {
        try {
            $saved = app(FirebaseService::class)->syncMobileAppConfig();
            if ($saved) {
                return redirect()->route('admin.mobile-app.index')->with('success', 'Mobile app configuration successfully synced to Firebase RTDB!');
            }
            return redirect()->route('admin.mobile-app.index')->with('error', 'Failed to sync to Firebase RTDB. Check Firebase credentials in .env.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.mobile-app.index')->with('error', 'Firebase RTDB Sync error: ' . $e->getMessage());
        }
    }

    /**
     * Download the app file (admin route)
     */
    public function download($platform)
    {
        if ($platform !== 'android') {
            return back()->with('error', 'Only Android apps are supported');
        }

        $app = MobileApp::where('platform', 'android')->first();

        if (!$app || !$app->file_path) {
            return back()->with('error', 'App not found or file not available');
        }

        // Increment download count
        $app->incrementDownloads();

        return response()->download(
            storage_path('app/public/' . $app->file_path),
            "viewlix-android-{$app->version}.apk"
        );
    }

    /**
     * Download the app file (public route)
     */
    public function downloadAndroid()
    {
        $app = MobileApp::where('platform', 'android')->first();

        if (!$app || !$app->file_path) {
            return back()->with('error', 'App not found or file not available');
        }

        // Increment download count
        $app->incrementDownloads();

        return response()->download(
            storage_path('app/public/' . $app->file_path),
            "viewlix-android-{$app->version}.apk"
        );
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Models\MobileApp;
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

        return view('admin.mobile-app', compact(
            'androidApp',
            'androidDownloads'
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

        return redirect()->route('admin.mobile-app.index')->with('success', "Android app updated successfully!");
    }

    /**
     * Download the app file
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
}

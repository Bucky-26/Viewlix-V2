@extends('layouts.admin')

@section('title', 'Mobile App Management')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white">Mobile App Management</h1>
            <p class="text-gray-400 mt-2">Manage mobile app releases, player streaming URL, and Firebase RTDB configuration</p>
        </div>
        <form action="{{ route('admin.mobile-app.sync-rtdb') }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2.5 bg-viewlix-red hover:bg-red-700 text-white font-medium text-sm rounded-lg transition-colors flex items-center gap-2 shadow-lg shadow-viewlix-red/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                Sync to Firebase RTDB
            </button>
        </form>
    </div>

    {{-- Alert Messages --}}
    @if ($errors->any())
        <div class="bg-red-500/10 border border-red-500/50 rounded-lg p-4">
            <p class="text-red-400 font-semibold">Please fix the following errors:</p>
            <ul class="text-red-300 text-sm mt-2 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success'))
        <div class="bg-green-500/10 border border-green-500/50 rounded-lg p-4">
            <p class="text-green-400">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Stats Card --}}
    <div class="bg-viewlix-gray border border-viewlix-border rounded-xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-sm">Total Downloads</p>
                <p class="text-4xl font-bold text-white mt-2">{{ $androidDownloads ?? 0 }}</p>
            </div>
            <svg class="w-16 h-16 text-green-500 opacity-20" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
        </div>
    </div>

    {{-- Upload Form --}}
    <div class="bg-viewlix-gray border border-viewlix-border rounded-xl p-6 max-w-2xl">
        <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
            <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
            Upload Android App
        </h3>

        <form action="{{ route('admin.mobile-app.store', 'android') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">App Download File (APK)</label>
                <div class="relative border-2 border-dashed border-viewlix-border rounded-lg p-6 hover:border-viewlix-red transition-colors cursor-pointer"
                     onclick="document.getElementById('android-file').click()">
                    <input type="file" id="android-file" name="app_file" accept=".apk" class="hidden"
                           onchange="updateFileName(this, 'android-file-name')">
                    <div class="text-center">
                        <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <p class="text-gray-300 text-sm font-medium">Click to upload or drag & drop</p>
                        <p class="text-gray-500 text-xs">APK files only</p>
                        <p id="android-file-name" class="text-viewlix-red text-xs mt-2"></p>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Version</label>
                <input type="text" name="version" 
                       value="{{ old('version', $androidApp->version ?? '1.0.0') }}"
                       placeholder="e.g., 1.0.0"
                       class="w-full px-4 py-2 bg-viewlix-darker border border-viewlix-border rounded-lg text-white placeholder-gray-500 focus:border-viewlix-red focus:ring-1 focus:ring-viewlix-red"
                       required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Player URL (Mobile Streaming Base)</label>
                <input type="text" name="player_url" 
                       value="{{ old('player_url', $playerUrl ?? 'https://content1.viewlix.online/') }}"
                       placeholder="https://content1.viewlix.online/"
                       class="w-full px-4 py-2 bg-viewlix-darker border border-viewlix-border rounded-lg text-white placeholder-gray-500 focus:border-viewlix-red focus:ring-1 focus:ring-viewlix-red">
                <p class="text-xs text-gray-500 mt-1">This player URL is synced to Firebase RTDB (app_config) for mobile app playback.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <label class="flex items-center gap-3 p-3 bg-viewlix-darker border border-viewlix-border rounded-lg cursor-pointer hover:border-viewlix-red/50 transition">
                    <input type="checkbox" name="force_update" value="1" {{ old('force_update', $forceUpdate ?? false) ? 'checked' : '' }} class="w-4 h-4 rounded text-viewlix-red focus:ring-viewlix-red">
                    <div>
                        <span class="text-sm font-medium text-white block">Force Update</span>
                        <span class="text-xs text-gray-400">Require users to update app</span>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3 bg-viewlix-darker border border-viewlix-border rounded-lg cursor-pointer hover:border-viewlix-red/50 transition">
                    <input type="checkbox" name="maintenance_mode" value="1" {{ old('maintenance_mode', $maintenanceMode ?? false) ? 'checked' : '' }} class="w-4 h-4 rounded text-viewlix-red focus:ring-viewlix-red">
                    <div>
                        <span class="text-sm font-medium text-white block">Maintenance Mode</span>
                        <span class="text-xs text-gray-400">Lock app with maintenance notice</span>
                    </div>
                </label>
            </div>

            <button type="submit" class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                Update & Sync to Firebase RTDB
            </button>
        </form>
    </div>

    {{-- Current App Info --}}
    @if ($androidApp || !empty($playerUrl))
    <div class="bg-viewlix-gray border border-viewlix-border rounded-xl p-6 max-w-2xl">
        <h4 class="text-lg font-bold text-white mb-4">Current Mobile Configuration (RTDB)</h4>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-400">Version:</span>
                <span class="text-white font-semibold">{{ $androidApp->version ?? '1.0.0' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Player URL:</span>
                <span class="text-green-400 font-mono text-xs">{{ $playerUrl ?? 'https://content1.viewlix.online/' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Force Update:</span>
                <span class="{{ ($forceUpdate ?? false) ? 'text-red-400' : 'text-gray-400' }} font-semibold">{{ ($forceUpdate ?? false) ? 'Enabled' : 'Disabled' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Maintenance Mode:</span>
                <span class="{{ ($maintenanceMode ?? false) ? 'text-red-400' : 'text-gray-400' }} font-semibold">{{ ($maintenanceMode ?? false) ? 'Active' : 'Inactive' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Uploaded:</span>
                <span class="text-white">{{ $androidApp?->created_at?->format('M d, Y') ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Downloads:</span>
                <span class="text-white font-semibold">{{ $androidApp->download_count ?? 0 }}</span>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
function updateFileName(input, displayId) {
    const fileName = input.files[0]?.name;
    const displayElement = document.getElementById(displayId);
    if (fileName) {
        displayElement.textContent = '✓ ' + fileName;
    }
}
</script>
@endsection

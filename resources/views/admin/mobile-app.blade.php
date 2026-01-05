@extends('layouts.admin')

@section('title', 'Android App Management')

@section('content')
<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-white">Android App Management</h1>
            <p class="text-gray-400 mt-2">Manage your Android app downloads and configuration</p>
        </div>
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
                       value="{{ old('version') }}"
                       placeholder="e.g., 1.0.0"
                       class="w-full px-4 py-2 bg-viewlix-darker border border-viewlix-border rounded-lg text-white placeholder-gray-500 focus:border-viewlix-red focus:ring-1 focus:ring-viewlix-red"
                       required>
            </div>

            <button type="submit" class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                Update Android App
            </button>
        </form>
    </div>

    {{-- Current App Info --}}
    @if ($androidApp)
    <div class="bg-viewlix-gray border border-viewlix-border rounded-xl p-6 max-w-2xl">
        <h4 class="text-lg font-bold text-white mb-4">Current Android App</h4>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-400">Version:</span>
                <span class="text-white font-semibold">{{ $androidApp->version ?? 'N/A' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-400">Uploaded:</span>
                <span class="text-white">{{ $androidApp->created_at?->format('M d, Y') ?? 'N/A' }}</span>
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

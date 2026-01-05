@extends('layouts.app')

@section('title', 'Download Viewlix App')
@section('description', 'Download the Viewlix Android app. Stream movies, TV shows, and anime on the go!')

@section('content')
<div class="min-h-screen">
    {{-- Hero Section --}}
    <div class="bg-gradient-to-b from-viewlix-red/20 to-transparent py-12 md:py-20">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">Download Viewlix App</h1>
            <p class="text-lg text-gray-300 mb-8">Stream your favorite movies, TV shows, and anime anywhere, anytime</p>
            
            {{-- Feature Badges --}}
            <div class="flex flex-wrap justify-center gap-4 mb-12">
                <div class="flex items-center gap-2 bg-viewlix-gray/50 px-4 py-2 rounded-full">
                    <svg class="w-5 h-5 text-viewlix-red" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-white text-sm">Offline Viewing</span>
                </div>
                <div class="flex items-center gap-2 bg-viewlix-gray/50 px-4 py-2 rounded-full">
                    <svg class="w-5 h-5 text-viewlix-red" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-white text-sm">Push Notifications</span>
                </div>
                <div class="flex items-center gap-2 bg-viewlix-gray/50 px-4 py-2 rounded-full">
                    <svg class="w-5 h-5 text-viewlix-red" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-white text-sm">Better Performance</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Download Card --}}
    <div class="max-w-2xl mx-auto px-4 py-12 md:py-20">
        {{-- Android Card --}}
        <div class="bg-gradient-to-br from-green-600/20 to-green-900/20 border border-green-500/30 rounded-2xl p-8 md:p-12 hover:border-green-500/60 transition-all">
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-white">Viewlix App</h2>
                    <p class="text-green-300 text-sm">{{ $androidApp->version ?? 'Latest' }}</p>
                </div>
            </div>

            <p class="text-gray-300 mb-6">Enjoy Viewlix on all your Android devices with a beautiful and intuitive interface.</p>

            <div class="space-y-3 mb-8">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-gray-300">Official Google Play Store</span>
                </div>
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-gray-300">Compatible with Android 8.0+</span>
                </div>
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-green-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-gray-300">Regular updates and bug fixes</span>
                </div>
            </div>

            @if ($androidApp && $androidApp->file_path)
                <a href="{{ route('app.download') }}" class="inline-block w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg text-center transition-colors">
                    Download APK
                </a>
            @else
                <button disabled class="w-full bg-gray-500 text-gray-300 font-bold py-3 px-6 rounded-lg text-center cursor-not-allowed">
                    Coming Soon
                </button>
            @endif
        </div>
    </div>

    {{-- FAQ Section --}}
    <div class="bg-viewlix-gray/50 py-12 md:py-20">
        <div class="max-w-3xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-white text-center mb-12">Frequently Asked Questions</h2>

            <div class="space-y-4">
                <details class="bg-viewlix-darker border border-viewlix-border rounded-lg p-6 cursor-pointer group">
                    <summary class="flex items-center justify-between font-semibold text-white">
                        <span>What's the difference between the app and website?</span>
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </summary>
                    <p class="text-gray-300 mt-4">The app offers offline viewing, better performance, push notifications for new releases, and a native mobile experience optimized for your device.</p>
                </details>

                <details class="bg-viewlix-darker border border-viewlix-border rounded-lg p-6 cursor-pointer group">
                    <summary class="flex items-center justify-between font-semibold text-white">
                        <span>Is the app free?</span>
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </summary>
                    <p class="text-gray-300 mt-4">Yes! The Viewlix app is completely free to download and use. You'll need an active Viewlix account to stream content.</p>
                </details>

                <details class="bg-viewlix-darker border border-viewlix-border rounded-lg p-6 cursor-pointer group">
                    <summary class="flex items-center justify-between font-semibold text-white">
                        <span>Can I download videos to watch offline?</span>
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </summary>
                    <p class="text-gray-300 mt-4">Yes! With the mobile app, you can download your favorite movies and TV shows to watch offline when you don't have an internet connection.</p>
                </details>

                <details class="bg-viewlix-darker border border-viewlix-border rounded-lg p-6 cursor-pointer group">
                    <summary class="flex items-center justify-between font-semibold text-white">
                        <span>What Android version is required?</span>
                        <svg class="w-5 h-5 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </summary>
                    <p class="text-gray-300 mt-4">The app works on all devices running Android 8.0 or later. Make sure your device is up to date for the best experience.</p>
                </details>
            </div>
        </div>
    </div>
</div>
@endsection

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @if(isset($SEOData))
            {!! seo($SEOData) !!}
        @else
            <title>{{ config('app.name', 'Viewlix') }} - Stream Movies, TV Shows & Anime</title>
            <meta name="description" content="Watch Movies, TV Shows & Anime in HD with Viewlix. Create your free account or sign in to your dashboard.">
        @endif

        <!-- PWA -->
        @PwaHead
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        <!-- Scripts / Styles -->
        <style>[x-cloak] { display: none !important; }</style>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        @stack('styles')
    </head>
    <body class="font-sans antialiased bg-[#08080a] text-gray-100 min-h-screen flex flex-col relative selection:bg-viewlix-red selection:text-white overflow-x-hidden">
        
        <!-- Ambient Glowing Lighting Effects -->
        <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden" aria-hidden="true">
            <!-- Top brand blue radial spotlight -->
            <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[48rem] h-[32rem] bg-gradient-to-b from-[#004aad]/25 via-[#004aad]/10 to-transparent blur-3xl rounded-full"></div>
            <!-- Secondary accent purple/cyan orbs -->
            <div class="absolute top-1/3 -left-32 w-96 h-96 bg-blue-600/10 blur-[120px] rounded-full"></div>
            <div class="absolute bottom-10 -right-32 w-96 h-96 bg-indigo-600/10 blur-[120px] rounded-full"></div>
            <!-- Subtle technical grid overlay -->
            <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.02)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.02)_1px,transparent_1px)] bg-[size:3.5rem_3.5rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_40%,#000_70%,transparent_100%)]"></div>
        </div>

        <!-- Top Navigation Bar -->
        <header class="relative z-10 w-full border-b border-white/5 bg-[#08080a]/60 backdrop-blur-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <!-- Logo -->
                <a href="/" class="group flex items-center gap-3 transition-transform duration-200 hover:scale-[1.02]">
                    <img src="{{ asset('logo.png') }}" alt="Viewlix Icon" class="w-9 h-9 object-contain drop-shadow-[0_0_12px_rgba(0,74,173,0.5)] group-hover:brightness-110 transition-all">
                    <img src="{{ asset('viewlix.png') }}" alt="Viewlix" class="h-7 w-auto object-contain group-hover:brightness-110 transition-all">
                </a>

                <!-- Back to Site Link -->
                <a href="/" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-medium text-gray-300 hover:text-white bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 transition-all duration-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Browse Movies</span>
                </a>
            </div>
        </header>

        <!-- Main Content Slot -->
        <main class="relative z-10 flex-1 flex flex-col justify-center items-center py-10 px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="relative z-10 w-full border-t border-white/5 bg-[#08080a]/80 py-6 text-center text-xs text-gray-500">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('viewlix.png') }}" alt="Viewlix" class="h-5 w-auto object-contain opacity-70">
                    <p>&copy; {{ date('Y') }} Viewlix. Stream movies, TV shows &amp; anime in HD.</p>
                </div>
                <div class="flex items-center gap-6">
                    <a href="/" class="hover:text-gray-300 transition-colors">Home</a>
                    <a href="/robots.txt" class="hover:text-gray-300 transition-colors">Terms</a>
                    <a href="/sitemap.xml" class="hover:text-gray-300 transition-colors">Sitemap</a>
                </div>
            </div>
        </footer>

        @stack('scripts')
    </body>
</html>

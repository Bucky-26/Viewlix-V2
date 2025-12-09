<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', config('app.name', 'Viewlix'))</title>
    
    <!-- PWA (only in production) -->
    @production
    @PwaHead
    @endproduction
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    
    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    
    @stack('styles')
</head>
<body class="bg-viewlix-dark min-h-screen">
    <!-- Mini Sidebar (hidden on mobile) -->
    @include('partials.sidebar')
    
    <!-- Main Content -->
    <main class="md:pl-[72px] min-h-screen pb-20 md:pb-0">
        <!-- Header -->
        @include('partials.header')
        
        <!-- Page Content -->
        <div class="p-4 lg:p-6">
            @hasSection('content')
                @yield('content')
            @else
                {{ $slot ?? '' }}
            @endif
        </div>
        
        <!-- Footer -->
        @include('partials.footer')
    </main>
    
    <!-- Mobile Bottom Navigation -->
    @include('partials.mobile-nav')
    
    <!-- Preview Offcanvas -->
    @include('partials.preview-offcanvas')
    
    <!-- Overlay Backdrop -->
    <div id="overlay-backdrop" class="hidden fixed inset-0 bg-black/60 z-[70] transition-opacity" onclick="closeAllOverlays()"></div>
    
    <!-- Global Overlay Scripts -->
    <script>
        // Helper to open overlay
        function openOverlay(selector) {
            const el = document.querySelector(selector);
            const backdrop = document.getElementById('overlay-backdrop');
            if (el) {
                backdrop.classList.remove('hidden');
                el.classList.remove('hidden');
                // Trigger animation
                requestAnimationFrame(() => {
                    el.classList.remove('translate-y-full');
                    el.classList.add('translate-y-0');
                });
            }
        }

        // Helper to close overlay
        function closeOverlay(selector) {
            const el = document.querySelector(selector);
            const backdrop = document.getElementById('overlay-backdrop');
            if (el) {
                el.classList.remove('translate-y-0');
                el.classList.add('translate-y-full');
                setTimeout(() => {
                    el.classList.add('hidden');
                    backdrop.classList.add('hidden');
                }, 300);
            }
        }

        // Close all overlays
        function closeAllOverlays() {
            const overlays = document.querySelectorAll('#preview-offcanvas, #share-offcanvas, #mobile-auth-offcanvas, #mobile-account-offcanvas');
            const backdrop = document.getElementById('overlay-backdrop');
            overlays.forEach(el => {
                el.classList.remove('translate-y-0');
                el.classList.add('translate-y-full');
                setTimeout(() => {
                    el.classList.add('hidden');
                }, 300);
            });
            backdrop.classList.add('hidden');
        }
    </script>
    
    <!-- PWA Service Worker (only in production) -->
    @production
    @RegisterServiceWorkerScript
    @endproduction
    
    @stack('scripts')
</body>
</html>

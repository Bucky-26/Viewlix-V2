<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-5WWKDRS7Q0"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-5WWKDRS7Q0');
</script>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-TK84GXHV');</script>
<!-- End Google Tag Manager -->
    <meta charset="utf-8">
    
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="referrer" content="origin" />


    @if(isset($SEOData))
        {!! seo($SEOData) !!}
    @else
        @php
            // Build a default SEOData so canonical/robots/OpenGraph are rendered consistently
            $defaultSEO = new \RalphJSmit\Laravel\SEO\Support\SEOData();
            $defaultSEO->title = trim(View::yieldContent('title', config('app.name', 'Viewlix')));
            $defaultSEO->description = trim(View::yieldContent('description', 'Discover, search, and watch the best movies, TV shows, and anime on Viewlix. Enjoy a modern streaming experience!'));
            $defaultSEO->url = url()->current();
        @endphp

        {!! seo($defaultSEO) !!}
    @endif
    
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
    <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TK84GXHV"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
    {{-- Mobile App Banner - Very Top (Static, No Overlay) --}}
    
    <!-- Mobile App Popup -->
    
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
    
        <!-- Overlay Backdrop (placed before offcanvas elements so offcanvas sits above it) -->
        <div id="overlay-backdrop" class="hidden pointer-events-none fixed inset-0 bg-black/30 z-[70] transition-opacity" onclick="closeAllOverlays()"></div>
    </main>
    
    <!-- Mobile Bottom Navigation -->
    @include('partials.mobile-nav')
    
    <!-- Preview Offcanvas -->
    @include('partials.preview-offcanvas')
    
    <!-- Mobile Notifications Offcanvas -->
    @include('partials.notifications-offcanvas')
    
    <!-- Desktop Notifications Offcanvas -->
    @include('partials.notifications-desktop-offcanvas')
    
    <!-- Global Overlay Scripts -->
    <script>
    
    </script>
    
    <!-- PWA Service Worker (only in production) -->
    @production
    @RegisterServiceWorkerScript
    @endproduction
    
    <!-- Firebase Initialization (for authenticated users) -->
    @auth
    <script>
        // Set VAPID key for FCM
        window.firebaseVapidKey = "{{ config('services.firebase.vapid_key') }}";
        
        // Wait for app.js to load, then initialize Firebase
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.initFirebase === 'function') {
                window.initFirebase({
                    apiKey: "{{ config('services.firebase.api_key') }}",
                    authDomain: "{{ config('services.firebase.auth_domain') }}",
                    databaseURL: "{{ config('services.firebase.database_url') }}",
                    projectId: "{{ config('services.firebase.project_id') }}",
                    storageBucket: "{{ config('services.firebase.storage_bucket') }}",
                    messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
                    appId: "{{ config('services.firebase.app_id') }}",
                    measurementId: "{{ config('services.firebase.measurement_id') }}"
                });
                
                window.signInToFirebase();
            }
        });
    </script>
    @endauth
    <!-- Google tag (gtag.js) -->

    <!-- Pull-to-refresh (PWA) - swipe down to reload on touch devices -->
    <script src="{{ asset('js/pull-to-refresh.js') }}"></script>
    
    <!-- Download Manager -->
    <script src="{{ asset('js/download-manager.js') }}"></script>
      
    <script src="{{ asset('js/main.js') }}"></script>

    @stack('scripts')
</body>
</html>

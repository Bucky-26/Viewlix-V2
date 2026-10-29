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
    <style>[x-cloak] { display: none !important; }</style>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <!-- Firebase Initialization Config -->
    <script>
        window.isUserLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
        window.userFirebaseUuid = "{{ Auth::check() ? Auth::user()->getFirebaseUuid() : '' }}";
        window.firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key') }}",
            authDomain: "{{ config('services.firebase.auth_domain') }}",
            databaseURL: "{{ config('services.firebase.database_url') }}",
            projectId: "{{ config('services.firebase.project_id') }}",
            storageBucket: "{{ config('services.firebase.storage_bucket') }}",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
            appId: "{{ config('services.firebase.app_id') }}",
            measurementId: "{{ config('services.firebase.measurement_id') }}"
        };
        window.firebaseVapidKey = "{{ config('services.firebase.vapid_key') }}";

        function setupFirebaseAuthOnReady() {
            if (window.isUserLoggedIn && typeof window.signInToFirebase === 'function' && typeof window.isFirebaseAuthenticated === 'function' && !window.isFirebaseAuthenticated()) {
                window.signInToFirebase().catch(function(e) { console.debug('Firebase auto-sign-in error', e); });
            }
        }

        window.addEventListener('firebase:ready', setupFirebaseAuthOnReady);
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.initFirebase === 'function' && window.firebaseConfig && window.firebaseConfig.apiKey) {
                window.initFirebase(window.firebaseConfig);
            }
            setupFirebaseAuthOnReady();
        });
    </script>
    
    @stack('styles')
</head>

<body class="bg-[#08080a] text-gray-100 min-h-screen relative antialiased selection:bg-viewlix-red selection:text-white overflow-x-hidden">
    <!-- Ambient Cinematic Lighting -->
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden" aria-hidden="true">
        <div class="absolute -top-40 left-1/3 w-[50rem] h-[30rem] bg-gradient-to-b from-[#004aad]/15 via-[#004aad]/5 to-transparent blur-3xl rounded-full"></div>
        <div class="absolute top-1/2 -right-40 w-96 h-96 bg-indigo-600/10 blur-[130px] rounded-full"></div>
        <div class="absolute bottom-10 left-10 w-96 h-96 bg-blue-600/10 blur-[130px] rounded-full"></div>
    </div>

    <!-- Header Banners (Very Top) -->
    @php
        try {
            $headerBanners = \App\Models\Banner::where('type', 'header')->where('is_active', true)->get();
            $popupBanners = \App\Models\Banner::where('type', 'popup')->where('is_active', true)->get();
        } catch (\Exception $e) {
            $headerBanners = collect();
            $popupBanners = collect();
            // Log error in production
            if (app()->environment('production')) {
                \Log::error('Banner query error: ' . $e->getMessage());
            }
        }
    @endphp
    
    @if($headerBanners && count($headerBanners) > 0)
        @foreach($headerBanners as $banner)
            @include('components.banner-header', ['banner' => $banner])
        @endforeach
    @endif

    <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TK84GXHV"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
    {{-- Mobile App Banner - Very Top (Static, No Overlay) --}}
    
    <!-- Banner Popups -->
    @if($popupBanners && count($popupBanners) > 0)
        @foreach($popupBanners as $banner)
            @include('components.banner-popup', ['banner' => $banner])
        @endforeach
    @endif
    
    <!-- Mobile App Popup -->
    
    <!-- Main Content -->
    <main class="relative z-10 min-h-screen flex flex-col justify-between pb-24 md:pb-0 @if($headerBanners->count() > 0) mt-20 @endif">
        <!-- Header -->
        @include('partials.header')
        
        <!-- Page Content -->
        <div class="p-4 lg:p-6 flex-1">
            @hasSection('content')
                @yield('content')
            @else
                {{ $slot ?? '' }}
            @endif
        </div>
        
        <!-- Footer -->
        @include('partials.footer')
    
        <!-- Overlay Backdrop (placed before offcanvas elements so offcanvas sits above it) -->
        <div id="overlay-backdrop" class="hidden pointer-events-none fixed inset-0 bg-black/50 backdrop-blur-sm z-[70] transition-opacity" onclick="closeAllOverlays()"></div>
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
    
    <!-- Firebase Initialization Fallback -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.setupFirebaseAuthOnReady === 'function') {
                window.setupFirebaseAuthOnReady();
            }
        });
    </script>
    <!-- Google tag (gtag.js) -->

    <!-- Pull-to-refresh (PWA) - swipe down to reload on touch devices -->
    <script src="{{ asset('js/pull-to-refresh.js') }}"></script>
    
    <!-- Download Manager -->
      
    <script src="{{ asset('js/main.js') }}"></script>

    <!-- Banner Management Script -->
    <script>
        function showBannerPopup(bannerId) {
            const popup = document.getElementById(`banner-popup-${bannerId}`);
            const inner = document.getElementById(`banner-popup-inner-${bannerId}`);
            if (popup) {
                popup.classList.remove('hidden');
                // Small delay to allow display block to apply before transition
                setTimeout(() => {
                    if (inner) {
                        inner.classList.remove('opacity-0', 'scale-95');
                        inner.classList.add('opacity-100', 'scale-100');
                    }
                }, 10);
            }
        }

        function closeBannerPopup(bannerId) {
            const popup = document.getElementById(`banner-popup-${bannerId}`);
            const inner = document.getElementById(`banner-popup-inner-${bannerId}`);
            if (inner) {
                inner.classList.remove('opacity-100', 'scale-100');
                inner.classList.add('opacity-0', 'scale-95');
            }
            if (popup) {
                setTimeout(() => {
                    popup.classList.add('hidden');
                }, 300); // Wait for transition
                // Save to localStorage that this banner has been shown
                localStorage.setItem(`banner-shown-${bannerId}`, 'true');
            }
        }

        function closeHeaderBanner(bannerId) {
            const banner = document.getElementById(`header-banner-${bannerId}`);
            const mainContent = document.querySelector('main');
            if (banner) {
                banner.style.display = 'none';
                // Remove top margin from main content when banner is closed
                if (mainContent) {
                    mainContent.classList.remove('mt-20');
                }
                // Save to sessionStorage so it stays closed for this browsing session
                sessionStorage.setItem(`header-banner-closed-${bannerId}`, 'true');
            }
        }

        // Initialize banners on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Popup banners
            const bannerIds = @json($popupBanners->pluck('id'));
            
            bannerIds.forEach(bannerId => {
                const storageKey = `banner-shown-${bannerId}`;
                const hasBeenShown = localStorage.getItem(storageKey);
                
                // Show banner if it hasn't been shown in this session or if page was closed and reopened
                // Banner shows on first visit or after page refresh (session based)
                if (!hasBeenShown) {
                    // Mark as shown in current session
                    localStorage.setItem(storageKey, 'true');
                    // Show the popup after a brief delay for better UX
                    setTimeout(() => {
                        showBannerPopup(bannerId);
                    }, 500);
                }
            });

            // Header banners - restore visibility based on sessionStorage (only closed during current session)
            const headerBannerIds = @json($headerBanners->pluck('id'));
            const mainContent = document.querySelector('main');
            let anyBannerClosed = false;
            
            headerBannerIds.forEach(bannerId => {
                const closedKey = `header-banner-closed-${bannerId}`;
                if (sessionStorage.getItem(closedKey)) {
                    const banner = document.getElementById(`header-banner-${bannerId}`);
                    if (banner) {
                        banner.style.display = 'none';
                        anyBannerClosed = true;
                    }
                }
            });
            
            // Remove margin if any banner is closed
            if (anyBannerClosed && mainContent) {
                mainContent.classList.remove('mt-20');
            }
        });

        // Show banners again when user returns to the page (after closing and reopening browser/tab)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                // Page was restored from bfcache (back-forward cache)
                const bannerIds = @json($popupBanners->pluck('id'));
                bannerIds.forEach(bannerId => {
                    localStorage.removeItem(`banner-shown-${bannerId}`);
                    setTimeout(() => {
                        showBannerPopup(bannerId);
                    }, 500);
                });

                // Header banners stay closed even on page restore (user dismissed them)
                // No need to reset sessionStorage as banners should stay hidden
            }
        });
    </script>

    {{-- Floating Live Chat & Support Ticket System --}}
    @include('partials.chat-support')

    @stack('scripts')
</body>
</html>

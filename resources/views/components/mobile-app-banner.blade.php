{{-- Mobile App Banner - Static at Top (Not Fixed) --}}
<div id="mobile-app-banner" class="hidden bg-gradient-to-r from-viewlix-red to-red-600 text-white px-4 py-3 shadow-lg md:hidden w-full">
    {{-- Header Text --}}
    <p class="text-center text-xs font-bold uppercase tracking-wider mb-2 opacity-90">📱 Mobile App Available</p>
    
    <div class="flex items-center justify-between max-w-full gap-2">
        <div class="flex items-center gap-3 flex-1 min-w-0">
            {{-- App Icon --}}
            <svg class="w-6 h-6 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
            </svg>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm">Get the Viewlix App</p>
                <p class="text-xs opacity-90">Download for a better experience</p>
            </div>
        </div>
        
        {{-- Action Button --}}
        <a href="{{ route('download') }}" class="px-3 py-2 bg-white text-viewlix-red font-semibold rounded-lg hover:bg-gray-100 transition-colors whitespace-nowrap text-xs sm:text-sm flex-shrink-0">
            Install
        </a>
        
        {{-- Close Button --}}
        <button onclick="closeMobileAppBanner()" class="text-white hover:bg-white/20 rounded-lg p-2 transition-colors flex-shrink-0" aria-label="Close banner">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
</div>

<script>
(function() {
    // Check if banner has been closed this session
    const bannerDismissed = sessionStorage.getItem('mobile-app-banner-dismissed');
    
    // Detect if the site is being accessed from a mobile device
    const isMobile = () => {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    };
    
    // Detect if the site is being accessed as a PWA (from home screen)
    const isPWA = () => {
        return window.matchMedia('(display-mode: standalone)').matches ||
               window.navigator.standalone === true ||
               document.referrer.includes('android-app://');
    };
    
    // Detect if the site is being accessed from native app (embedded WebView)
    const isNativeApp = () => {
        const userAgent = navigator.userAgent.toLowerCase();
        // Check for common native app indicators
        return userAgent.includes('viewlix') || // Your app identifier
               userAgent.includes('android') && userAgent.includes('webview') ||
               window.isNativeApp === true; // Set by native app
    };
    
    // Show the banner if conditions are met
    function showMobileAppBanner() {
        // Only show if:
        // 1. Not previously dismissed in this session
        // 2. It's a mobile device
        // 3. It's NOT already in PWA mode
        // 4. It's NOT the native app
        if (!bannerDismissed && isMobile() && !isPWA() && !isNativeApp()) {
            const banner = document.getElementById('mobile-app-banner');
            if (banner) {
                banner.classList.remove('hidden');
            }
        }
    }
    
    // Close banner function
    window.closeMobileAppBanner = function() {
        const banner = document.getElementById('mobile-app-banner');
        if (banner) {
            banner.classList.add('hidden');
            // Remember dismissal for this session
            sessionStorage.setItem('mobile-app-banner-dismissed', 'true');
        }
    };
    
    // Show banner when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', showMobileAppBanner);
    } else {
        showMobileAppBanner();
    }
})();
</script>

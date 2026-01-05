{{-- Mobile App Popup Modal --}}
<div id="mobile-app-popup" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 md:hidden" role="dialog">
    <div class="bg-viewlix-darker border border-viewlix-border rounded-2xl shadow-2xl max-w-sm w-full overflow-hidden animate-in fade-in zoom-in-95 duration-300 relative z-50">
        {{-- Header Badge --}}
        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 text-center py-2 px-4">
            <p class="text-xs font-bold uppercase tracking-wider text-white">📱 Mobile App Available</p>
        </div>
        
        {{-- Header with Gradient --}}
        <div class="bg-gradient-to-r from-viewlix-red to-red-600 p-6 text-center">
            {{-- App Icon --}}
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white rounded-full mb-4">
                <svg class="w-8 h-8 text-viewlix-red" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/>
                </svg>
            </div>
            
            <h2 class="text-2xl font-bold text-white mb-2">Get Viewlix App</h2>
            <p class="text-red-100 text-sm">Download now for the best experience</p>
        </div>
        
        {{-- Content --}}
        <div class="p-6 space-y-4">
            {{-- Features List --}}
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-viewlix-red flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-gray-300">Stream offline - watch anywhere</p>
                </div>
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-viewlix-red flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-gray-300">Faster performance & smooth playback</p>
                </div>
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-viewlix-red flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-gray-300">Push notifications for new releases</p>
                </div>
            </div>
        </div>
        
        {{-- Buttons --}}
        <div class="px-6 pb-6 space-y-3">
            {{-- Primary Button - App Store/Play Store --}}
            <a href="{{ route('download') }}" id="mobile-app-download-btn" 
               class="flex items-center justify-center w-full px-4 py-3 bg-gradient-to-r from-viewlix-red to-red-600 text-white font-semibold rounded-lg hover:from-red-600 hover:to-red-700 transition-all shadow-lg gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 1a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V1zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V5zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V9zm0 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z"/>
                </svg>
                <span>Download Now</span>
            </a>
            
            {{-- Secondary Button - Dismiss --}}
            <button onclick="closeMobileAppPopup()" 
                    class="w-full px-4 py-3 bg-viewlix-gray text-gray-300 font-semibold rounded-lg hover:bg-viewlix-border hover:text-white transition-colors">
                Maybe Later
            </button>
        </div>
    </div>
</div>

<script>
(function() {
    // Configuration
    const POPUP_DELAY = 3000; // Show popup after 3 seconds
    const POPUP_STORAGE_KEY = 'mobile-app-popup-dismissed';
    const POPUP_DISMISS_TIME = 86400000; // 24 hours in milliseconds
    
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
    
    // Check if popup was recently dismissed
    const isPopupDismissed = () => {
        const dismissedTime = localStorage.getItem(POPUP_STORAGE_KEY);
        if (!dismissedTime) return false;
        
        const timeSinceDismissal = Date.now() - parseInt(dismissedTime);
        return timeSinceDismissal < POPUP_DISMISS_TIME;
    };
    
    // Show the popup
    function showMobileAppPopup() {
        // Only show if:
        // 1. It's a mobile device
        // 2. It's NOT already in PWA mode
        // 3. Not dismissed in the last 24 hours
        // 4. It's NOT the native app
        if (isMobile() && !isPWA() && !isPopupDismissed() && !isNativeApp()) {
            const popup = document.getElementById('mobile-app-popup');
            if (popup) {
                // Show after delay to not interrupt initial page load
                setTimeout(() => {
                    popup.classList.remove('hidden');
                }, POPUP_DELAY);
            }
        }
    }
    
    // Close popup function
    window.closeMobileAppPopup = function() {
        const popup = document.getElementById('mobile-app-popup');
        if (popup) {
            popup.classList.add('hidden');
            // Remember dismissal for 24 hours
            localStorage.setItem(POPUP_STORAGE_KEY, Date.now().toString());
        }
    };
    
    // Close on backdrop click
    document.addEventListener('click', function(e) {
        const popup = document.getElementById('mobile-app-popup');
        if (popup && e.target === popup) {
            closeMobileAppPopup();
        }
    });
    
    // Show popup when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', showMobileAppPopup);
    } else {
        showMobileAppPopup();
    }
})();
</script>

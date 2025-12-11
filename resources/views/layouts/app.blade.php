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
    
        <!-- Overlay Backdrop (placed before offcanvas elements so offcanvas sits above it) -->
        <div id="overlay-backdrop" class="hidden pointer-events-none fixed inset-0 bg-black/60 z-[70] transition-opacity" onclick="closeAllOverlays()"></div>
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
        // Helper to open overlay
        function openOverlay(selector) {
            const el = document.querySelector(selector);
            const backdrop = document.getElementById('overlay-backdrop');
            if (el) {
                // Make backdrop visible and enable pointer events so clicks on backdrop can close overlays
                backdrop.classList.remove('hidden');
                backdrop.classList.remove('pointer-events-none');
                backdrop.classList.add('pointer-events-auto');
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
                    // Hide backdrop and disable pointer events
                    backdrop.classList.add('hidden');
                    backdrop.classList.remove('pointer-events-auto');
                    backdrop.classList.add('pointer-events-none');
                }, 300);
            }
        }

        // Helper to open desktop right offcanvas
        function openDesktopOverlay(selector) {
            const el = document.querySelector(selector);
            const backdrop = document.getElementById('overlay-backdrop');
            if (el) {
                // Ensure backdrop can receive clicks
                backdrop.classList.remove('hidden');
                backdrop.classList.remove('pointer-events-none');
                backdrop.classList.add('pointer-events-auto');
                el.classList.remove('hidden');
                // Trigger animation
                requestAnimationFrame(() => {
                    el.classList.remove('translate-x-full');
                    el.classList.add('translate-x-0');
                });
            }
        }

        // Helper to close desktop right offcanvas
        function closeDesktopOverlay(selector) {
            const el = document.querySelector(selector);
            const backdrop = document.getElementById('overlay-backdrop');
            if (el) {
                el.classList.remove('translate-x-0');
                el.classList.add('translate-x-full');
                setTimeout(() => {
                    el.classList.add('hidden');
                    // Hide backdrop and disable pointer events
                    backdrop.classList.add('hidden');
                    backdrop.classList.remove('pointer-events-auto');
                    backdrop.classList.add('pointer-events-none');
                }, 300);
            }
        }

        // Close all overlays
        function closeAllOverlays() {
            // Bottom offcanvas (mobile)
            const bottomOverlays = document.querySelectorAll('#preview-offcanvas, #share-offcanvas, #mobile-auth-offcanvas, #mobile-account-offcanvas, #notifications-offcanvas');
            const backdrop = document.getElementById('overlay-backdrop');
            bottomOverlays.forEach(el => {
                el.classList.remove('translate-y-0');
                el.classList.add('translate-y-full');
                setTimeout(() => {
                    el.classList.add('hidden');
                }, 300);
            });
            
            // Right offcanvas (desktop)
            const rightOverlays = document.querySelectorAll('#notifications-desktop-offcanvas');
            rightOverlays.forEach(el => {
                el.classList.remove('translate-x-0');
                el.classList.add('translate-x-full');
                setTimeout(() => {
                    el.classList.add('hidden');
                }, 300);
            });
            
            // Hide backdrop and disable pointer events
            backdrop.classList.add('hidden');
            backdrop.classList.remove('pointer-events-auto');
            backdrop.classList.add('pointer-events-none');
        }

        // Data-driven handlers for elements using Preline-like data attributes (data-hs-overlay)
        document.addEventListener('click', function(e) {
            const trigger = e.target.closest('[data-hs-overlay]');
            if (!trigger) return;
            e.preventDefault();
            const selector = trigger.getAttribute('data-hs-overlay');
            if (!selector) return;
            const target = document.querySelector(selector);
            if (!target) return;

            // If target is hidden -> open, otherwise close
            if (target.classList.contains('hidden')) {
                openOverlay(selector);
            } else {
                closeOverlay(selector);
            }
        });

        // Notification UI Rendering (Vanilla JS)
        function formatNotificationTime(timestamp) {
            if (!timestamp) return '';
            const date = new Date(timestamp);
            const now = new Date();
            const diff = now - date;
            
            if (diff < 60000) return 'Just now';
            if (diff < 3600000) return Math.floor(diff / 60000) + 'm ago';
            if (diff < 86400000) return Math.floor(diff / 3600000) + 'h ago';
            if (diff < 604800000) return Math.floor(diff / 86400000) + 'd ago';
            return date.toLocaleDateString();
        }

        function renderNotificationItem(notif, isMobile = false) {
            const sizeClass = isMobile ? 'w-12 h-12' : 'w-10 h-10';
            const paddingClass = isMobile ? 'px-4 py-4' : 'px-4 py-3';
            const unreadBg = !notif.read ? 'bg-viewlix-red/5' : '';
            
            const imageHtml = notif.image 
                ? `<img src="${notif.image}" class="${sizeClass} rounded-lg object-cover flex-shrink-0" alt="">`
                : `<div class="${sizeClass} rounded-lg bg-viewlix-gray flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                   </div>`;
            
            const unreadDot = !notif.read 
                ? `<span class="w-2 h-2 bg-viewlix-red rounded-full flex-shrink-0 mt-2"></span>` 
                : '';

            return `
                <a href="${notif.link || '#'}" 
                   onclick="if (!${notif.read} && typeof markNotificationRead === 'function') markNotificationRead('${notif.id}')"
                   class="flex items-start gap-3 ${paddingClass} hover:bg-viewlix-gray transition-colors border-b border-viewlix-border/50 last:border-0 ${unreadBg}">
                    ${imageHtml}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">${notif.title || ''}</p>
                        <p class="text-xs text-gray-400 line-clamp-2">${notif.message || ''}</p>
                        <p class="text-[10px] text-gray-500 mt-1">${formatNotificationTime(notif.timestamp)}</p>
                    </div>
                    ${unreadDot}
                </a>
            `;
        }

        function renderEmptyState(isMobile = false) {
            const size = isMobile ? 'w-16 h-16' : 'w-12 h-12';
            const iconSize = isMobile ? 'w-8 h-8' : 'w-12 h-12';
            const padding = isMobile ? 'py-12' : 'py-8';
            
            return `
                <div class="flex flex-col items-center justify-center ${padding} text-center px-4">
                    <div class="${isMobile ? 'w-16 h-16 rounded-full bg-viewlix-gray/50 flex items-center justify-center mb-4' : ''}">
                        <svg class="${iconSize} text-gray-600 ${isMobile ? '' : 'mb-2'}" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                        </svg>
                    </div>
                    <p class="${isMobile ? 'text-base font-medium text-gray-300 mb-1' : 'text-sm text-gray-400'}">No notifications${isMobile ? '' : ' yet'}</p>
                    ${isMobile ? '<p class="text-sm text-gray-500">We\'ll notify you when something arrives</p>' : ''}
                </div>
            `;
        }

        function updateNotificationUI(notifications) {
            const unreadCount = notifications.filter(n => !n.read).length;
            
            // Update desktop badge
            const desktopBadge = document.getElementById('desktop-notif-badge');
            if (desktopBadge) {
                desktopBadge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                desktopBadge.classList.toggle('hidden', unreadCount === 0);
            }
            
            // Update mobile badge
            const mobileBadge = document.getElementById('mobile-notif-badge');
            if (mobileBadge) {
                mobileBadge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                mobileBadge.classList.toggle('hidden', unreadCount === 0);
            }
            
            // Update mobile offcanvas badge
            const offcanvasBadge = document.getElementById('mobile-offcanvas-badge');
            if (offcanvasBadge) {
                offcanvasBadge.textContent = unreadCount;
                offcanvasBadge.classList.toggle('hidden', unreadCount === 0);
            }
            
            // Update desktop offcanvas badge
            const desktopOffcanvasBadge = document.getElementById('desktop-offcanvas-badge');
            if (desktopOffcanvasBadge) {
                desktopOffcanvasBadge.textContent = unreadCount;
                desktopOffcanvasBadge.classList.toggle('hidden', unreadCount === 0);
            }
            
            // Update mark all read buttons
            const desktopMarkAll = document.getElementById('desktop-mark-all-read');
            const mobileMarkAll = document.getElementById('mobile-mark-all-read');
            if (desktopMarkAll) desktopMarkAll.classList.toggle('hidden', unreadCount === 0);
            if (mobileMarkAll) mobileMarkAll.classList.toggle('hidden', unreadCount === 0);
            
            // Update footers
            const desktopFooter = document.getElementById('desktop-notif-footer');
            const mobileFooter = document.getElementById('mobile-notif-footer');
            if (desktopFooter) desktopFooter.classList.toggle('hidden', notifications.length === 0);
            if (mobileFooter) mobileFooter.classList.toggle('hidden', notifications.length === 0);
            
            // Update desktop list
            const desktopList = document.getElementById('desktop-notification-list');
            if (desktopList) {
                if (notifications.length === 0) {
                    desktopList.innerHTML = renderEmptyState(false);
                } else {
                    desktopList.innerHTML = notifications.map(n => renderNotificationItem(n, false)).join('');
                }
            }
            
            // Update mobile list
            const mobileList = document.getElementById('mobile-notification-list');
            if (mobileList) {
                if (notifications.length === 0) {
                    mobileList.innerHTML = renderEmptyState(true);
                } else {
                    mobileList.innerHTML = notifications.map(n => renderNotificationItem(n, true)).join('');
                }
            }
        }

        // Listen for notification updates
        window.addEventListener('notifications-updated', (e) => {
            updateNotificationUI(e.detail.notifications || []);
        });
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
                
                // Sign in to Firebase with Laravel session
                window.signInToFirebase();
            }
        });
    </script>
    @endauth
    
    @stack('scripts')
</body>
</html>

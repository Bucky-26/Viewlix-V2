{{-- Desktop Notifications Offcanvas (Right Side) --}}
@auth
<div id="notifications-desktop-offcanvas" 
     class="hidden translate-x-full fixed top-0 right-0 h-full transition-all duration-300 transform w-96 max-w-full z-[80] bg-viewlix-darker border-l border-viewlix-border shadow-2xl"
     role="dialog" 
     tabindex="-1" 
     aria-labelledby="notifications-desktop-offcanvas-label">
    
    <div class="flex flex-col h-full">
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-viewlix-border flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-viewlix-red/10 flex items-center justify-center">
                    <svg class="w-5 h-5 text-viewlix-red" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div>
                    <h3 id="notifications-desktop-offcanvas-label" class="text-lg font-semibold text-white">Notifications</h3>
                    <p class="text-xs text-gray-400" id="desktop-notif-count-text">No new notifications</p>
                </div>
            </div>
            <button type="button" 
                    onclick="closeDesktopOverlay('#notifications-desktop-offcanvas')"
                    class="p-2 text-gray-400 hover:text-white hover:bg-viewlix-gray rounded-xl transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        {{-- Actions Bar --}}
        <div class="flex items-center justify-between px-5 py-2 border-b border-viewlix-border/50 flex-shrink-0 hidden" id="desktop-actions-bar">
            <button type="button" 
                    onclick="if(typeof markAllNotificationsRead === 'function') markAllNotificationsRead()"
                    id="desktop-mark-all-read"
                    class="text-sm text-viewlix-red hover:text-viewlix-red-dark font-medium">
                Mark all as read
            </button>
            <button type="button" 
                    onclick="if (confirm('Clear all notifications?') && typeof clearAllNotifications === 'function') clearAllNotifications()"
                    class="text-sm text-gray-400 hover:text-red-400 font-medium">
                Clear all
            </button>
        </div>
        
        {{-- Notification List --}}
        <div class="overflow-y-auto flex-1 overscroll-contain" id="desktop-notification-list">
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-16 text-center px-6">
                <div class="w-20 h-20 rounded-full bg-viewlix-gray/30 flex items-center justify-center mb-5">
                    <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                </div>
                <p class="text-lg font-medium text-gray-300 mb-2">No notifications</p>
                <p class="text-sm text-gray-500">We'll notify you when something arrives</p>
            </div>
        </div>
    </div>
</div>

{{-- Desktop Notifications Backdrop --}}
<div id="notifications-desktop-backdrop" 
     class="hidden fixed inset-0 bg-black/50 z-[75] transition-opacity duration-300 opacity-0"
     onclick="closeDesktopNotifications()">
</div>
@endauth

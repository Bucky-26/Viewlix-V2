{{-- Mobile Notifications Offcanvas --}}
@auth
<div id="notifications-offcanvas" 
     class="hidden translate-y-full fixed bottom-0 inset-x-0 transition-all duration-300 transform max-h-[85vh] w-full z-[80] bg-viewlix-darker border-t border-viewlix-border rounded-t-2xl"
     role="dialog" 
     tabindex="-1" 
     aria-labelledby="notifications-offcanvas-label">
    
    <div class="flex flex-col h-full max-h-[85vh]">
        {{-- Drag Handle --}}
        <div class="flex justify-center pt-3 pb-2 flex-shrink-0">
            <span class="w-12 h-1.5 bg-viewlix-gray rounded-full cursor-pointer" onclick="closeOverlay('#notifications-offcanvas')"></span>
        </div>
        
        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-viewlix-border flex-shrink-0">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-viewlix-red" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <h3 id="notifications-offcanvas-label" class="text-lg font-semibold text-white">Notifications</h3>
                <span id="mobile-offcanvas-badge" class="inline-flex items-center justify-center min-w-[20px] h-[20px] px-1.5 text-[11px] font-bold text-white bg-viewlix-red rounded-full hidden">
                    0
                </span>
            </div>
            <button type="button" 
                    onclick="if(typeof markAllNotificationsRead === 'function') markAllNotificationsRead()"
                    id="mobile-mark-all-read"
                    class="text-sm text-viewlix-red hover:text-viewlix-red-dark font-medium hidden">
                Mark all read
            </button>
        </div>
        
        {{-- Notification List --}}
        <div class="overflow-y-auto flex-1 overscroll-contain" id="mobile-notification-list">
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-12 text-center px-4">
                <div class="w-16 h-16 rounded-full bg-viewlix-gray/50 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                </div>
                <p class="text-base font-medium text-gray-300 mb-1">No notifications</p>
                <p class="text-sm text-gray-500">We'll notify you when something arrives</p>
            </div>
        </div>
        
        {{-- Footer --}}
        <div class="px-4 py-3 border-t border-viewlix-border flex items-center justify-between safe-area-bottom hidden" id="mobile-notif-footer">
            <button type="button" 
                    onclick="if (confirm('Clear all notifications?') && typeof clearAllNotifications === 'function') clearAllNotifications()"
                    class="text-sm text-gray-400 hover:text-red-400 font-medium">
                Clear all
            </button>
            <button type="button" 
                    class="text-sm text-gray-400 hover:text-white font-medium"
                    onclick="closeOverlay('#notifications-offcanvas')">
                Close
            </button>
        </div>
    </div>
</div>
@endauth

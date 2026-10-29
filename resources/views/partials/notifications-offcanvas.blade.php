{{-- Mobile Notifications Offcanvas --}}
@auth
<div id="notifications-offcanvas" 
    class="hidden translate-y-full fixed bottom-0 inset-x-0 transition-all duration-300 transform max-h-[85vh] w-full z-[90] bg-[#0d0e17]/95 backdrop-blur-3xl border-t border-white/10 rounded-t-[2rem] shadow-[0_-25px_60px_rgba(0,0,0,0.9)]"
     role="dialog" 
     tabindex="-1" 
     aria-labelledby="notifications-offcanvas-label"
     onclick="event.stopPropagation()">
    
    <div class="flex flex-col h-full max-h-[85vh]">
        {{-- Drag Handle --}}
        <div class="flex justify-center pt-3 pb-1 flex-shrink-0">
            <span class="w-12 h-1 bg-white/20 hover:bg-white/40 rounded-full cursor-pointer transition-colors" onclick="closeOverlay('#notifications-offcanvas', false)"></span>
        </div>
        
        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-3.5 border-b border-white/10 flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-viewlix-red/20 text-viewlix-red flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h3 id="notifications-offcanvas-label" class="text-base font-bold text-white tracking-tight">Notifications</h3>
                <span id="mobile-offcanvas-badge" class="inline-flex items-center justify-center min-w-[20px] h-[20px] px-1.5 text-[11px] font-bold text-white bg-viewlix-red rounded-full hidden shadow-[0_0_8px_rgba(229,9,20,0.5)]">
                    0
                </span>
            </div>
            <button type="button" 
                    onclick="if(typeof markAllNotificationsRead === 'function') markAllNotificationsRead()"
                    id="mobile-mark-all-read"
                    class="text-xs text-viewlix-red hover:text-red-400 font-semibold hidden transition-colors">
                Mark all read
            </button>
        </div>
        
        {{-- Notification List --}}
        <div class="overflow-y-auto flex-1 overscroll-contain" id="mobile-notification-list">
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-12 text-center px-4">
                <div class="w-16 h-16 rounded-2xl bg-white/[0.03] border border-white/5 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                </div>
                <p class="text-base font-medium text-gray-300 mb-1">No notifications</p>
                <p class="text-xs text-gray-500">We'll notify you when new releases and episodes drop</p>
            </div>
        </div>
        
        {{-- Footer --}}
        <div class="px-5 py-3.5 border-t border-white/10 flex items-center justify-between safe-area-bottom hidden" id="mobile-notif-footer">
            <button type="button" 
                    onclick="if (confirm('Clear all notifications?') && typeof clearAllNotifications === 'function') clearAllNotifications()"
                    class="text-xs text-gray-400 hover:text-red-400 font-medium transition-colors">
                Clear all
            </button>
            <button type="button" 
                    class="text-xs text-gray-400 hover:text-white font-medium transition-colors"
                    onclick="closeOverlay('#notifications-offcanvas', false)">
                Close
            </button>
        </div>
    </div>
</div>
@endauth

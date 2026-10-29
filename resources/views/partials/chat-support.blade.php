{{-- Floating Chat Support & Ticket System --}}
<div id="chat-support-wrapper" class="relative" 
     data-user-id="{{ auth()->id() }}"
     data-user-name="{{ auth()->user()?->name }}"
     data-user-email="{{ auth()->user()?->email }}"
     data-is-authenticated="{{ auth()->check() ? '1' : '0' }}">

    {{-- Floating Chat Bubble (Bottom Right) --}}
    <div id="chat-bubble-container" class="fixed bottom-20 md:bottom-4 right-4 sm:right-5 z-40 flex items-center gap-2.5">
        
        {{-- Teaser Greeting Pill --}}
        <div id="chat-teaser-pill" class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-2xl bg-[#0c0d16]/95 backdrop-blur-xl border border-white/10 shadow-[0_8px_30px_rgba(0,0,0,0.8)] text-xs text-gray-200 transition-all duration-300">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <span>Need help? <strong>Chat with us!</strong></span>
            <button type="button" id="chat-teaser-close" class="text-gray-400 hover:text-white ml-1 p-0.5" title="Dismiss">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Main FAB Button --}}
        <button type="button" 
                id="chat-bubble-toggle" 
                aria-label="Toggle live chat support"
                class="relative w-12 h-12 sm:w-13 sm:h-13 rounded-2xl bg-gradient-to-tr from-viewlix-red via-blue-600 to-indigo-600 text-white shadow-[0_6px_25px_rgba(0,74,173,0.5)] hover:shadow-[0_8px_30px_rgba(0,74,173,0.7)] hover:scale-105 active:scale-95 transition-all duration-300 flex items-center justify-center cursor-pointer border border-white/20 group">
            
            {{-- Online Pulse Dot --}}
            <span class="absolute -top-1 -right-1 flex h-3.5 w-3.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500 border-2 border-[#08080a]"></span>
            </span>

            {{-- Chat Icon (Default) --}}
            <svg id="chat-bubble-icon-open" class="w-6 h-6 transition-transform group-hover:rotate-12 duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>

            {{-- Close X Icon (Hidden when closed) --}}
            <svg id="chat-bubble-icon-close" class="w-5 h-5 hidden transition-transform group-hover:rotate-90 duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>

            {{-- Unread Message Indicator Pill --}}
            <span id="chat-unread-badge" class="hidden absolute -bottom-1 -left-1 px-1.5 py-0.5 rounded-full bg-red-600 border border-white/40 text-[9px] font-bold text-white shadow-lg animate-bounce">
                New
            </span>
        </button>
    </div>

    {{-- Support Window Panel --}}
    <div id="chat-window" 
         class="fixed bottom-36 md:bottom-[70px] right-4 sm:right-5 z-50 w-[calc(100vw-2rem)] sm:w-[380px] md:w-[395px] h-[480px] max-h-[calc(100vh-85px)] rounded-3xl bg-[#0c0d16]/95 backdrop-blur-2xl border border-white/10 shadow-[0_25px_70px_rgba(0,0,0,0.95)] flex flex-col overflow-hidden transition-all duration-300 transform scale-95 opacity-0 pointer-events-none origin-bottom-right">

        {{-- Window Header --}}
        <div class="px-4 py-3.5 bg-gradient-to-r from-white/[0.04] to-transparent border-b border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="relative w-9 h-9 rounded-xl bg-gradient-to-tr from-viewlix-red/30 to-blue-600/30 border border-white/15 flex items-center justify-center text-white">
                    <img src="{{ asset('logo.png') }}" alt="Viewlix" class="w-5 h-5 object-contain">
                    <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 rounded-full bg-emerald-500 border border-[#0c0d16]"></span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-black text-white tracking-tight">Viewlix Support</h3>
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-blue-500/20 text-blue-400 border border-blue-500/30">Live</span>
                    </div>
                    <p class="text-[11px] text-gray-400 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span>Online • Instant Help</span>
                    </p>
                </div>
            </div>

            {{-- Header Actions --}}
            <div class="flex items-center gap-1.5">
                {{-- Live Chat Switcher --}}
                <button type="button" 
                        id="chat-tab-live" 
                        title="Live Chat Support"
                        class="px-2 py-1 rounded-xl bg-viewlix-red text-white text-xs font-semibold transition-all flex items-center gap-1 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <span>Chat</span>
                </button>

                {{-- Ticket Mode Button --}}
                <button type="button" 
                        id="chat-tab-tickets" 
                        title="My Support Tickets"
                        class="px-2 py-1 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-semibold text-gray-300 hover:text-white transition-all flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                    <span>Tickets</span>
                </button>

                {{-- New General Chat Button --}}
                <button type="button" 
                        id="chat-btn-new-chat" 
                        title="Start a new chat session"
                        class="px-2 py-1 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-semibold text-gray-400 hover:text-white transition-all flex items-center gap-0.5">
                    <span>+ New</span>
                </button>

                {{-- Close / Minimize Button --}}
                <button type="button" 
                        id="chat-window-close" 
                        title="Close Support"
                        class="w-7 h-7 rounded-xl bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white flex items-center justify-center transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Active Ticket Context Banner (Shown only when in ticket chat mode) --}}
        <div id="chat-ticket-banner" class="hidden px-3.5 py-2 bg-gradient-to-r from-amber-500/15 via-purple-500/10 to-transparent border-b border-white/10 flex items-center justify-between text-xs shrink-0">
            <div class="flex items-center gap-2 min-w-0">
                <span class="text-amber-400 font-bold">🎫</span>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5">
                        <span id="chat-ticket-banner-id" class="font-mono font-bold text-amber-300 text-[11px]"></span>
                        <span id="chat-ticket-banner-status" class="text-[9px] uppercase font-bold px-1.5 py-0.2 rounded border bg-blue-500/20 text-blue-400 border-blue-500/30"></span>
                    </div>
                    <p id="chat-ticket-banner-subject" class="text-[10px] text-gray-300 truncate max-w-[190px]"></p>
                </div>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
                <button type="button" id="chat-ticket-reopen-banner-btn" class="hidden text-[11px] px-2 py-1 rounded-xl bg-emerald-600/30 hover:bg-emerald-600 text-emerald-300 hover:text-white border border-emerald-500/30 transition-colors font-medium">
                    🔄 Reopen
                </button>
                <button type="button" id="chat-ticket-back-to-live" class="text-[11px] px-2.5 py-1 rounded-xl bg-white/10 hover:bg-white/20 text-gray-200 hover:text-white transition-colors shrink-0 font-medium">
                    ← Live Chat
                </button>
            </div>
        </div>

        {{-- VIEW 1: Direct Live Chat & Ticket Chat Thread --}}
        <div id="chat-view-messages" class="flex-1 flex flex-col min-h-0">
            
            {{-- Messages Stream Container --}}
            <div id="chat-messages-container" class="flex-1 p-4 overflow-y-auto modern-scrollbar space-y-3">
                {{-- Initial Bot Welcome Message --}}
                <div id="chat-default-welcome" class="flex items-start gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-viewlix-red/40 to-blue-600/40 border border-white/10 flex items-center justify-center text-white flex-shrink-0 mt-0.5">
                        <img src="{{ asset('logo.png') }}" alt="V" class="w-4 h-4 object-contain">
                    </div>
                    <div class="max-w-[85%] sm:max-w-[75%] flex flex-col items-start">
                        <div class="inline-block max-w-full px-3.5 py-2.5 rounded-2xl rounded-tl-sm bg-[#161722] border border-white/10 text-xs text-gray-200 leading-relaxed space-y-1.5 shadow-sm">
                            @auth
                                <p class="font-semibold text-white">Hi {{ auth()->user()->name }}! 👋 Welcome to Viewlix Support.</p>
                                <p class="text-gray-300">How can we help you today? Type your message below, or select a quick topic:</p>
                            @else
                                <p class="font-semibold text-white">Hi there! 👋 Welcome to Viewlix Support.</p>
                                <p class="text-gray-300">Please sign in to your account to send live messages and request assistance from our support team.</p>
                            @endauth
                        </div>
                        <span class="text-[10px] text-gray-400 px-1 mt-1">Just now</span>
                    </div>
                </div>

                {{-- Quick Prompt Suggestion Chips --}}
                <div class="flex flex-wrap gap-1.5 pt-1 pl-9" id="chat-quick-chips">
                    <button type="button" class="chat-chip px-2.5 py-1 rounded-lg bg-white/5 hover:bg-blue-600/20 border border-white/10 hover:border-blue-500/40 text-[11px] text-gray-300 hover:text-white transition-all">
                        🎬 Video playback issue
                    </button>
                    <button type="button" class="chat-chip px-2.5 py-1 rounded-lg bg-white/5 hover:bg-blue-600/20 border border-white/10 hover:border-blue-500/40 text-[11px] text-gray-300 hover:text-white transition-all">
                        ✨ Request movie / TV show
                    </button>
                    <button type="button" class="chat-chip px-2.5 py-1 rounded-lg bg-white/5 hover:bg-blue-600/20 border border-white/10 hover:border-blue-500/40 text-[11px] text-gray-300 hover:text-white transition-all">
                        🔑 Account / Login help
                    </button>
                    <button type="button" class="chat-chip px-2.5 py-1 rounded-lg bg-white/5 hover:bg-blue-600/20 border border-white/10 hover:border-blue-500/40 text-[11px] text-gray-300 hover:text-white transition-all">
                        🎫 Submit formal ticket
                    </button>
                </div>
            </div>

            @auth
            {{-- Message Input Bar (Authenticated Users) --}}
            <div id="chat-active-input-container" class="p-3 bg-[#08080c]/90 border-t border-white/10">
                <form id="chat-message-form" class="flex items-center gap-2">
                    <input type="text" 
                           id="chat-message-input" 
                           placeholder="Type your message..." 
                           autocomplete="off"
                           maxlength="2000"
                           class="flex-1 px-3.5 py-2.5 rounded-xl bg-white/[0.05] border border-white/10 text-xs text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition-colors">
                    
                    <button type="submit" 
                            id="chat-send-button"
                            class="w-10 h-10 rounded-xl bg-gradient-to-tr from-viewlix-red to-blue-600 hover:from-blue-600 hover:to-viewlix-red text-white flex items-center justify-center shadow-md transition-all disabled:opacity-50 flex-shrink-0">
                        <svg class="w-4 h-4 ml-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </form>
                <div class="flex items-center justify-between text-[10px] text-gray-400 px-1 mt-1.5">
                    <span id="chat-session-indicator">⚡ Live Chat Session</span>
                    <button type="button" id="chat-open-ticket-link" class="text-blue-400 hover:underline">
                        Need formal ticket? Submit Ticket
                    </button>
                </div>
            </div>
            @else
            {{-- Guest Message Input Lock Bar --}}
            <div id="chat-active-input-container" class="p-3.5 bg-[#0b0c16] border-t border-white/10 flex flex-col gap-2 text-center">
                <div class="flex items-center justify-center gap-1.5 text-xs font-bold text-amber-300">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Sign In to Send Messages</span>
                </div>
                <p class="text-[11px] text-gray-400 leading-normal">
                    Only authenticated members can send messages and request support.
                </p>
                <div class="flex items-center justify-center gap-2 pt-1">
                    <a href="{{ route('login') }}" class="px-4 py-1.5 rounded-xl bg-gradient-to-r from-viewlix-red to-blue-600 hover:from-blue-600 hover:to-viewlix-red text-white font-bold text-xs shadow-md transition-all">
                        Sign In
                    </a>
                    @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="px-3.5 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-gray-300 hover:text-white font-semibold text-xs transition-all">
                        Create Account
                    </a>
                    @endif
                </div>
            </div>
            @endauth

            {{-- Closed / Resolved Ticket Footer with Reopen Request Workflow --}}
            <div id="chat-ticket-closed-footer" class="hidden p-3.5 bg-[#090a12] border-t border-white/10 space-y-2.5">
                {{-- State A: Closed / Resolved Notice with Request Reopen Button --}}
                <div id="chat-reopen-state-closed" class="space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-semibold text-emerald-400 flex items-center gap-1.5">
                            <span>🔒</span>
                            <span>This ticket is resolved</span>
                        </span>
                        <span class="text-[10px] text-gray-400 font-mono" id="chat-closed-ticket-id"></span>
                    </div>
                    <p class="text-[11px] text-gray-300 leading-normal">
                        New messages cannot be sent on resolved tickets. If you still require help, you may submit a request to reopen this ticket.
                    </p>
                    
                    {{-- Notice: If Previous Reopen Was Declined --}}
                    <div id="chat-ticket-declined-notice" class="hidden p-2.5 rounded-xl bg-red-950/40 border border-red-500/40 text-left space-y-1">
                        <div class="flex items-center gap-1.5 text-[11px] font-bold text-red-300">
                            <span>✕ Reopen Request Declined by Support</span>
                        </div>
                        <p id="chat-ticket-declined-reason-text" class="text-[11px] text-gray-200 leading-relaxed italic"></p>
                    </div>

                    {{-- Open Reopen Form Button --}}
                    <button type="button" 
                            id="chat-btn-show-reopen-form"
                            class="w-full py-2 px-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-semibold text-xs transition-all flex items-center justify-center gap-1.5 shadow-md shadow-emerald-950/40">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Request to Reopen Ticket</span>
                    </button>

                    {{-- Expandable Reopen Request Form (Initially Hidden) --}}
                    <div id="chat-reopen-form-container" class="hidden space-y-2 pt-1">
                        <label class="block text-[11px] text-gray-300 font-medium">
                            Explain why you want to reopen this ticket:
                        </label>
                        <textarea id="chat-reopen-reason-input" 
                                  rows="2" 
                                  placeholder="e.g. Video is still buffering on episode 3, or I still need assistance..." 
                                  class="w-full bg-[#141522] border border-white/10 rounded-xl px-3 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-emerald-500 resize-none"></textarea>
                        <div class="flex items-center justify-end gap-2 pt-0.5">
                            <button type="button" 
                                    id="chat-btn-cancel-reopen" 
                                    class="px-3 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white text-xs font-medium transition-colors">
                                Cancel
                            </button>
                            <button type="button" 
                                    id="chat-btn-submit-reopen" 
                                    class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold transition-all flex items-center gap-1.5 shadow-sm">
                                <span>Submit Reopen Request</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- State B: Pending Reopen Request (Awaiting Admin Review) --}}
                <div id="chat-reopen-state-pending" class="hidden p-3 rounded-xl bg-amber-500/10 border border-amber-500/30 space-y-1.5 text-left">
                    <div class="flex items-center gap-1.5 text-xs font-bold text-amber-300">
                        <svg class="w-4 h-4 animate-spin text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Reopen Request Pending Review</span>
                    </div>
                    <p class="text-[11px] text-amber-100/90 leading-relaxed">
                        Your request has been submitted to support. An agent will review it shortly. Messaging will unlock once approved.
                    </p>
                    <div id="chat-reopen-pending-reason" class="text-[10px] text-amber-200/70 italic truncate"></div>
                </div>
            </div>
        </div>

        {{-- VIEW 2: Support Tickets & Ticket History (Initially Hidden) --}}
        <div id="chat-view-ticket" class="hidden flex-1 flex flex-col min-h-0 p-4 overflow-y-auto modern-scrollbar">
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-sm font-bold text-white flex items-center gap-2">
                    <span>Support Tickets Hub</span>
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30">Multi-Session</span>
                </h4>
                <button type="button" id="ticket-back-to-chat" class="text-xs text-gray-400 hover:text-white transition-colors flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    <span>Back to Live Chat</span>
                </button>
            </div>

            @auth
            <form id="support-ticket-form" class="space-y-3">
                {{-- Category --}}
                <div>
                    <label for="ticket-category" class="block text-[11px] font-semibold text-gray-300 mb-1">Issue Category</label>
                    <select id="ticket-category" class="w-full px-3 py-2 rounded-xl bg-white/[0.05] border border-white/10 text-xs text-white focus:outline-none focus:border-blue-500 transition-colors">
                        <option value="playback" class="bg-[#0c0d16]">Playback / Video Streaming Issue</option>
                        <option value="request" class="bg-[#0c0d16]">Request a Movie or TV Show</option>
                        <option value="account" class="bg-[#0c0d16]">Account, Login or Favorites Sync</option>
                        <option value="bug" class="bg-[#0c0d16]">Bug Report / Technical Glitch</option>
                        <option value="other" class="bg-[#0c0d16]">Other Inquiries</option>
                    </select>
                </div>

                {{-- Subject --}}
                <div>
                    <label for="ticket-subject" class="block text-[11px] font-semibold text-gray-300 mb-1">Subject</label>
                    <input type="text" id="ticket-subject" required maxlength="150" placeholder="Brief summary of your request" class="w-full px-3 py-2 rounded-xl bg-white/[0.05] border border-white/10 text-xs text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition-colors">
                </div>

                {{-- Priority & Contact Email --}}
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label for="ticket-priority" class="block text-[11px] font-semibold text-gray-300 mb-1">Priority</label>
                        <select id="ticket-priority" class="w-full px-3 py-2 rounded-xl bg-white/[0.05] border border-white/10 text-xs text-white focus:outline-none focus:border-blue-500 transition-colors">
                            <option value="normal" class="bg-[#0c0d16]">Normal</option>
                            <option value="high" class="bg-[#0c0d16]">High</option>
                            <option value="urgent" class="bg-[#0c0d16]">Urgent</option>
                        </select>
                    </div>
                    <div>
                        <label for="ticket-email" class="block text-[11px] font-semibold text-gray-300 mb-1">Contact Email</label>
                        <input type="email" id="ticket-email" value="{{ auth()->user()?->email }}" readonly class="w-full px-3 py-2 rounded-xl bg-white/[0.05] border border-white/10 text-xs text-gray-300 focus:outline-none focus:border-blue-500 transition-colors cursor-not-allowed">
                    </div>
                </div>

                {{-- Message Details --}}
                <div>
                    <label for="ticket-message" class="block text-[11px] font-semibold text-gray-300 mb-1">Description / Details</label>
                    <textarea id="ticket-message" required rows="3" maxlength="3000" placeholder="Describe the issue or details of your request..." class="w-full px-3 py-2 rounded-xl bg-white/[0.05] border border-white/10 text-xs text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 transition-colors resize-none"></textarea>
                </div>

                {{-- Submit Button --}}
                <button type="submit" id="ticket-submit-btn" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-viewlix-red to-blue-600 hover:from-blue-600 hover:to-viewlix-red text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <span>Submit Support Ticket</span>
                </button>
            </form>

            {{-- Recent Tickets List --}}
            <div class="mt-4 pt-3 border-t border-white/10">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-gray-300">My Recent Tickets</span>
                    <button type="button" id="refresh-tickets-btn" class="text-[11px] text-blue-400 hover:underline">Refresh</button>
                </div>
                <div id="ticket-history-container" class="space-y-2 text-xs">
                    <p class="text-[11px] text-gray-400 italic">No previous tickets found.</p>
                </div>
            </div>
            @else
            {{-- Guest Ticket View Card --}}
            <div class="p-6 rounded-2xl bg-white/[0.03] border border-white/10 text-center space-y-3.5 my-auto">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center mx-auto">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-white">Sign In to Request Support</h4>
                <p class="text-xs text-gray-400 max-w-xs mx-auto leading-relaxed">
                    Only authenticated Viewlix members can submit support tickets and track ticket resolutions.
                </p>
                <div class="flex items-center justify-center gap-2 pt-1">
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-viewlix-red to-blue-600 hover:from-blue-600 hover:to-viewlix-red text-white font-bold text-xs shadow-md transition-all">
                        Sign In
                    </a>
                    @if(Route::has('register'))
                    <a href="{{ route('register') }}" class="px-4 py-2.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-gray-300 hover:text-white font-semibold text-xs transition-all">
                        Create Account
                    </a>
                    @endif
                </div>
            </div>
            @endauth
        </div>
    </div>
</div>

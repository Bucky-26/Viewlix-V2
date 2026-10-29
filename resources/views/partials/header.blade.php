{{-- Floating Top Navigation Bar --}}
<header class="sticky top-2.5 sm:top-3.5 z-40 mx-2.5 sm:mx-4 md:mx-6 lg:mx-8 mt-2.5 sm:mt-3.5 mb-3 rounded-2xl bg-[#0c0d16]/70 backdrop-blur-3xl border border-white/10 shadow-[0_8px_32px_rgba(0,0,0,0.6)] transition-all duration-300">
    <div class="flex items-center justify-between h-14 md:h-16 px-3 sm:px-4 md:px-5 lg:px-6">
        
        {{-- Brand / Logo (Visible on all viewports) --}}
        <div class="flex items-center me-2 sm:me-3 md:me-4 flex-shrink-0">
            <a href="{{ route('home') }}" class="flex items-center gap-1.5 sm:gap-2 group">
                <img src="{{ asset('viewlix.png') }}" alt="Viewlix" class="h-5 sm:h-5 md:h-5.5 w-auto object-contain">
            </a>
        </div>

        {{-- Desktop Navigation Links (Home, Movies, Filipino, TV Shows, Anime) --}}
        <nav class="hidden md:flex items-center gap-1 lg:gap-1.5 me-4 flex-shrink-0">
            <a href="{{ route('home') }}" class="px-2.5 lg:px-3 py-1.5 rounded-xl text-xs font-semibold transition-all duration-300 {{ request()->routeIs('home') ? 'bg-white/10 text-white shadow-sm' : 'text-gray-400 hover:text-white hover:bg-white/5 hover:drop-shadow-[0_0_8px_rgba(255,255,255,0.4)]' }}">
                Home
            </a>
            <a href="{{ route('movies') }}" class="px-2.5 lg:px-3 py-1.5 rounded-xl text-xs font-semibold transition-all duration-300 {{ request()->routeIs('movies') ? 'bg-white/10 text-white shadow-sm' : 'text-gray-400 hover:text-white hover:bg-white/5 hover:drop-shadow-[0_0_8px_rgba(255,255,255,0.4)]' }}">
                Movies
            </a>
            <a href="{{ route('filipino') }}" class="px-2.5 lg:px-3 py-1.5 rounded-xl text-xs font-semibold transition-all duration-300 {{ request()->routeIs('filipino') ? 'bg-white/10 text-white shadow-sm' : 'text-gray-400 hover:text-white hover:bg-white/5 hover:drop-shadow-[0_0_8px_rgba(255,255,255,0.4)]' }}">
                Filipino
            </a>
            <a href="{{ route('tv-shows') }}" class="px-2.5 lg:px-3 py-1.5 rounded-xl text-xs font-semibold transition-all duration-300 {{ request()->routeIs('tv-shows') ? 'bg-white/10 text-white shadow-sm' : 'text-gray-400 hover:text-white hover:bg-white/5 hover:drop-shadow-[0_0_8px_rgba(255,255,255,0.4)]' }}">
                TV Shows
            </a>
            <a href="{{ route('anime') }}" class="px-2.5 lg:px-3 py-1.5 rounded-xl text-xs font-semibold transition-all duration-300 {{ request()->routeIs('anime') ? 'bg-white/10 text-white shadow-sm' : 'text-gray-400 hover:text-white hover:bg-white/5 hover:drop-shadow-[0_0_8px_rgba(255,255,255,0.4)]' }}">
                Anime
            </a>
        </nav>

        {{-- Search Bar --}}
        <div class="flex-1 max-w-xl w-full">
            <form action="{{ route('search') }}" method="GET" class="relative w-full">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-gray-400">
                    <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div class="relative w-full">
                    <input id="global-search-input" 
                           type="search" 
                           name="q"
                           value="{{ request('q') }}"
                           class="block w-full ps-9 md:ps-10 pe-9 md:pe-12 py-1.5 md:py-2 text-xs md:text-sm text-white bg-white/[0.04] border border-white/10 hover:border-white/20 focus:border-viewlix-red focus:bg-[#121218] shadow-inner focus:shadow-[0_0_15px_rgba(229,9,20,0.3)] focus:ring-2 focus:ring-viewlix-red/20 rounded-xl placeholder-gray-500 transition-all duration-300 outline-none"
                           placeholder="Search movies, TV, anime..."
                           autocomplete="off">
                    
                    {{-- Keyboard shortcut badge (desktop) --}}
                    <div class="absolute inset-y-0 end-0 pe-3 flex items-center pointer-events-none">
                        <kbd class="hidden sm:inline-flex items-center px-1.5 py-0.5 text-[10px] font-semibold text-gray-400 bg-white/5 border border-white/10 rounded-md">
                            /
                        </kbd>
                    </div>

                    {{-- Autocomplete suggestions dropdown container --}}
                    <div id="search-suggestions" class="hidden z-50 absolute left-0 right-0 mt-2 bg-[#0f0f15]/95 backdrop-blur-2xl border border-white/10 rounded-2xl shadow-2xl shadow-black/80 overflow-hidden">
                        <ul class="divide-y divide-white/5 max-h-96 overflow-y-auto" role="listbox"></ul>
                    </div>
                </div>
            </form>
        </div>
        
        {{-- Right Controls --}}
        <div class="hidden md:flex items-center gap-1.5 sm:gap-2.5 ms-auto md:ms-4">
            
            {{-- Mobile Search Icon --}}
            <a href="{{ route('search') }}" 
               class="md:hidden relative p-2 text-gray-400 hover:text-white hover:bg-white/10 rounded-xl transition-all" 
               title="Search">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </a>

            @auth
            {{-- Admin Gate Icon Button (Visible only to Admin users) --}}
            @if(Auth::user()->isAdmin())
            <a href="{{ route('admin.gate.enter') }}" 
               class="hidden sm:flex relative p-2 text-blue-400 hover:text-white hover:bg-blue-600/20 rounded-xl transition-all border border-blue-500/30 items-center justify-center group shadow-sm shadow-blue-500/10" 
               title="Admin Panel Gate"
               aria-label="Admin Panel Gate">
                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </a>
            @endif

            {{-- Watch History Link --}}
            <a href="{{ route('watch-history') }}" 
               class="hidden sm:inline-flex relative p-2 text-gray-400 hover:text-white hover:bg-white/10 rounded-xl transition-all" 
               title="Watch History">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </a>

            {{-- Notification Bell - Desktop (opens right offcanvas) --}}
            <button id="desktop-notif-button" 
                    type="button" 
                    class="hidden md:inline-flex relative p-2 text-gray-400 hover:text-white hover:bg-white/10 rounded-xl transition-all" 
                    aria-label="Open notifications"
                    title="Notifications"
                    data-hs-overlay="#notifications-desktop-offcanvas">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                {{-- Notification Badge --}}
                <span id="desktop-notif-badge"
                      class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-viewlix-red rounded-full hidden shadow-md shadow-viewlix-red/40">
                    0
                </span>
            </button>

            {{-- Notification Bell - Mobile (opens bottom offcanvas) --}}
            <button type="button" 
                    class="inline-flex md:hidden relative p-2 text-gray-400 hover:text-white hover:bg-white/10 rounded-xl transition-all" 
                    onclick="openOverlay('#notifications-offcanvas', false)"
                    aria-controls="notifications-offcanvas"
                    aria-label="Open notifications"
                    title="Notifications">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span id="mobile-notif-badge"
                      class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-viewlix-red rounded-full hidden shadow-md shadow-viewlix-red/40">
                    0
                </span>
            </button>

            {{-- Quick Logout Button (Desktop) --}}
            <form method="POST" action="{{ route('logout') }}" class="hidden lg:block">
                @csrf
                <button type="submit" class="p-2 text-gray-400 hover:text-red-400 hover:bg-red-500/10 rounded-xl transition-all" title="Logout">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
            
            {{-- User Dropdown Menu (Desktop Alpine.js) --}}
            <div class="relative inline-flex" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                <button id="user-avatar-dropdown-btn" 
                        type="button" 
                        @click="open = !open"
                        :aria-expanded="open"
                        aria-haspopup="true"
                        aria-label="User profile & settings menu"
                        class="flex items-center gap-1.5 p-1 hover:bg-white/10 rounded-xl transition-all border border-white/5 hover:border-white/20 focus:outline-none focus:ring-2 focus:ring-blue-500/40 cursor-pointer">
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-blue-500/30">
                    @else
                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-viewlix-red to-blue-600 flex items-center justify-center text-white font-bold text-xs uppercase shadow-md shadow-viewlix-red/20 ring-2 ring-white/10">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif
                    <svg class="w-3.5 h-3.5 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180 text-white' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                
                <div x-show="open"
                     x-cloak
                     style="display: none;"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                     class="absolute right-0 top-full mt-2 w-64 bg-[#0f0f15]/95 backdrop-blur-2xl shadow-2xl shadow-black/90 rounded-2xl p-2 border border-white/10 z-50 divide-y divide-white/5">
                    
                    {{-- User Identity Header --}}
                    <div class="px-3.5 py-2.5">
                        <p class="text-sm font-bold text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate mt-0.5">{{ Auth::user()->email }}</p>
                        @if(Auth::user()->google_id)
                            <span class="inline-flex items-center gap-1 mt-1.5 text-[11px] text-emerald-400 font-medium">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Google Connected
                            </span>
                        @endif
                    </div>
                    
                    {{-- Navigation Links --}}
                    <div class="py-1 space-y-0.5">
                        @if(Auth::user()->isAdmin())
                        <a class="flex items-center gap-x-3 py-2 px-3 rounded-xl text-xs font-semibold text-blue-400 hover:bg-blue-500/10 transition-colors" href="{{ route('admin.gate.enter') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>Admin Control Panel</span>
                        </a>
                        @endif

                        <a class="flex items-center gap-x-3 py-2 px-3 rounded-xl text-xs text-gray-300 hover:text-white hover:bg-white/5 transition-colors font-medium" href="{{ route('profile.show') }}">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>Profile Showcase</span>
                        </a>

                        <a class="flex items-center gap-x-3 py-2 px-3 rounded-xl text-xs text-gray-300 hover:text-white hover:bg-white/5 transition-colors font-medium" href="{{ route('profile.edit') }}">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>Settings &amp; Avatar</span>
                        </a>
                        
                        <a class="flex items-center gap-x-3 py-2 px-3 rounded-xl text-xs text-gray-300 hover:text-white hover:bg-white/5 transition-colors font-medium" href="{{ route('favorites') }}">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            <span>My Favorites</span>
                        </a>
                        
                        <a class="flex items-center gap-x-3 py-2 px-3 rounded-xl text-xs text-gray-300 hover:text-white hover:bg-white/5 transition-colors font-medium" href="{{ route('watch-history') }}">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Watch History</span>
                        </a>
                    </div>
                    
                    {{-- Logout Action --}}
                    <div class="pt-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-x-3 py-2 px-3 rounded-xl text-xs font-semibold text-red-400 hover:bg-red-500/10 hover:text-red-300 transition-colors cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                <span>Log Out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @else
            {{-- Guest Controls --}}
            <a href="{{ route('login') }}" class="hidden sm:inline-flex px-3.5 py-1.5 text-xs font-semibold text-gray-300 hover:text-white transition-colors">
                Sign In
            </a>
            <a href="{{ route('register') }}" class="px-4 py-2 text-xs font-semibold rounded-xl bg-gradient-to-r from-viewlix-red to-blue-600 hover:from-blue-600 hover:to-viewlix-red text-white shadow-md shadow-viewlix-red/25 hover:shadow-viewlix-red/40 active:scale-[0.99] transition-all flex items-center gap-1.5">
                <span>Join Free</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </a>
            @endauth
        </div>
    </div>

    {{-- Mobile Category Quick Navigation (Floating on Top) --}}
    <nav class="hidden items-center gap-1.5 px-3 pb-2 pt-0.5 overflow-x-auto no-scrollbar border-t border-white/5">
        <a href="{{ route('home') }}" class="px-3 py-1 rounded-full text-[11px] font-semibold whitespace-nowrap transition-all {{ request()->routeIs('home') ? 'bg-gradient-to-r from-viewlix-red to-rose-600 text-white shadow-sm shadow-viewlix-red/40' : 'bg-white/5 text-gray-300 hover:text-white' }}">
            Home
        </a>
        <a href="{{ route('movies') }}" class="px-3 py-1 rounded-full text-[11px] font-semibold whitespace-nowrap transition-all {{ request()->routeIs('movies') ? 'bg-gradient-to-r from-viewlix-red to-rose-600 text-white shadow-sm shadow-viewlix-red/40' : 'bg-white/5 text-gray-300 hover:text-white' }}">
            Movies
        </a>
        <a href="{{ route('filipino') }}" class="px-3 py-1 rounded-full text-[11px] font-semibold whitespace-nowrap transition-all {{ request()->routeIs('filipino') ? 'bg-gradient-to-r from-viewlix-red to-rose-600 text-white shadow-sm shadow-viewlix-red/40' : 'bg-white/5 text-gray-300 hover:text-white' }}">
            Filipino
        </a>
        <a href="{{ route('tv-shows') }}" class="px-3 py-1 rounded-full text-[11px] font-semibold whitespace-nowrap transition-all {{ request()->routeIs('tv-shows') ? 'bg-gradient-to-r from-viewlix-red to-rose-600 text-white shadow-sm shadow-viewlix-red/40' : 'bg-white/5 text-gray-300 hover:text-white' }}">
            TV Shows
        </a>
        <a href="{{ route('anime') }}" class="px-3 py-1 rounded-full text-[11px] font-semibold whitespace-nowrap transition-all {{ request()->routeIs('anime') ? 'bg-gradient-to-r from-viewlix-red to-rose-600 text-white shadow-sm shadow-viewlix-red/40' : 'bg-white/5 text-gray-300 hover:text-white' }}">
            Anime
        </a>
    </nav>
</header>

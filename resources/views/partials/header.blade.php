{{-- Header --}}
<header class="sticky top-0 z-40 bg-viewlix-dark/80 backdrop-blur-md border-b border-viewlix-border">
    <div class="flex items-center justify-between h-16 px-4 lg:px-6">
        {{-- Search --}}
        <div class="flex-1 max-w-xl">
            <form action="{{ route('search') }}" method="GET" class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="search" 
                       name="q"
                       value="{{ request('q') }}"
                       class="block w-full ps-10 pe-4 py-2.5 text-sm text-white bg-viewlix-gray border border-viewlix-border rounded-xl focus:ring-viewlix-red focus:border-viewlix-red placeholder-gray-400"
                       placeholder="Search movies, TV shows, anime...">
            </form>
        </div>
        
        {{-- Right Side --}}
        <div class="flex items-center gap-3 ms-4">
            @auth
            {{-- Watch History Link --}}
            <a href="{{ route('watch-history') }}" class="relative p-2 text-gray-400 hover:text-white hover:bg-viewlix-gray rounded-xl transition-colors" title="Watch History">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </a>

            {{-- Notification Bell - Desktop (opens right offcanvas) --}}
        <button id="desktop-notif-button" type="button" 
            class="hidden md:inline-flex relative p-2 text-gray-400 hover:text-white hover:bg-viewlix-gray rounded-xl transition-colors" 
            aria-label="Open notifications"
            title="Notifications">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                {{-- Notification Badge --}}
                <span id="desktop-notif-badge"
                      class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-viewlix-red rounded-full hidden">
                    0
                </span>
            </button>

            {{-- Notification Bell - Mobile (opens bottom offcanvas) --}}
            <button type="button" 
                    class="inline-flex md:hidden relative p-2 text-gray-400 hover:text-white hover:bg-viewlix-gray rounded-xl transition-colors" 
                    onclick="openOverlay('#notifications-offcanvas', false)"
                    aria-controls="notifications-offcanvas"
                    aria-label="Open notifications"
                    title="Notifications">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                {{-- Notification Badge --}}
                <span id="mobile-notif-badge"
                      class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-bold text-white bg-viewlix-red rounded-full hidden">
                    0
                </span>
            </button>

            {{-- Logout Button (Desktop) --}}
            <form method="POST" action="{{ route('logout') }}" class="hidden lg:block">
                @csrf
                <button type="submit" class="p-2 text-gray-400 hover:text-red-400 hover:bg-red-500/10 rounded-xl transition-colors" title="Logout">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
            
            {{-- User Dropdown --}}
            <div class="hs-dropdown relative inline-flex">
                <button id="hs-dropdown-user" type="button" class="flex items-center gap-2 p-1 hover:bg-viewlix-gray rounded-xl transition-colors">
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="w-9 h-9 rounded-full object-cover">
                    @else
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-viewlix-red to-viewlix-red-dark flex items-center justify-center text-white font-semibold text-sm uppercase">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif
                </button>
                
                <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-48 bg-viewlix-darker shadow-xl rounded-xl p-2 mt-2 border border-viewlix-border" aria-labelledby="hs-dropdown-user">
                    <div class="px-3 py-2 border-b border-viewlix-border mb-2">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                        @if(Auth::user()->google_id)
                            <span class="inline-flex items-center gap-1 mt-1 text-xs text-green-400">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Google Connected
                            </span>
                        @endif
                    </div>
                    @if(Auth::user()->isAdmin())
                    <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-viewlix-red hover:bg-viewlix-gray font-medium" href="{{ route('admin.dashboard') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Admin Panel
                    </a>
                    @endif
                    <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-300 hover:bg-viewlix-gray hover:text-white" href="{{ route('account') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Account
                    </a>
                    <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-300 hover:bg-viewlix-gray hover:text-white" href="{{ route('profile.edit') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Edit Profile
                    </a>
                    <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-300 hover:bg-viewlix-gray hover:text-white" href="{{ route('favorites') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        Favorites
                    </a>
                    <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-300 hover:bg-viewlix-gray hover:text-white" href="{{ route('watch-history') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Watch History
                    </a>
                    <hr class="my-2 border-viewlix-border">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-red-400 hover:bg-viewlix-gray">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
            @else
            {{-- Guest: Login/Register Links (hidden on mobile) --}}
            <a href="{{ route('login') }}" class="hidden md:inline-block px-4 py-2 text-sm text-gray-300 hover:text-white transition-colors">
                Sign In
            </a>
            <a href="{{ route('register') }}" class="hidden md:inline-block px-4 py-2 bg-viewlix-red hover:bg-viewlix-red-dark text-white text-sm font-medium rounded-xl transition-colors">
                Sign Up
            </a>

            @endauth
        </div>
    </div>
</header>

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
            
            {{-- User Dropdown --}}
            <div class="hs-dropdown relative inline-flex">
                <button id="hs-dropdown-user" type="button" class="flex items-center gap-2 p-1 hover:bg-viewlix-gray rounded-xl transition-colors">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-viewlix-red to-viewlix-red-dark flex items-center justify-center text-white font-semibold text-sm uppercase">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </button>
                
                <div class="hs-dropdown-menu transition-[opacity,margin] duration hs-dropdown-open:opacity-100 opacity-0 hidden min-w-48 bg-viewlix-darker shadow-xl rounded-xl p-2 mt-2 border border-viewlix-border" aria-labelledby="hs-dropdown-user">
                    <div class="px-3 py-2 border-b border-viewlix-border mb-2">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                    </div>
                    <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-300 hover:bg-viewlix-gray hover:text-white" href="{{ route('profile.edit') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profile
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
            {{-- Guest: Login/Register Links --}}
            <a href="{{ route('login') }}" class="px-4 py-2 text-sm text-gray-300 hover:text-white transition-colors">
                Sign In
            </a>
            <a href="{{ route('register') }}" class="px-4 py-2 bg-viewlix-red hover:bg-viewlix-red-dark text-white text-sm font-medium rounded-xl transition-colors">
                Sign Up
            </a>
            @endauth
        </div>
    </div>
</header>

{{-- Mobile Bottom Navigation - Only visible on mobile --}}
<nav class="fixed bottom-0 left-0 right-0 z-50 bg-viewlix-darker/95 backdrop-blur-md border-t border-viewlix-border md:hidden safe-area-bottom">
    <div class="flex items-center justify-around h-16 px-2">
        {{-- Home --}}
        <a href="{{ route('home') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('home') ? 'text-viewlix-red' : 'text-gray-400 hover:text-white' }} transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="text-xs mt-1">Home</span>
        </a>
        
        {{-- Movies --}}
        <a href="{{ route('movies') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('movies') ? 'text-viewlix-red' : 'text-gray-400 hover:text-white' }} transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
            </svg>
            <span class="text-xs mt-1">Movies</span>
        </a>
        
        {{-- TV Shows --}}
        <a href="{{ route('tv-shows') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('tv-shows') ? 'text-viewlix-red' : 'text-gray-400 hover:text-white' }} transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <span class="text-xs mt-1">TV</span>
        </a>
        
        {{-- Anime --}}
        <a href="{{ route('anime') }}" class="flex flex-col items-center justify-center flex-1 py-2 {{ request()->routeIs('anime') ? 'text-viewlix-red' : 'text-gray-400 hover:text-white' }} transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
            <span class="text-xs mt-1">Anime</span>
        </a>
        
        {{-- Account/Login --}}
        @auth
        <button type="button" class="flex flex-col items-center justify-center flex-1 py-2 text-gray-400 hover:text-white transition-colors" onclick="openOverlay('#mobile-account-offcanvas')">
            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-viewlix-red to-viewlix-red-dark flex items-center justify-center text-white text-xs font-semibold uppercase">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <span class="text-xs mt-1">Account</span>
        </button>
        @else
        <a href="{{ route('login') }}" class="flex flex-col items-center justify-center flex-1 py-2 text-gray-400 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="text-xs mt-1">Profile</span>
        </a>
        @endauth
    </div>
</nav>

{{-- Mobile Account Offcanvas (for authenticated users) --}}
@auth
<div id="mobile-account-offcanvas" class="hidden translate-y-full fixed bottom-0 inset-x-0 transition-all duration-300 transform h-auto max-h-[85vh] w-full z-[80] bg-viewlix-darker border-t border-viewlix-border rounded-t-2xl" role="dialog" tabindex="-1" aria-labelledby="mobile-account-offcanvas-label">
    <div class="flex justify-center pt-3 pb-2">
        <span class="w-12 h-1.5 bg-viewlix-gray rounded-full cursor-pointer" onclick="closeOverlay('#mobile-account-offcanvas')"></span>
    </div>
    
    <div class="p-4 overflow-y-auto">
        {{-- User Info --}}
        <div class="flex items-center gap-4 mb-6 pb-4 border-b border-viewlix-border">
            @if(Auth::user()->avatar)
                <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="w-14 h-14 rounded-full object-cover">
            @else
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-viewlix-red to-viewlix-red-dark flex items-center justify-center text-white text-xl font-bold uppercase">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
            @endif
            <div>
                <h3 id="mobile-account-offcanvas-label" class="text-lg font-bold text-white">{{ Auth::user()->name }}</h3>
                <p class="text-sm text-gray-400">{{ Auth::user()->email }}</p>
                @if(Auth::user()->google_id)
                    <span class="inline-flex items-center gap-1 mt-1 text-xs text-green-400">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Google Connected
                    </span>
                @endif
            </div>
        </div>
        
        {{-- Menu Items --}}
        <div class="space-y-2">
            @if(Auth::user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-4 p-3 rounded-xl bg-viewlix-red/10 hover:bg-viewlix-red/20 transition-colors">
                <svg class="w-6 h-6 text-viewlix-red" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span class="text-viewlix-red font-medium">Admin Panel</span>
            </a>
            @endif
            
            <a href="{{ route('account') }}" class="flex items-center gap-4 p-3 rounded-xl hover:bg-viewlix-gray transition-colors">
                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-white">Account Settings</span>
            </a>
            
            <a href="{{ route('favorites') }}" class="flex items-center gap-4 p-3 rounded-xl hover:bg-viewlix-gray transition-colors">
                <svg class="w-6 h-6 text-viewlix-red" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <span class="text-white">My Favorites</span>
            </a>
            
            <a href="{{ route('watch-history') }}" class="flex items-center gap-4 p-3 rounded-xl hover:bg-viewlix-gray transition-colors">
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-white">Watch History</span>
            </a>
            
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-4 p-3 rounded-xl hover:bg-viewlix-gray transition-colors">
                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="text-white">Settings</span>
            </a>
        </div>
        
        {{-- Logout --}}
        <div class="mt-6 pt-4 border-t border-viewlix-border">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center gap-4 w-full p-3 rounded-xl hover:bg-viewlix-gray transition-colors text-red-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endauth

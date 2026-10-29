{{-- Mini Sidebar - Icon Only (Hidden on mobile) --}}
<aside class="hidden md:flex fixed inset-y-0 start-0 z-50 flex-col w-[72px] bg-[#09090d]/95 backdrop-blur-2xl border-e border-white/5">
    {{-- Logo --}}
    <div class="flex items-center justify-center h-16 border-b border-white/5">
        <a href="{{ route('home') }}" class="group flex items-center justify-center transition-transform hover:scale-105" title="Viewlix">
            <img src="{{ asset('logo.png') }}" alt="Viewlix" class="w-9 h-9 object-contain drop-shadow-[0_0_12px_rgba(0,74,173,0.4)] group-hover:drop-shadow-[0_0_18px_rgba(0,74,173,0.7)] transition-all">
        </a>
    </div>
    
    {{-- Navigation --}}
    <nav class="flex flex-col items-center py-4 space-y-2.5 flex-1 overflow-y-auto no-scrollbar">
        {{-- Home --}}
        <a href="{{ url('/') }}" 
           class="group relative flex items-center justify-center w-11 h-11 rounded-xl transition-all duration-200 
                  {{ request()->is('/') ? 'bg-gradient-to-tr from-viewlix-red to-blue-600 text-white shadow-lg shadow-viewlix-red/30 scale-105' : 'text-gray-400 hover:bg-white/10 hover:text-white' }}"
           title="Home">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="absolute left-full ml-3 px-2.5 py-1 text-xs font-semibold text-white bg-[#14141c]/95 backdrop-blur-md border border-white/10 rounded-lg shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
                Home
            </span>
        </a>
        
        {{-- Movies --}}
        <a href="{{ url('/movies') }}" 
           class="group relative flex items-center justify-center w-11 h-11 rounded-xl transition-all duration-200 
                  {{ request()->is('movies*') ? 'bg-gradient-to-tr from-viewlix-red to-blue-600 text-white shadow-lg shadow-viewlix-red/30 scale-105' : 'text-gray-400 hover:bg-white/10 hover:text-white' }}"
           title="Movies">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
            </svg>
            <span class="absolute left-full ml-3 px-2.5 py-1 text-xs font-semibold text-white bg-[#14141c]/95 backdrop-blur-md border border-white/10 rounded-lg shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
                Movies
            </span>
        </a>
        
        {{-- TV Shows --}}
        <a href="{{ url('/tv-shows') }}" 
           class="group relative flex items-center justify-center w-11 h-11 rounded-xl transition-all duration-200 
                  {{ request()->is('tv-shows*') ? 'bg-gradient-to-tr from-viewlix-red to-blue-600 text-white shadow-lg shadow-viewlix-red/30 scale-105' : 'text-gray-400 hover:bg-white/10 hover:text-white' }}"
           title="TV Shows">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <span class="absolute left-full ml-3 px-2.5 py-1 text-xs font-semibold text-white bg-[#14141c]/95 backdrop-blur-md border border-white/10 rounded-lg shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
                TV Shows
            </span>
        </a>
        
        {{-- Anime --}}
        <a href="{{ url('/anime') }}" 
           class="group relative flex items-center justify-center w-11 h-11 rounded-xl transition-all duration-200 
                  {{ request()->is('anime*') ? 'bg-gradient-to-tr from-viewlix-red to-blue-600 text-white shadow-lg shadow-viewlix-red/30 scale-105' : 'text-gray-400 hover:bg-white/10 hover:text-white' }}"
           title="Anime">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
            <span class="absolute left-full ml-3 px-2.5 py-1 text-xs font-semibold text-white bg-[#14141c]/95 backdrop-blur-md border border-white/10 rounded-lg shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
                Anime
            </span>
        </a>
        
        {{-- Filipino (Tagalog) Hub --}}
        <a href="{{ route('filipino') }}" 
           class="group relative flex items-center justify-center w-11 h-11 rounded-xl transition-all duration-200 
                  {{ request()->is('filipino*') ? 'bg-gradient-to-tr from-viewlix-red to-blue-600 text-white shadow-lg shadow-viewlix-red/30 scale-105' : 'text-gray-400 hover:bg-white/10 hover:text-white' }}"
           title="Filipino Movies & Series">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7a2 2 0 012-2h3l2-3h4l2 3h3a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 14s1-1 4-1 4 1 4 1"/>
            </svg>
            <span class="absolute left-full ml-3 px-2.5 py-1 text-xs font-semibold text-white bg-[#14141c]/95 backdrop-blur-md border border-white/10 rounded-lg shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
                Filipino Hub
            </span>
        </a>
        
        {{-- Favorites --}}
        <a href="{{ url('/favorites') }}" 
           class="group relative flex items-center justify-center w-11 h-11 rounded-xl transition-all duration-200 
                  {{ request()->is('favorites*') ? 'bg-gradient-to-tr from-viewlix-red to-blue-600 text-white shadow-lg shadow-viewlix-red/30 scale-105' : 'text-gray-400 hover:bg-white/10 hover:text-white' }}"
           title="Favorites">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <span class="absolute left-full ml-3 px-2.5 py-1 text-xs font-semibold text-white bg-[#14141c]/95 backdrop-blur-md border border-white/10 rounded-lg shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
                Favorites
            </span>
        </a>
        
        {{-- Watch History --}}
        @auth
        <a href="{{ route('watch-history') }}" 
           class="group relative flex items-center justify-center w-11 h-11 rounded-xl transition-all duration-200 
                  {{ request()->is('watch-history*') ? 'bg-gradient-to-tr from-viewlix-red to-blue-600 text-white shadow-lg shadow-viewlix-red/30 scale-105' : 'text-gray-400 hover:bg-white/10 hover:text-white' }}"
           title="Watch History">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="absolute left-full ml-3 px-2.5 py-1 text-xs font-semibold text-white bg-[#14141c]/95 backdrop-blur-md border border-white/10 rounded-lg shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
                Watch History
            </span>
        </a>

        {{-- Admin Panel (Admin users only) --}}
        @if(Auth::user()->isAdmin())
        <a href="{{ route('admin.gate.enter') }}" 
           class="group relative flex items-center justify-center w-11 h-11 rounded-xl transition-all duration-200 
                  {{ request()->is('admin*') ? 'bg-gradient-to-tr from-viewlix-red to-blue-600 text-white shadow-lg shadow-viewlix-red/30 scale-105' : 'text-blue-400 hover:bg-blue-500/20 hover:text-white' }}"
           title="Admin Panel">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            <span class="absolute left-full ml-3 px-2.5 py-1 text-xs font-semibold text-white bg-[#14141c]/95 backdrop-blur-md border border-white/10 rounded-lg shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
                Admin Gate
            </span>
        </a>
        @endif
        @endauth
    </nav>
    
    {{-- Account (Bottom) --}}
    <div class="flex flex-col items-center py-4 border-t border-white/5">
        @auth
        <a href="{{ route('profile.edit') }}" 
           class="group relative flex items-center justify-center w-11 h-11 rounded-xl transition-all duration-200 
                  {{ request()->is('profile*') ? 'bg-gradient-to-tr from-viewlix-red to-blue-600 text-white shadow-lg shadow-viewlix-red/30 scale-105' : 'text-gray-400 hover:bg-white/10 hover:text-white' }}"
           title="Profile">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="absolute left-full ml-3 px-2.5 py-1 text-xs font-semibold text-white bg-[#14141c]/95 backdrop-blur-md border border-white/10 rounded-lg shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
                Profile
            </span>
        </a>
        @else
        <a href="{{ route('login') }}" 
           class="group relative flex items-center justify-center w-11 h-11 rounded-xl transition-all duration-200 text-gray-400 hover:bg-white/10 hover:text-white"
           title="Sign In">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            <span class="absolute left-full ml-3 px-2.5 py-1 text-xs font-semibold text-white bg-[#14141c]/95 backdrop-blur-md border border-white/10 rounded-lg shadow-2xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none z-50">
                Sign In
            </span>
        </a>
        @endauth
    </div>
</aside>

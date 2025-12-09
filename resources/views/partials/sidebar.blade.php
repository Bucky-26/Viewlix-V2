{{-- Mini Sidebar - Icon Only (Hidden on mobile) --}}
<aside class="hidden md:flex fixed inset-y-0 start-0 z-50 flex-col w-[72px] bg-viewlix-darker border-e border-viewlix-border">
    {{-- Logo --}}
    <div class="flex items-center justify-center h-16 border-b border-viewlix-border">
        <a href="{{ route('home') }}" class="flex items-center justify-center">
            <svg class="w-8 h-8 text-viewlix-red" viewBox="0 0 24 24" fill="currentColor">
                <path d="M4 4h4l4 12 4-12h4L12 22 4 4z"/>
            </svg>
        </a>
    </div>
    
    {{-- Navigation --}}
    <nav class="flex flex-col items-center py-4 space-y-2 flex-1">
        {{-- Home --}}
        <a href="{{ url('/') }}" 
           class="group relative flex items-center justify-center w-12 h-12 rounded-xl transition-all duration-200 
                  {{ request()->is('/') ? 'bg-viewlix-red text-white' : 'text-gray-400 hover:bg-viewlix-gray hover:text-white' }}"
           title="Home">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="absolute left-full ml-2 px-2 py-1 text-sm font-medium text-white bg-viewlix-gray rounded-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                Home
            </span>
        </a>
        
        {{-- Movies --}}
        <a href="{{ url('/movies') }}" 
           class="group relative flex items-center justify-center w-12 h-12 rounded-xl transition-all duration-200 
                  {{ request()->is('movies*') ? 'bg-viewlix-red text-white' : 'text-gray-400 hover:bg-viewlix-gray hover:text-white' }}"
           title="Movies">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
            </svg>
            <span class="absolute left-full ml-2 px-2 py-1 text-sm font-medium text-white bg-viewlix-gray rounded-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                Movies
            </span>
        </a>
        
        {{-- TV Shows --}}
        <a href="{{ url('/tv-shows') }}" 
           class="group relative flex items-center justify-center w-12 h-12 rounded-xl transition-all duration-200 
                  {{ request()->is('tv-shows*') ? 'bg-viewlix-red text-white' : 'text-gray-400 hover:bg-viewlix-gray hover:text-white' }}"
           title="TV Shows">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <span class="absolute left-full ml-2 px-2 py-1 text-sm font-medium text-white bg-viewlix-gray rounded-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                TV Shows
            </span>
        </a>
        
        {{-- Anime --}}
        <a href="{{ url('/anime') }}" 
           class="group relative flex items-center justify-center w-12 h-12 rounded-xl transition-all duration-200 
                  {{ request()->is('anime*') ? 'bg-viewlix-red text-white' : 'text-gray-400 hover:bg-viewlix-gray hover:text-white' }}"
           title="Anime">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
            <span class="absolute left-full ml-2 px-2 py-1 text-sm font-medium text-white bg-viewlix-gray rounded-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                Anime
            </span>
        </a>
        
        {{-- Favorites --}}
        <a href="{{ url('/favorites') }}" 
           class="group relative flex items-center justify-center w-12 h-12 rounded-xl transition-all duration-200 
                  {{ request()->is('favorites*') ? 'bg-viewlix-red text-white' : 'text-gray-400 hover:bg-viewlix-gray hover:text-white' }}"
           title="Favorites">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <span class="absolute left-full ml-2 px-2 py-1 text-sm font-medium text-white bg-viewlix-gray rounded-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                Favorites
            </span>
        </a>
        
        {{-- Watch History --}}
        @auth
        <a href="{{ route('watch-history') }}" 
           class="group relative flex items-center justify-center w-12 h-12 rounded-xl transition-all duration-200 
                  {{ request()->is('watch-history*') ? 'bg-viewlix-red text-white' : 'text-gray-400 hover:bg-viewlix-gray hover:text-white' }}"
           title="Watch History">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="absolute left-full ml-2 px-2 py-1 text-sm font-medium text-white bg-viewlix-gray rounded-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                Watch History
            </span>
        </a>
        @endauth
    </nav>
    
    {{-- Account (Bottom) --}}
    <div class="flex flex-col items-center py-4 border-t border-viewlix-border">
        @auth
        <a href="{{ route('profile.edit') }}" 
           class="group relative flex items-center justify-center w-12 h-12 rounded-xl transition-all duration-200 
                  {{ request()->is('profile*') ? 'bg-viewlix-red text-white' : 'text-gray-400 hover:bg-viewlix-gray hover:text-white' }}"
           title="Profile">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="absolute left-full ml-2 px-2 py-1 text-sm font-medium text-white bg-viewlix-gray rounded-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                Profile
            </span>
        </a>
        @else
        <a href="{{ route('login') }}" 
           class="group relative flex items-center justify-center w-12 h-12 rounded-xl transition-all duration-200 text-gray-400 hover:bg-viewlix-gray hover:text-white"
           title="Sign In">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
            <span class="absolute left-full ml-2 px-2 py-1 text-sm font-medium text-white bg-viewlix-gray rounded-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                Sign In
            </span>
        </a>
        @endauth
    </div>
</aside>

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
        <button type="button" class="flex flex-col items-center justify-center flex-1 py-2 text-gray-400 hover:text-white transition-colors" onclick="openOverlay('#mobile-auth-offcanvas')">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="text-xs mt-1">Sign In</span>
        </button>
        @endauth
    </div>
</nav>

{{-- Mobile Auth Offcanvas (for guests) --}}
<div id="mobile-auth-offcanvas" class="hidden translate-y-full fixed bottom-0 inset-x-0 transition-all duration-300 transform h-auto max-h-[90vh] w-full z-[80] bg-viewlix-darker border-t border-viewlix-border rounded-t-2xl" role="dialog" tabindex="-1" aria-labelledby="mobile-auth-offcanvas-label">
    <div class="flex justify-center pt-3 pb-2">
        <span class="w-12 h-1.5 bg-viewlix-gray rounded-full cursor-pointer" onclick="closeOverlay('#mobile-auth-offcanvas')"></span>
    </div>
    
    <div class="p-4 overflow-y-auto max-h-[calc(90vh-40px)]" x-data="{ activeTab: 'login' }">
        {{-- Google Sign In Button --}}
        <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center gap-3 px-4 py-3 bg-white hover:bg-gray-100 text-gray-800 font-medium rounded-xl transition-colors mb-4">
            <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            Continue with Google
        </a>
        
        <div class="relative mb-4">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-viewlix-border"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-3 bg-viewlix-darker text-gray-500">or</span>
            </div>
        </div>
        
        {{-- Tab Buttons --}}
        <div class="flex mb-6 bg-viewlix-gray rounded-xl p-1">
            <button 
                @click="activeTab = 'login'" 
                :class="activeTab === 'login' ? 'bg-viewlix-red text-white' : 'text-gray-400 hover:text-white'"
                class="flex-1 py-2.5 text-sm font-semibold rounded-lg transition-colors"
            >
                Sign In
            </button>
            <button 
                @click="activeTab = 'register'" 
                :class="activeTab === 'register' ? 'bg-viewlix-red text-white' : 'text-gray-400 hover:text-white'"
                class="flex-1 py-2.5 text-sm font-semibold rounded-lg transition-colors"
            >
                Create Account
            </button>
        </div>
        
        {{-- Login Form --}}
        <div x-show="activeTab === 'login'" x-transition>
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                
                <div>
                    <label for="mobile-email" class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                    <input 
                        type="email" 
                        id="mobile-email" 
                        name="email" 
                        required 
                        autocomplete="username"
                        class="w-full px-4 py-3 bg-viewlix-gray border border-viewlix-border rounded-xl text-white placeholder-gray-500 focus:ring-viewlix-red focus:border-viewlix-red"
                        placeholder="your@email.com"
                    >
                </div>
                
                <div>
                    <label for="mobile-password" class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                    <input 
                        type="password" 
                        id="mobile-password" 
                        name="password" 
                        required 
                        autocomplete="current-password"
                        class="w-full px-4 py-3 bg-viewlix-gray border border-viewlix-border rounded-xl text-white placeholder-gray-500 focus:ring-viewlix-red focus:border-viewlix-red"
                        placeholder="••••••••"
                    >
                </div>
                
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-viewlix-gray border-viewlix-border text-viewlix-red focus:ring-viewlix-red">
                        <span class="ml-2 text-sm text-gray-400">Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-viewlix-red hover:text-viewlix-red-light">
                        Forgot password?
                    </a>
                </div>
                
                <button type="submit" class="w-full py-3 bg-viewlix-red hover:bg-viewlix-red-dark text-white font-semibold rounded-xl transition-colors">
                    Sign In
                </button>
            </form>
        </div>
        
        {{-- Register Form --}}
        <div x-show="activeTab === 'register'" x-transition>
            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf
                
                <div>
                    <label for="mobile-reg-name" class="block text-sm font-medium text-gray-300 mb-1">Name</label>
                    <input 
                        type="text" 
                        id="mobile-reg-name" 
                        name="name" 
                        required 
                        autocomplete="name"
                        class="w-full px-4 py-3 bg-viewlix-gray border border-viewlix-border rounded-xl text-white placeholder-gray-500 focus:ring-viewlix-red focus:border-viewlix-red"
                        placeholder="Your name"
                    >
                </div>
                
                <div>
                    <label for="mobile-reg-email" class="block text-sm font-medium text-gray-300 mb-1">Email</label>
                    <input 
                        type="email" 
                        id="mobile-reg-email" 
                        name="email" 
                        required 
                        autocomplete="username"
                        class="w-full px-4 py-3 bg-viewlix-gray border border-viewlix-border rounded-xl text-white placeholder-gray-500 focus:ring-viewlix-red focus:border-viewlix-red"
                        placeholder="your@email.com"
                    >
                </div>
                
                <div>
                    <label for="mobile-reg-password" class="block text-sm font-medium text-gray-300 mb-1">Password</label>
                    <input 
                        type="password" 
                        id="mobile-reg-password" 
                        name="password" 
                        required 
                        autocomplete="new-password"
                        class="w-full px-4 py-3 bg-viewlix-gray border border-viewlix-border rounded-xl text-white placeholder-gray-500 focus:ring-viewlix-red focus:border-viewlix-red"
                        placeholder="••••••••"
                    >
                </div>
                
                <div>
                    <label for="mobile-reg-password-confirm" class="block text-sm font-medium text-gray-300 mb-1">Confirm Password</label>
                    <input 
                        type="password" 
                        id="mobile-reg-password-confirm" 
                        name="password_confirmation" 
                        required 
                        autocomplete="new-password"
                        class="w-full px-4 py-3 bg-viewlix-gray border border-viewlix-border rounded-xl text-white placeholder-gray-500 focus:ring-viewlix-red focus:border-viewlix-red"
                        placeholder="••••••••"
                    >
                </div>
                
                <button type="submit" class="w-full py-3 bg-viewlix-red hover:bg-viewlix-red-dark text-white font-semibold rounded-xl transition-colors">
                    Create Account
                </button>
            </form>
        </div>
        
        <p class="text-xs text-gray-500 text-center mt-6 pb-4">
            By continuing, you agree to our Terms of Service and Privacy Policy.
        </p>
    </div>
</div>

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

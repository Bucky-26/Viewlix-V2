{{-- Mobile Floating Bottom Navigation - Only visible on mobile --}}
<nav id="mobile-bottom-nav" 
     class="mobile-nav fixed inset-x-3 sm:inset-x-8 z-50 md:hidden bg-[#0c0d16]/90 backdrop-blur-2xl border border-white/10 rounded-2xl shadow-[0_12px_40px_rgba(0,0,0,0.85)] transition-all duration-300"
     style="bottom: calc(0.75rem + env(safe-area-inset-bottom, 0px));">
    <div class="flex items-center justify-around h-15 px-1 py-1">
        {{-- Home --}}
        <a href="{{ route('home') }}" class="group relative flex flex-col items-center justify-center flex-1 py-1.5 px-0.5 rounded-xl transition-all duration-300 {{ request()->routeIs('home') ? 'text-white bg-white/[0.08] shadow-[inset_0_1px_1px_rgba(255,255,255,0.12)]' : 'text-gray-400 hover:text-gray-200' }}">
            @if(request()->routeIs('home'))
                <span class="absolute -top-1 w-6 h-0.5 bg-gradient-to-r from-viewlix-red to-blue-500 rounded-full shadow-[0_0_8px_rgba(229,9,20,0.8)]"></span>
            @endif
            <div class="p-0.5 transition-transform {{ request()->routeIs('home') ? 'text-viewlix-red scale-105 drop-shadow-[0_0_8px_rgba(229,9,20,0.5)]' : 'group-hover:scale-105' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
            <span class="text-[10px] tracking-tight {{ request()->routeIs('home') ? 'font-bold text-white' : 'font-medium' }}">Home</span>
        </a>
        
        {{-- Movies --}}
        <a href="{{ route('movies') }}" class="group relative flex flex-col items-center justify-center flex-1 py-1.5 px-0.5 rounded-xl transition-all duration-300 {{ request()->routeIs('movies') ? 'text-white bg-white/[0.08] shadow-[inset_0_1px_1px_rgba(255,255,255,0.12)]' : 'text-gray-400 hover:text-gray-200' }}">
            @if(request()->routeIs('movies'))
                <span class="absolute -top-1 w-6 h-0.5 bg-gradient-to-r from-viewlix-red to-blue-500 rounded-full shadow-[0_0_8px_rgba(229,9,20,0.8)]"></span>
            @endif
            <div class="p-0.5 transition-transform {{ request()->routeIs('movies') ? 'text-viewlix-red scale-105 drop-shadow-[0_0_8px_rgba(229,9,20,0.5)]' : 'group-hover:scale-105' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                </svg>
            </div>
            <span class="text-[10px] tracking-tight {{ request()->routeIs('movies') ? 'font-bold text-white' : 'font-medium' }}">Movies</span>
        </a>
        
        {{-- Filipino (Tagalog hub) --}}
        <a href="{{ route('filipino') }}" class="group relative flex flex-col items-center justify-center flex-1 py-1.5 px-0.5 rounded-xl transition-all duration-300 {{ request()->routeIs('filipino') ? 'text-white bg-white/[0.08] shadow-[inset_0_1px_1px_rgba(255,255,255,0.12)]' : 'text-gray-400 hover:text-gray-200' }}">
            @if(request()->routeIs('filipino'))
                <span class="absolute -top-1 w-6 h-0.5 bg-gradient-to-r from-viewlix-red to-blue-500 rounded-full shadow-[0_0_8px_rgba(229,9,20,0.8)]"></span>
            @endif
            <div class="p-0.5 transition-transform {{ request()->routeIs('filipino') ? 'text-viewlix-red scale-105 drop-shadow-[0_0_8px_rgba(229,9,20,0.5)]' : 'group-hover:scale-105' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 5h6v14H3z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h11" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 8h11" />
                </svg>
            </div>
            <span class="text-[10px] tracking-tight {{ request()->routeIs('filipino') ? 'font-bold text-white' : 'font-medium' }}">Filipino</span>
        </a>
        
        {{-- TV Shows --}}
        <a href="{{ route('tv-shows') }}" class="group relative flex flex-col items-center justify-center flex-1 py-1.5 px-0.5 rounded-xl transition-all duration-300 {{ request()->routeIs('tv-shows') ? 'text-white bg-white/[0.08] shadow-[inset_0_1px_1px_rgba(255,255,255,0.12)]' : 'text-gray-400 hover:text-gray-200' }}">
            @if(request()->routeIs('tv-shows'))
                <span class="absolute -top-1 w-6 h-0.5 bg-gradient-to-r from-viewlix-red to-blue-500 rounded-full shadow-[0_0_8px_rgba(229,9,20,0.8)]"></span>
            @endif
            <div class="p-0.5 transition-transform {{ request()->routeIs('tv-shows') ? 'text-viewlix-red scale-105 drop-shadow-[0_0_8px_rgba(229,9,20,0.5)]' : 'group-hover:scale-105' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-[10px] tracking-tight {{ request()->routeIs('tv-shows') ? 'font-bold text-white' : 'font-medium' }}">TV</span>
        </a>
        
        {{-- Anime --}}
        <a href="{{ route('anime') }}" class="group relative flex flex-col items-center justify-center flex-1 py-1.5 px-0.5 rounded-xl transition-all duration-300 {{ request()->routeIs('anime') ? 'text-white bg-white/[0.08] shadow-[inset_0_1px_1px_rgba(255,255,255,0.12)]' : 'text-gray-400 hover:text-gray-200' }}">
            @if(request()->routeIs('anime'))
                <span class="absolute -top-1 w-6 h-0.5 bg-gradient-to-r from-viewlix-red to-blue-500 rounded-full shadow-[0_0_8px_rgba(229,9,20,0.8)]"></span>
            @endif
            <div class="p-0.5 transition-transform {{ request()->routeIs('anime') ? 'text-viewlix-red scale-105 drop-shadow-[0_0_8px_rgba(229,9,20,0.5)]' : 'group-hover:scale-105' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
            </div>
            <span class="text-[10px] tracking-tight {{ request()->routeIs('anime') ? 'font-bold text-white' : 'font-medium' }}">Anime</span>
        </a>
        
        {{-- Account/Login --}}
        @auth
        <button type="button" class="group flex flex-col items-center justify-center flex-1 py-1.5 px-0.5 rounded-xl text-gray-400 hover:text-white transition-all" onclick="openOverlay('#mobile-account-offcanvas', false)">
            <div class="w-5 h-5 rounded-full bg-gradient-to-tr from-viewlix-red to-blue-600 p-[1px] shadow-[0_0_8px_rgba(229,9,20,0.3)] group-hover:scale-105 transition-transform">
                <div class="w-full h-full rounded-full bg-[#0c0d15] flex items-center justify-center text-white text-[10px] font-bold uppercase">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
            </div>
            <span class="text-[10px] tracking-tight font-medium mt-0.5">Account</span>
        </button>
        @else
        <button type="button" class="group flex flex-col items-center justify-center flex-1 py-1.5 px-0.5 rounded-xl text-gray-400 hover:text-white transition-all" onclick="openOverlay('#mobile-auth-offcanvas', false); showAuthView('login')">
            <div class="p-0.5 transition-transform group-hover:scale-105">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <span class="text-[10px] tracking-tight font-medium mt-0.5">Sign In</span>
        </button>
        @endauth
    </div>
</nav>

{{-- Mobile Auth Offcanvas (Login/Register) - Only visible for guests --}}
@guest
<div id="mobile-auth-offcanvas" class="hidden translate-y-full fixed bottom-0 inset-x-0 transition-all duration-300 transform h-auto max-h-[92vh] w-full z-[80] bg-[#0d0e17]/95 backdrop-blur-3xl border-t border-white/10 rounded-t-[2rem] shadow-[0_-25px_60px_rgba(0,0,0,0.9)]" role="dialog" tabindex="-1" aria-labelledby="mobile-auth-offcanvas-label">
    <div class="flex justify-center pt-3 pb-1">
        <span class="w-12 h-1 bg-white/20 hover:bg-white/40 rounded-full cursor-pointer transition-colors" onclick="closeOverlay('#mobile-auth-offcanvas', false)"></span>
    </div>
    
    <div class="p-6 overflow-y-auto max-h-[calc(92vh-3rem)]">
        {{-- Login View --}}
        <div id="auth-login-view" class="auth-view">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center gap-2 mb-3">
                    <img src="{{ asset('logo.png') }}" alt="Logo" class="w-9 h-9 rounded-xl object-contain drop-shadow-[0_0_12px_rgba(229,9,20,0.5)]">
                    <img src="{{ asset('viewlix.png') }}" alt="Viewlix" class="h-6 w-auto object-contain">
                </div>
                <h2 id="mobile-auth-offcanvas-label" class="text-2xl font-black text-white tracking-tight">Welcome Back</h2>
                <p class="text-gray-400 text-xs mt-1">Sign in to sync your favorites and watch history</p>
            </div>

            <!-- Session Status -->
            @if(session('status'))
                <div class="mb-4 p-3 bg-green-500/10 border border-green-500/20 rounded-xl text-green-400 text-xs font-medium">
                    {{ session('status') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-xs font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Google Sign In -->
            <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center gap-3 px-4 py-3 bg-white hover:bg-gray-100 active:scale-[0.98] text-gray-900 font-semibold text-sm rounded-xl transition-all shadow-md mb-5">
                <svg class="w-4 h-4" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Continue with Google
            </a>
            
            <div class="relative mb-5">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-white/10"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-3 bg-[#0d0e17] text-gray-400 font-medium">or continue with email</span>
                </div>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="mobile-login-email" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">{{ __('Email') }}</label>
                    <input id="mobile-login-email" class="block w-full px-4 py-3 rounded-xl bg-white/[0.04] border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-viewlix-red focus:ring-1 focus:ring-viewlix-red transition-all" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="name@domain.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                </div>

                <!-- Password -->
                <div>
                    <label for="mobile-login-password" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">{{ __('Password') }}</label>
                    <input id="mobile-login-password" class="block w-full px-4 py-3 rounded-xl bg-white/[0.04] border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-viewlix-red focus:ring-1 focus:ring-viewlix-red transition-all"
                                type="password"
                                name="password"
                                required autocomplete="current-password" 
                                placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between pt-1">
                    <label for="mobile-remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="mobile-remember_me" type="checkbox" class="w-4 h-4 rounded bg-white/5 border-white/20 text-viewlix-red focus:ring-viewlix-red focus:ring-offset-0" name="remember">
                        <span class="ms-2 text-xs text-gray-400">{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="text-xs text-gray-400 hover:text-white transition-colors" href="{{ route('password.request') }}">
                            {{ __('Forgot password?') }}
                        </a>
                    @endif
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 px-5 rounded-xl bg-gradient-to-r from-viewlix-red via-red-600 to-rose-700 text-white font-bold text-sm shadow-[0_4px_20px_rgba(229,9,20,0.4)] hover:shadow-[0_6px_25px_rgba(229,9,20,0.6)] active:scale-[0.98] transition-all">
                        {{ __('Sign In') }}
                    </button>
                </div>

                <div class="text-center pt-2">
                    <span class="text-xs text-gray-400">Don't have an account?</span>
                    <button type="button" class="text-xs font-semibold text-viewlix-red hover:text-red-400 transition-colors ml-1" onclick="showAuthView('register')">
                        {{ __('Create account') }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Register View --}}
        <div id="auth-register-view" class="auth-view hidden">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center gap-2 mb-3">
                    <img src="{{ asset('logo.png') }}" alt="Logo" class="w-9 h-9 rounded-xl object-contain drop-shadow-[0_0_12px_rgba(229,9,20,0.5)]">
                    <img src="{{ asset('viewlix.png') }}" alt="Viewlix" class="h-6 w-auto object-contain">
                </div>
                <h2 class="text-2xl font-black text-white tracking-tight">Create Account</h2>
                <p class="text-gray-400 text-xs mt-1">Join Viewlix to stream thousands of titles free</p>
            </div>
            
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-xs font-medium">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Google Sign Up -->
            <a href="{{ route('auth.google') }}" class="w-full flex items-center justify-center gap-3 px-4 py-3 bg-white hover:bg-gray-100 active:scale-[0.98] text-gray-900 font-semibold text-sm rounded-xl transition-all shadow-md mb-5">
                <svg class="w-4 h-4" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Continue with Google
            </a>
            
            <div class="relative mb-5">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-white/10"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-3 bg-[#0d0e17] text-gray-400 font-medium">or register with email</span>
                </div>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <!-- Name -->
                <div>
                    <label for="mobile-register-name" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">{{ __('Full Name') }}</label>
                    <input id="mobile-register-name" class="block w-full px-4 py-3 rounded-xl bg-white/[0.04] border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-viewlix-red focus:ring-1 focus:ring-viewlix-red transition-all" type="text" name="name" :value="old('name')" required autocomplete="name" placeholder="John Doe" />
                    <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                </div>

                <!-- Email Address -->
                <div>
                    <label for="mobile-register-email" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">{{ __('Email') }}</label>
                    <input id="mobile-register-email" class="block w-full px-4 py-3 rounded-xl bg-white/[0.04] border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-viewlix-red focus:ring-1 focus:ring-viewlix-red transition-all" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="name@domain.com" />
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                </div>

                <!-- Password -->
                <div>
                    <label for="mobile-register-password" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">{{ __('Password') }}</label>
                    <input id="mobile-register-password" class="block w-full px-4 py-3 rounded-xl bg-white/[0.04] border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-viewlix-red focus:ring-1 focus:ring-viewlix-red transition-all"
                                type="password"
                                name="password"
                                required autocomplete="new-password" 
                                placeholder="Min. 8 characters" />
                    <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="mobile-register-password_confirmation" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-1.5">{{ __('Confirm Password') }}</label>
                    <input id="mobile-register-password_confirmation" class="block w-full px-4 py-3 rounded-xl bg-white/[0.04] border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-viewlix-red focus:ring-1 focus:ring-viewlix-red transition-all"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" 
                                placeholder="••••••••" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 px-5 rounded-xl bg-gradient-to-r from-viewlix-red via-red-600 to-rose-700 text-white font-bold text-sm shadow-[0_4px_20px_rgba(229,9,20,0.4)] hover:shadow-[0_6px_25px_rgba(229,9,20,0.6)] active:scale-[0.98] transition-all">
                        {{ __('Create Account') }}
                    </button>
                </div>

                <div class="text-center pt-2">
                    <span class="text-xs text-gray-400">Already have an account?</span>
                    <button type="button" class="text-xs font-semibold text-viewlix-red hover:text-red-400 transition-colors ml-1" onclick="showAuthView('login')">
                        {{ __('Sign in') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endguest

<script>
    function showAuthView(view) {
        const loginView = document.getElementById('auth-login-view');
        const registerView = document.getElementById('auth-register-view');
        
        if (loginView && registerView) {
            if (view === 'login') {
                loginView.classList.remove('hidden');
                registerView.classList.add('hidden');
            } else {
                loginView.classList.add('hidden');
                registerView.classList.remove('hidden');
            }
        }
    }
</script>

{{-- Mobile Account Offcanvas (for authenticated users) --}}
@auth
<div id="mobile-account-offcanvas" class="hidden translate-y-full fixed bottom-0 inset-x-0 transition-all duration-300 transform h-auto max-h-[85vh] w-full z-[80] bg-[#0d0e17]/95 backdrop-blur-3xl border-t border-white/10 rounded-t-[2rem] shadow-[0_-25px_60px_rgba(0,0,0,0.9)]" role="dialog" tabindex="-1" aria-labelledby="mobile-account-offcanvas-label">
    <div class="flex justify-center pt-3 pb-1">
        <span class="w-12 h-1 bg-white/20 hover:bg-white/40 rounded-full cursor-pointer transition-colors" onclick="closeOverlay('#mobile-account-offcanvas', false)"></span>
    </div>
    
    <div class="p-6 overflow-y-auto">
        {{-- User Info --}}
        <div class="flex items-center gap-4 mb-6 pb-5 border-b border-white/10">
            @if(Auth::user()->avatar)
                <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="w-14 h-14 rounded-2xl object-cover ring-2 ring-viewlix-red/40 ring-offset-2 ring-offset-[#0d0e17]">
            @else
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-viewlix-red to-blue-600 flex items-center justify-center text-white text-xl font-black uppercase shadow-[0_0_15px_rgba(229,9,20,0.4)]">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
            @endif
            <div class="min-w-0 flex-1">
                <h3 id="mobile-account-offcanvas-label" class="text-lg font-bold text-white truncate">{{ Auth::user()->name }}</h3>
                <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                @if(Auth::user()->google_id)
                    <span class="inline-flex items-center gap-1 mt-1 text-[11px] font-medium text-emerald-400">
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
            <a href="{{ route('admin.gate.enter') }}" class="flex items-center gap-3.5 p-3.5 rounded-xl bg-gradient-to-r from-viewlix-red/20 to-blue-600/20 border border-viewlix-red/30 hover:border-viewlix-red/60 transition-all group">
                <div class="w-9 h-9 rounded-lg bg-viewlix-red/20 text-viewlix-red flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="text-sm font-bold text-white">Admin Panel</div>
                    <div class="text-[11px] text-red-300/70">Management console & security</div>
                </div>
                <svg class="w-4 h-4 text-gray-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            @endif
            
            <a href="{{ route('profile.show') }}" class="flex items-center gap-3.5 p-3.5 rounded-xl bg-white/[0.03] border border-white/5 hover:bg-white/[0.08] hover:border-white/15 transition-all group">
                <div class="w-9 h-9 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-white flex-1">My Profile</span>
                <svg class="w-4 h-4 text-gray-500 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>

            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3.5 p-3.5 rounded-xl bg-white/[0.03] border border-white/5 hover:bg-white/[0.08] hover:border-white/15 transition-all group">
                <div class="w-9 h-9 rounded-lg bg-purple-500/10 text-purple-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-white flex-1">Settings & Avatar</span>
                <svg class="w-4 h-4 text-gray-500 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            
            <a href="{{ route('favorites') }}" class="flex items-center gap-3.5 p-3.5 rounded-xl bg-white/[0.03] border border-white/5 hover:bg-white/[0.08] hover:border-white/15 transition-all group">
                <div class="w-9 h-9 rounded-lg bg-rose-500/10 text-rose-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-white flex-1">My Favorites</span>
                <svg class="w-4 h-4 text-gray-500 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            
            <a href="{{ route('watch-history') }}" class="flex items-center gap-3.5 p-3.5 rounded-xl bg-white/[0.03] border border-white/5 hover:bg-white/[0.08] hover:border-white/15 transition-all group">
                <div class="w-9 h-9 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-white flex-1">Watch History</span>
                <svg class="w-4 h-4 text-gray-500 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3.5 p-3.5 rounded-xl bg-white/[0.03] border border-white/5 hover:bg-white/[0.08] hover:border-white/15 transition-all group">
                <div class="w-9 h-9 rounded-lg bg-gray-500/10 text-gray-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-white flex-1">Preferences</span>
                <svg class="w-4 h-4 text-gray-500 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        
        {{-- Logout --}}
        <div class="mt-6 pt-5 border-t border-white/10">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center justify-center gap-2.5 w-full py-3 rounded-xl bg-red-500/10 hover:bg-red-500/20 border border-red-500/20 text-red-400 font-semibold text-sm transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </div>
</div>
@endauth


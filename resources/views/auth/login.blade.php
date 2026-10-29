<x-guest-layout>
    <div class="w-full max-w-5xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <!-- Left Column: Cinematic Showcase (Visible on Large Screens) -->
            <div class="hidden lg:flex lg:col-span-6 flex-col justify-between h-full p-8 lg:p-10 rounded-3xl bg-gradient-to-b from-white/[0.05] to-white/[0.01] border border-white/10 backdrop-blur-xl relative overflow-hidden shadow-2xl">
                <!-- Ambient corner glow inside card -->
                <div class="absolute -top-20 -left-20 w-60 h-60 bg-viewlix-red/20 blur-3xl rounded-full pointer-events-none"></div>
                <div class="absolute -bottom-20 -right-20 w-60 h-60 bg-indigo-500/20 blur-3xl rounded-full pointer-events-none"></div>
                
                <div class="relative z-10">
                    <!-- Brand Logo Showcase -->
                    <div class="flex items-center gap-3 mb-6">
                        <img src="{{ asset('logo.png') }}" alt="Viewlix Icon" class="w-11 h-11 object-contain drop-shadow-[0_0_16px_rgba(0,74,173,0.5)]">
                        <img src="{{ asset('viewlix.png') }}" alt="Viewlix" class="h-8 w-auto object-contain">
                    </div>

                    <!-- Premium Badge -->
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-xs font-semibold text-blue-400 mb-6 backdrop-blur-sm">
                        <span class="flex h-2 w-2 rounded-full bg-blue-500 animate-pulse"></span>
                        Next-Gen Streaming Experience
                    </div>

                    <h1 class="text-3xl xl:text-4xl font-extrabold text-white tracking-tight leading-tight mb-4">
                        Stream Without Limits. <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-sky-300 to-indigo-400">
                            Anytime. Anywhere.
                        </span>
                    </h1>

                    <p class="text-gray-400 text-sm leading-relaxed mb-8">
                        Sign in to resume watching your favorite movies, TV series, and trending anime with seamless cross-device progress.
                    </p>

                    <!-- Feature Pillars Grid -->
                    <div class="grid grid-cols-2 gap-3 mb-8">
                        <div class="p-3.5 rounded-2xl bg-white/[0.03] border border-white/5 hover:border-white/10 transition-colors">
                            <div class="w-8 h-8 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center mb-2.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <h4 class="text-white text-xs font-bold mb-0.5">Ultra HD Quality</h4>
                            <p class="text-gray-400 text-[11px] leading-tight">Crystal clear 4K & HDR streaming.</p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-white/[0.03] border border-white/5 hover:border-white/10 transition-colors">
                            <div class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center mb-2.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h4 class="text-white text-xs font-bold mb-0.5">Watch History</h4>
                            <p class="text-gray-400 text-[11px] leading-tight">Pick up right where you left off.</p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-white/[0.03] border border-white/5 hover:border-white/10 transition-colors">
                            <div class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center mb-2.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                </svg>
                            </div>
                            <h4 class="text-white text-xs font-bold mb-0.5">Cloud Watchlist</h4>
                            <p class="text-gray-400 text-[11px] leading-tight">Save titles for later with 1 click.</p>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-white/[0.03] border border-white/5 hover:border-white/10 transition-colors">
                            <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center mb-2.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                            </div>
                            <h4 class="text-white text-xs font-bold mb-0.5">100% Free</h4>
                            <p class="text-gray-400 text-[11px] leading-tight">No subscription fees or paywalls.</p>
                        </div>
                    </div>
                </div>

                <!-- Bottom Status -->
                <div class="relative z-10 pt-4 border-t border-white/5 flex items-center justify-between text-xs text-gray-400">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        Over 25,000+ Movies & Shows Ready
                    </span>
                    <span class="text-gray-500">v2.0 Viewlix.site</span>
                </div>
            </div>

            <!-- Right Column: Glassmorphism Authentication Card -->
            <div class="col-span-1 lg:col-span-6 w-full max-w-md mx-auto">
                <div class="p-6 sm:p-8 rounded-3xl bg-[#101015]/90 border border-white/10 backdrop-blur-2xl shadow-2xl shadow-black/80 relative">
                    
                    <!-- Mobile Brand Logo (Small Screens) -->
                    <div class="flex items-center justify-center gap-2.5 mb-5 lg:hidden">
                        <img src="{{ asset('logo.png') }}" alt="Viewlix Icon" class="w-8 h-8 object-contain">
                        <img src="{{ asset('viewlix.png') }}" alt="Viewlix" class="h-6 w-auto object-contain">
                    </div>

                    <!-- Segmented Dual Tab Switcher -->
                    <div class="flex p-1 bg-white/5 rounded-2xl border border-white/5 mb-6">
                        <a href="{{ route('login') }}" class="flex-1 py-2.5 text-center text-xs sm:text-sm font-semibold rounded-xl bg-gradient-to-r from-viewlix-red to-blue-600 text-white shadow-md shadow-viewlix-red/20 transition-all">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="flex-1 py-2.5 text-center text-xs sm:text-sm font-medium rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition-all">
                            Create Account
                        </a>
                    </div>

                    <!-- Welcome Header -->
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-white tracking-tight">Welcome Back</h2>
                        <p class="text-gray-400 text-xs sm:text-sm mt-1">Sign in to your Viewlix account</p>
                    </div>

                    <!-- Session Status Banner -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />
                    
                    <!-- Error Flash Alert -->
                    @if(session('error'))
                        <div class="mb-4 p-3.5 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs flex items-center gap-2.5 animate-fadeIn">
                            <svg class="w-4 h-4 flex-shrink-0 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    <!-- Google SSO Button -->
                    <a href="{{ route('auth.google') }}" class="group w-full flex items-center justify-center gap-3 px-4 py-3 bg-white hover:bg-gray-50 text-gray-800 text-sm font-semibold rounded-xl shadow-sm hover:shadow-md hover:scale-[1.01] active:scale-[0.99] transition-all duration-200 mb-6">
                        <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                        <span>Continue with Google</span>
                    </a>

                    <!-- Subtle Modern Divider -->
                    <div class="relative flex items-center justify-center mb-6">
                        <div class="w-full border-t border-white/10"></div>
                        <span class="absolute px-3 bg-[#101015] text-[11px] uppercase tracking-wider text-gray-500 font-medium">
                            or email sign in
                        </span>
                    </div>

                    <!-- Sign In Form -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-4" x-data="{ showPassword: false }">
                        @csrf

                        <!-- Email Address Input -->
                        <div>
                            <label for="email" class="block text-xs font-medium text-gray-300 mb-1.5">Email Address</label>
                            <div class="relative rounded-xl shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                    </svg>
                                </div>
                                <input id="email" 
                                       type="email" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autofocus 
                                       autocomplete="username" 
                                       inputmode="email"
                                       placeholder="name@example.com"
                                       class="w-full pl-10 pr-4 py-3 bg-white/[0.04] border border-white/10 hover:border-white/20 focus:border-blue-500 focus:bg-[#15151c] focus:ring-2 focus:ring-blue-500/20 text-white placeholder-gray-500 text-sm rounded-xl transition-all duration-200 outline-none @error('email') border-red-500/50 focus:border-red-500 focus:ring-red-500/20 @enderror">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                        </div>

                        <!-- Password Input with Interactive Show/Hide Toggle -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="password" class="block text-xs font-medium text-gray-300">Password</label>
                                @if (Route::has('password.request'))
                                    <a class="text-xs text-blue-400 hover:text-blue-300 transition-colors font-medium" href="{{ route('password.request') }}">
                                        Forgot password?
                                    </a>
                                @endif
                            </div>
                            <div class="relative rounded-xl shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input :type="showPassword ? 'text' : 'password'"
                                       id="password" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password"
                                       placeholder="••••••••"
                                       class="w-full pl-10 pr-11 py-3 bg-white/[0.04] border border-white/10 hover:border-white/20 focus:border-blue-500 focus:bg-[#15151c] focus:ring-2 focus:ring-blue-500/20 text-white placeholder-gray-500 text-sm rounded-xl transition-all duration-200 outline-none @error('password') border-red-500/50 focus:border-red-500 focus:ring-red-500/20 @enderror">
                                
                                <!-- Show / Hide Password Eye Toggle Button -->
                                <button type="button" 
                                        @click="showPassword = !showPassword" 
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-200 focus:outline-none transition-colors"
                                        :aria-label="showPassword ? 'Hide password' : 'Show password'">
                                    <!-- Eye Icon (when hidden) -->
                                    <svg x-show="!showPassword" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <!-- Eye Off Icon (when shown) -->
                                    <svg x-show="showPassword" x-cloak class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
                        </div>

                        <!-- Remember Me Checkbox -->
                        <div class="flex items-center pt-1">
                            <label for="remember_me" class="inline-flex items-center cursor-pointer select-none group">
                                <div class="relative flex items-center">
                                    <input id="remember_me" 
                                           type="checkbox" 
                                           name="remember"
                                           class="w-4 h-4 rounded bg-white/5 border border-white/20 text-viewlix-red focus:ring-viewlix-red/30 focus:ring-offset-0 focus:ring-2 cursor-pointer transition-all">
                                </div>
                                <span class="ms-2.5 text-xs text-gray-400 group-hover:text-gray-300 transition-colors">Remember my session on this device</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit" 
                                    class="group w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-viewlix-red via-blue-600 to-indigo-600 hover:from-blue-600 hover:to-viewlix-red text-white text-sm font-semibold shadow-lg shadow-viewlix-red/25 hover:shadow-viewlix-red/40 active:scale-[0.99] transition-all duration-200 flex items-center justify-center gap-2">
                                <span>Sign In to Viewlix</span>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </div>

                        <!-- Bottom Switch Prompt -->
                        <div class="text-center pt-2">
                            <span class="text-xs text-gray-400">New to Viewlix?</span>
                            <a href="{{ route('register') }}" class="text-xs text-blue-400 hover:text-blue-300 font-semibold transition-colors ml-1">
                                Create an account for free
                            </a>
                        </div>
                    </form>

                    <!-- Trust Badge -->
                    <div class="mt-6 pt-4 border-t border-white/5 flex items-center justify-center gap-2 text-[11px] text-gray-500">
                        <svg class="w-3.5 h-3.5 text-emerald-500/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>256-Bit Encrypted &amp; Secure Connection</span>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-guest-layout>

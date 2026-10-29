<x-guest-layout>
    <div class="w-full max-w-5xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <!-- Left Column: Cinematic Showcase (Visible on Large Screens) -->
            <div class="hidden lg:flex lg:col-span-6 flex-col justify-between h-full p-8 lg:p-10 rounded-3xl bg-gradient-to-b from-white/[0.05] to-white/[0.01] border border-white/10 backdrop-blur-xl relative overflow-hidden shadow-2xl">
                <!-- Ambient corner glow inside card -->
                <div class="absolute -top-20 -left-20 w-60 h-60 bg-blue-600/20 blur-3xl rounded-full pointer-events-none"></div>
                <div class="absolute -bottom-20 -right-20 w-60 h-60 bg-viewlix-red/20 blur-3xl rounded-full pointer-events-none"></div>
                
                <div class="relative z-10">
                    <!-- Brand Logo Showcase -->
                    <div class="flex items-center gap-3 mb-6">
                        <img src="{{ asset('logo.png') }}" alt="Viewlix Icon" class="w-11 h-11 object-contain drop-shadow-[0_0_16px_rgba(0,74,173,0.5)]">
                        <img src="{{ asset('viewlix.png') }}" alt="Viewlix" class="h-8 w-auto object-contain">
                    </div>

                    <!-- Brand Badge -->
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-xs font-semibold text-sky-400 mb-6 backdrop-blur-sm">
                        <span class="flex h-2 w-2 rounded-full bg-sky-400 animate-ping"></span>
                        Start Watching In Seconds
                    </div>

                    <h1 class="text-3xl xl:text-4xl font-extrabold text-white tracking-tight leading-tight mb-4">
                        Join Millions of <br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-sky-300 to-indigo-400">
                            Entertainment Fans.
                        </span>
                    </h1>

                    <p class="text-gray-400 text-sm leading-relaxed mb-8">
                        Create your free Viewlix account now to unlock personalized recommendations, custom watchlists, and synchronized watch progress across all your devices.
                    </p>

                    <!-- Feature List -->
                    <div class="space-y-3.5 mb-8">
                        <div class="flex items-start gap-3 p-3 rounded-2xl bg-white/[0.02] border border-white/5">
                            <div class="w-7 h-7 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-white text-xs font-bold">100% Free Forever</h4>
                                <p class="text-gray-400 text-[11px]">No credit card or monthly subscription required.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3 rounded-2xl bg-white/[0.02] border border-white/5">
                            <div class="w-7 h-7 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-white text-xs font-bold">Anime &amp; Series Library</h4>
                                <p class="text-gray-400 text-[11px]">Simulcasts, full seasons, and blockbuster movies.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-3 rounded-2xl bg-white/[0.02] border border-white/5">
                            <div class="w-7 h-7 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-white text-xs font-bold">PWA &amp; Mobile Ready</h4>
                                <p class="text-gray-400 text-[11px]">Install on Android or iOS for a native app feel.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Status -->
                <div class="relative z-10 pt-4 border-t border-white/5 flex items-center justify-between text-xs text-gray-400">
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                        Instant Activation Guaranteed
                    </span>
                    <span class="text-gray-500">Viewlix.site</span>
                </div>
            </div>

            <!-- Right Column: Registration Form Card -->
            <div class="col-span-1 lg:col-span-6 w-full max-w-md mx-auto"
                 x-data="{
                     name: '{{ old('name') }}',
                     email: '{{ old('email') }}',
                     password: '',
                     password_confirmation: '',
                     showPassword: false,
                     showConfirmPassword: false,
                     get strengthScore() {
                         if (!this.password) return 0;
                         let score = 0;
                         if (this.password.length >= 8) score++;
                         if (/[a-z]/.test(this.password) && /[A-Z]/.test(this.password)) score++;
                         if (/[0-9]/.test(this.password)) score++;
                         if (/[^a-zA-Z0-9]/.test(this.password)) score++;
                         return Math.min(4, Math.max(1, score));
                     },
                     get strengthLabel() {
                         switch(this.strengthScore) {
                             case 1: return 'Weak';
                             case 2: return 'Fair';
                             case 3: return 'Good';
                             case 4: return 'Strong';
                             default: return '';
                         }
                     },
                     get passwordsMatch() {
                         return this.password && this.password_confirmation && this.password === this.password_confirmation;
                     },
                     get passwordsMismatch() {
                         return this.password && this.password_confirmation && this.password !== this.password_confirmation;
                     }
                 }">
                
                <div class="p-6 sm:p-8 rounded-3xl bg-[#101015]/90 border border-white/10 backdrop-blur-2xl shadow-2xl shadow-black/80 relative">
                    
                    <!-- Mobile Brand Logo (Small Screens) -->
                    <div class="flex items-center justify-center gap-2.5 mb-5 lg:hidden">
                        <img src="{{ asset('logo.png') }}" alt="Viewlix Icon" class="w-8 h-8 object-contain">
                        <img src="{{ asset('viewlix.png') }}" alt="Viewlix" class="h-6 w-auto object-contain">
                    </div>

                    <!-- Segmented Dual Tab Switcher -->
                    <div class="flex p-1 bg-white/5 rounded-2xl border border-white/5 mb-6">
                        <a href="{{ route('login') }}" class="flex-1 py-2.5 text-center text-xs sm:text-sm font-medium rounded-xl text-gray-400 hover:text-white hover:bg-white/5 transition-all">
                            Sign In
                        </a>
                        <a href="{{ route('register') }}" class="flex-1 py-2.5 text-center text-xs sm:text-sm font-semibold rounded-xl bg-gradient-to-r from-viewlix-red to-blue-600 text-white shadow-md shadow-viewlix-red/20 transition-all">
                            Create Account
                        </a>
                    </div>

                    <!-- Welcome Header -->
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-white tracking-tight">Create Account</h2>
                        <p class="text-gray-400 text-xs sm:text-sm mt-1">Start watching free in less than a minute</p>
                    </div>

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
                        <span>Sign up with Google</span>
                    </a>

                    <!-- Subtle Modern Divider -->
                    <div class="relative flex items-center justify-center mb-6">
                        <div class="w-full border-t border-white/10"></div>
                        <span class="absolute px-3 bg-[#101015] text-[11px] uppercase tracking-wider text-gray-500 font-medium">
                            or register with email
                        </span>
                    </div>

                    <!-- Registration Form -->
                    <form method="POST" action="{{ route('register') }}" class="space-y-4">
                        @csrf

                        <!-- Full Name Input -->
                        <div>
                            <label for="name" class="block text-xs font-medium text-gray-300 mb-1.5">Full Name</label>
                            <div class="relative rounded-xl shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <input id="name" 
                                       type="text" 
                                       name="name" 
                                       x-model="name"
                                       required 
                                       autofocus 
                                       autocomplete="name" 
                                       placeholder="John Doe"
                                       class="w-full pl-10 pr-4 py-3 bg-white/[0.04] border border-white/10 hover:border-white/20 focus:border-blue-500 focus:bg-[#15151c] focus:ring-2 focus:ring-blue-500/20 text-white placeholder-gray-500 text-sm rounded-xl transition-all duration-200 outline-none @error('name') border-red-500/50 focus:border-red-500 focus:ring-red-500/20 @enderror">
                            </div>
                            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                        </div>

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
                                       x-model="email"
                                       required 
                                       autocomplete="username" 
                                       inputmode="email"
                                       placeholder="name@example.com"
                                       class="w-full pl-10 pr-4 py-3 bg-white/[0.04] border border-white/10 hover:border-white/20 focus:border-blue-500 focus:bg-[#15151c] focus:ring-2 focus:ring-blue-500/20 text-white placeholder-gray-500 text-sm rounded-xl transition-all duration-200 outline-none @error('email') border-red-500/50 focus:border-red-500 focus:ring-red-500/20 @enderror">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                        </div>

                        <!-- Password Input with Strength Meter & Show/Hide Toggle -->
                        <div>
                            <label for="password" class="block text-xs font-medium text-gray-300 mb-1.5">Create Password</label>
                            <div class="relative rounded-xl shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <input :type="showPassword ? 'text' : 'password'"
                                       id="password" 
                                       name="password" 
                                       x-model="password"
                                       required 
                                       autocomplete="new-password"
                                       placeholder="Minimum 8 characters"
                                       class="w-full pl-10 pr-11 py-3 bg-white/[0.04] border border-white/10 hover:border-white/20 focus:border-blue-500 focus:bg-[#15151c] focus:ring-2 focus:ring-blue-500/20 text-white placeholder-gray-500 text-sm rounded-xl transition-all duration-200 outline-none @error('password') border-red-500/50 focus:border-red-500 focus:ring-red-500/20 @enderror">
                                
                                <button type="button" 
                                        @click="showPassword = !showPassword" 
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-200 focus:outline-none transition-colors"
                                        :aria-label="showPassword ? 'Hide password' : 'Show password'">
                                    <svg x-show="!showPassword" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="showPassword" x-cloak class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />

                            <!-- Live Password Strength Meter -->
                            <div x-show="password.length > 0" x-cloak class="mt-2.5 space-y-1.5">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="text-gray-400">Security strength:</span>
                                    <span class="font-semibold"
                                          :class="{
                                              'text-red-400': strengthScore === 1,
                                              'text-amber-400': strengthScore === 2,
                                              'text-sky-400': strengthScore === 3,
                                              'text-emerald-400': strengthScore === 4
                                          }"
                                          x-text="strengthLabel"></span>
                                </div>
                                <div class="grid grid-cols-4 gap-1.5 h-1.5 w-full">
                                    <div class="rounded-full transition-colors duration-300"
                                         :class="strengthScore >= 1 ? (strengthScore === 1 ? 'bg-red-500' : (strengthScore === 2 ? 'bg-amber-500' : (strengthScore === 3 ? 'bg-sky-500' : 'bg-emerald-500'))) : 'bg-white/10'"></div>
                                    <div class="rounded-full transition-colors duration-300"
                                         :class="strengthScore >= 2 ? (strengthScore === 2 ? 'bg-amber-500' : (strengthScore === 3 ? 'bg-sky-500' : 'bg-emerald-500')) : 'bg-white/10'"></div>
                                    <div class="rounded-full transition-colors duration-300"
                                         :class="strengthScore >= 3 ? (strengthScore === 3 ? 'bg-sky-500' : 'bg-emerald-500') : 'bg-white/10'"></div>
                                    <div class="rounded-full transition-colors duration-300"
                                         :class="strengthScore >= 4 ? 'bg-emerald-500' : 'bg-white/10'"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Confirm Password Input -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="password_confirmation" class="block text-xs font-medium text-gray-300">Confirm Password</label>
                                <span x-show="passwordsMatch" x-cloak class="text-[11px] text-emerald-400 font-medium inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Matches
                                </span>
                                <span x-show="passwordsMismatch" x-cloak class="text-[11px] text-red-400 font-medium inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Does not match
                                </span>
                            </div>
                            <div class="relative rounded-xl shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <input :type="showConfirmPassword ? 'text' : 'password'"
                                       id="password_confirmation" 
                                       name="password_confirmation" 
                                       x-model="password_confirmation"
                                       required 
                                       autocomplete="new-password"
                                       placeholder="Re-enter password"
                                       class="w-full pl-10 pr-11 py-3 bg-white/[0.04] border border-white/10 hover:border-white/20 focus:border-blue-500 focus:bg-[#15151c] focus:ring-2 focus:ring-blue-500/20 text-white placeholder-gray-500 text-sm rounded-xl transition-all duration-200 outline-none @error('password_confirmation') border-red-500/50 focus:border-red-500 focus:ring-red-500/20 @enderror"
                                       :class="{'border-emerald-500/50': passwordsMatch, 'border-red-500/50': passwordsMismatch}">
                                
                                <button type="button" 
                                        @click="showConfirmPassword = !showConfirmPassword" 
                                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-200 focus:outline-none transition-colors"
                                        :aria-label="showConfirmPassword ? 'Hide password' : 'Show password'">
                                    <svg x-show="!showConfirmPassword" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg x-show="showConfirmPassword" x-cloak class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
                        </div>

                        <!-- Terms Disclaimer -->
                        <p class="text-[11px] text-gray-500 leading-relaxed pt-1">
                            By signing up, you agree to Viewlix's <a href="https://viewlix.site/terms" class="text-gray-400 hover:text-white underline">Terms</a> and acknowledge our <a href="https://viewlix.site/privacy-policy" class="text-gray-400 hover:text-white underline">Privacy Policy</a>.
                        </p>

                        <!-- Submit Button -->
                        <div class="pt-2">
                            <button type="submit" 
                                    class="group w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-viewlix-red via-blue-600 to-indigo-600 hover:from-blue-600 hover:to-viewlix-red text-white text-sm font-semibold shadow-lg shadow-viewlix-red/25 hover:shadow-viewlix-red/40 active:scale-[0.99] transition-all duration-200 flex items-center justify-center gap-2">
                                <span>Create Your Free Account</span>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </div>

                        <!-- Bottom Prompt -->
                        <div class="text-center pt-2">
                            <span class="text-xs text-gray-400">Already registered?</span>
                            <a href="{{ route('login') }}" class="text-xs text-blue-400 hover:text-blue-300 font-semibold transition-colors ml-1">
                                Sign in to your account
                            </a>
                        </div>
                    </form>

                    <!-- Trust Badge -->
                    <div class="mt-6 pt-4 border-t border-white/5 flex items-center justify-center gap-2 text-[11px] text-gray-500">
                        <svg class="w-3.5 h-3.5 text-emerald-500/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>Protected by 256-Bit SSL Encryption</span>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-guest-layout>

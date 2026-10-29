<x-guest-layout>
    <div class="w-full max-w-lg mx-auto">
        <!-- Main Authorization Glassmorphism Card -->
        <div class="p-6 sm:p-8 rounded-3xl bg-[#101015]/90 border border-white/10 backdrop-blur-2xl shadow-2xl shadow-black/80 relative overflow-hidden">
            
            <!-- Ambient Glow Accents -->
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-blue-600/20 blur-3xl rounded-full pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-indigo-500/20 blur-3xl rounded-full pointer-events-none"></div>

            <div class="relative z-10">
                <!-- Header / Brand & Badge -->
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center p-3 rounded-2xl bg-blue-500/10 border border-blue-500/20 mb-4 shadow-inner">
                        <img src="{{ asset('logo.png') }}" alt="Viewlix Icon" class="w-10 h-10 object-contain drop-shadow-[0_0_12px_rgba(0,74,173,0.5)]">
                    </div>
                    
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-xs font-semibold text-blue-400 mb-3">
                        <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                        Proxy Service Authorization
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Authorize Viewlix Proxy</h1>
                    <p class="text-gray-400 text-xs sm:text-sm mt-2 max-w-sm mx-auto leading-relaxed">
                        Sign in with an administrator account to generate an authentication token and authorize the proxy service.
                    </p>
                </div>

                <!-- Redirect Target Info Notice -->
                @if(!empty($redirect_uri))
                    <div class="mb-5 p-3 rounded-xl bg-white/[0.03] border border-white/10 flex items-start gap-2.5 text-xs text-gray-300">
                        <svg class="w-4 h-4 text-blue-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div class="min-w-0 flex-1">
                            <span class="text-gray-400 block text-[11px] font-medium uppercase tracking-wider">Redirect Target</span>
                            <span class="font-mono text-gray-200 text-xs break-all">{{ $redirect_uri }}</span>
                        </div>
                    </div>
                @endif

                <!-- Validation Errors Alert -->
                @if($errors->any())
                    <div class="mb-5 p-3.5 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs flex items-center gap-2.5">
                        <svg class="w-4 h-4 flex-shrink-0 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span class="font-medium">{{ $errors->first() }}</span>
                    </div>
                @endif

                <!-- Authorization Form -->
                <form method="POST" action="{{ url('/api/v3/auth/login') }}" class="space-y-4" x-data="{ showPassword: false }">
                    @csrf

                    <!-- Pass the redirect_uri through the form -->
                    <input type="hidden" name="redirect_uri" value="{{ $redirect_uri }}">

                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">Admin Email</label>
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
                                   autocomplete="email" 
                                   placeholder="admin@viewlix.site"
                                   class="w-full pl-10 pr-4 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all">
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="block text-xs font-semibold text-gray-300 mb-1.5 uppercase tracking-wider">Password</label>
                        <div class="relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-500">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input id="password" 
                                   :type="showPassword ? 'text' : 'password'" 
                                   name="password" 
                                   required 
                                   autocomplete="current-password" 
                                   placeholder="••••••••••••"
                                   class="w-full pl-10 pr-11 py-3 rounded-xl bg-white/5 border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all">
                            <button type="button" 
                                    @click="showPassword = !showPassword" 
                                    class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-500 hover:text-gray-300 transition-colors focus:outline-none"
                                    tabindex="-1"
                                    aria-label="Toggle password visibility">
                                <svg x-show="!showPassword" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Action Button -->
                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-500 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold text-sm shadow-lg shadow-blue-600/30 hover:shadow-blue-600/50 hover:scale-[1.01] active:scale-[0.99] transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span>Sign In &amp; Authorize</span>
                        </button>
                    </div>
                </form>

                <!-- Footer Links inside Card -->
                <div class="mt-6 pt-4 border-t border-white/5 flex items-center justify-between text-xs text-gray-500">
                    <a href="{{ url('/') }}" class="hover:text-gray-300 transition-colors flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Cancel</span>
                    </a>
                    <span>Protected by Laravel Sanctum</span>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>

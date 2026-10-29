<x-guest-layout>
    <div class="w-full max-w-md mx-auto">
        <div class="p-6 sm:p-8 rounded-3xl bg-[#101015]/90 border border-white/10 backdrop-blur-2xl shadow-2xl shadow-black/80 relative">
            
            <div class="text-center mb-6">
                <!-- Brand Logo -->
                <div class="flex items-center justify-center gap-2.5 mb-5">
                    <img src="{{ asset('logo.png') }}" alt="Viewlix Icon" class="w-9 h-9 object-contain drop-shadow-[0_0_12px_rgba(0,74,173,0.4)]">
                    <img src="{{ asset('viewlix.png') }}" alt="Viewlix" class="h-7 w-auto object-contain">
                </div>
                
                <h2 class="text-2xl font-bold text-white tracking-tight">Forgot Password?</h2>
                <p class="text-gray-400 text-xs sm:text-sm mt-1.5 leading-relaxed">
                    No problem! Enter your registered email address and we'll send you a password reset link.
                </p>
            </div>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                @csrf

                <!-- Email Address -->
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
                               placeholder="your@email.com"
                               class="w-full pl-10 pr-4 py-3 bg-white/[0.04] border border-white/10 hover:border-white/20 focus:border-blue-500 focus:bg-[#15151c] focus:ring-2 focus:ring-blue-500/20 text-white placeholder-gray-500 text-sm rounded-xl transition-all duration-200 outline-none @error('email') border-red-500/50 focus:border-red-500 focus:ring-red-500/20 @enderror">
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
                </div>

                <div class="pt-2">
                    <button type="submit" 
                            class="group w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-viewlix-red via-blue-600 to-indigo-600 hover:from-blue-600 hover:to-viewlix-red text-white text-sm font-semibold shadow-lg shadow-viewlix-red/25 hover:shadow-viewlix-red/40 active:scale-[0.99] transition-all duration-200 flex items-center justify-center gap-2">
                        <span>Send Password Reset Link</span>
                        <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>

                <div class="text-center pt-2">
                    <a class="text-xs text-gray-400 hover:text-white transition-colors inline-flex items-center gap-1.5" href="{{ route('login') }}">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Back to sign in</span>
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>

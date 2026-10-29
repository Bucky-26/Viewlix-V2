<section class="w-full">
    <header class="pb-4 border-b border-white/5">
        <h2 class="text-lg font-bold text-white">
            {{ __('Account Details') }}
        </h2>
        <p class="mt-1 text-xs text-gray-400">
            {{ __("Manage your display name and email address. Changing email will require re-verification.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')
        <input type="hidden" name="redirect_to" value="edit">

        {{-- Name Field --}}
        <div>
            <label for="profile_name" class="block text-xs font-semibold uppercase tracking-wider text-gray-300 mb-2">
                {{ __('Full Name / Display Name') }}
            </label>
            <div class="relative">
                <input id="profile_name" 
                       name="name" 
                       type="text" 
                       class="block w-full px-4 py-3 rounded-xl bg-white/[0.04] border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-viewlix-red focus:ring-1 focus:ring-viewlix-red transition-all" 
                       value="{{ old('name', $user->name) }}" 
                       required 
                       autocomplete="name" />
            </div>
            <x-input-error class="mt-2 text-xs text-rose-400" :messages="$errors->get('name')" />
        </div>

        {{-- Email Field --}}
        <div>
            <label for="profile_email" class="block text-xs font-semibold uppercase tracking-wider text-gray-300 mb-2">
                {{ __('Email Address') }}
            </label>
            <div class="relative">
                <input id="profile_email" 
                       name="email" 
                       type="email" 
                       class="block w-full px-4 py-3 rounded-xl bg-white/[0.04] border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-viewlix-red focus:ring-1 focus:ring-viewlix-red transition-all" 
                       value="{{ old('email', $user->email) }}" 
                       required 
                       autocomplete="username" />
            </div>
            <x-input-error class="mt-2 text-xs text-rose-400" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-300 text-xs">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-amber-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Your email address is unverified.
                        </span>
                        <button form="send-verification" class="text-xs font-bold text-amber-400 hover:text-amber-300 underline underline-offset-2">
                            Resend Verification Link
                        </button>
                    </div>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-emerald-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center justify-between pt-2">
            <button type="submit" 
                    class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-viewlix-red to-blue-600 hover:from-blue-600 hover:to-indigo-600 text-white text-xs font-bold shadow-lg shadow-blue-500/20 hover:shadow-blue-500/40 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                </svg>
                {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2500)"
                   class="text-xs font-semibold text-emerald-400 flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    {{ __('Saved successfully.') }}
                </p>
            @endif
        </div>
    </form>
</section>

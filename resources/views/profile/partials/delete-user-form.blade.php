<section class="w-full">
    <header class="pb-4 border-b border-rose-500/20">
        <h2 class="text-lg font-bold text-rose-400 flex items-center gap-2">
            <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            {{ __('Delete Account') }}
        </h2>
        <p class="mt-1 text-xs text-gray-400">
            {{ __('Permanently remove your account, profile photo, watch history, and library bookmarks.') }}
        </p>
    </header>

    <div class="mt-6 p-4 rounded-2xl bg-rose-500/5 border border-rose-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="text-sm font-semibold text-rose-300">Danger: Irreversible Action</div>
            <p class="text-xs text-gray-400 mt-0.5">
                Once deleted, your account cannot be recovered. All associated media data will be cleared immediately.
            </p>
        </div>

        <button type="button"
                x-data=""
                x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-lg shadow-rose-600/30 transition-all flex items-center justify-center gap-2 flex-shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            {{ __('Delete My Account') }}
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 bg-[#11121d] border border-white/10 rounded-2xl shadow-2xl">
            @csrf
            @method('delete')

            <div class="w-12 h-12 rounded-2xl bg-rose-500/15 text-rose-400 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            <h2 class="text-xl font-bold text-white">
                {{ __('Confirm Account Deletion') }}
            </h2>

            <p class="mt-2 text-xs text-gray-400 leading-relaxed">
                {{ __('Are you sure you want to delete your account? This will permanently delete your profile photo, favorites, and watch history. Please enter your current password to proceed.') }}
            </p>

            <div class="mt-6">
                <label for="delete_account_password" class="block text-xs font-semibold uppercase tracking-wider text-gray-300 mb-2">
                    {{ __('Password Confirmation') }}
                </label>
                <input id="delete_account_password"
                       name="password"
                       type="password"
                       class="block w-full px-4 py-3 rounded-xl bg-white/[0.04] border border-white/10 text-white placeholder-gray-500 text-sm focus:outline-none focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition-all"
                       placeholder="{{ __('Enter your current password') }}" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-xs text-rose-400" />
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" 
                        x-on:click="$dispatch('close')" 
                        class="px-4 py-2.5 rounded-xl bg-white/[0.05] hover:bg-white/[0.1] text-gray-300 text-xs font-semibold border border-white/10 transition-all">
                    {{ __('Cancel') }}
                </button>

                <button type="submit" 
                        class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-lg shadow-rose-600/30 transition-all">
                    {{ __('Permanently Delete') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>

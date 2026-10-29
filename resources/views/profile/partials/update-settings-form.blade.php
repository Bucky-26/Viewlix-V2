<section>
    <header>
        <h2 class="text-lg font-medium text-white">
            {{ __('Playback & Viewing Preferences') }}
        </h2>

        <p class="mt-1 text-sm text-gray-400">
            {{ __("Customize your streaming experience across all devices.") }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.settings.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <label for="default_quality" class="block text-sm font-medium text-gray-300">{{ __('Default Video Quality') }}</label>
            <select id="default_quality" name="default_quality" class="mt-2 block w-full rounded-xl bg-[#11121d] border-gray-600 text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-4 py-2.5">
                <option value="auto" {{ (old('default_quality', $user->settings['default_quality'] ?? 'auto') === 'auto') ? 'selected' : '' }}>Auto (Recommended)</option>
                <option value="1080p" {{ (old('default_quality', $user->settings['default_quality'] ?? 'auto') === '1080p') ? 'selected' : '' }}>1080p (High Definition)</option>
                <option value="720p" {{ (old('default_quality', $user->settings['default_quality'] ?? 'auto') === '720p') ? 'selected' : '' }}>720p (Standard Definition)</option>
                <option value="480p" {{ (old('default_quality', $user->settings['default_quality'] ?? 'auto') === '480p') ? 'selected' : '' }}>480p (Data Saver)</option>
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('default_quality')" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <div>
                <h4 class="text-sm font-medium text-gray-300">Autoplay Next Episode</h4>
                <p class="text-xs text-gray-400 mt-1">Automatically play the next episode when watching TV shows and Anime.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="autoplay_next" value="1" class="sr-only peer" {{ (old('autoplay_next', $user->settings['autoplay_next'] ?? true)) ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-gray-700 rounded-full transition-colors peer-checked:bg-green-500"></div>
                <div class="absolute left-0.5 top-0.5 bg-white w-5 h-5 rounded-full transition-transform peer-checked:translate-x-full shadow-sm"></div>
            </label>
        </div>

        <div class="flex items-center justify-between mt-4">
            <div>
                <h4 class="text-sm font-medium text-gray-300">Skip Intro</h4>
                <p class="text-xs text-gray-400 mt-1">Automatically skip TV show and Anime introductions.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="skip_intro" value="1" class="sr-only peer" {{ (old('skip_intro', $user->settings['skip_intro'] ?? false)) ? 'checked' : '' }}>
                <div class="w-11 h-6 bg-gray-700 rounded-full transition-colors peer-checked:bg-green-500"></div>
                <div class="absolute left-0.5 top-0.5 bg-white w-5 h-5 rounded-full transition-transform peer-checked:translate-x-full shadow-sm"></div>
            </label>
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-viewlix-red to-blue-600 text-white text-xs font-bold shadow-lg shadow-blue-500/20 hover:shadow-blue-500/40 transition-all">
                {{ __('Save Settings') }}
            </button>
        </div>
    </form>
</section>

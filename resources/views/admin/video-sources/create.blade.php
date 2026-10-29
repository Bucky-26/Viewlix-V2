@extends('layouts.admin')

@section('title', 'Add Video Source')

@section('content')
<div class="p-4 sm:p-6 max-w-4xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Add Video Source</h1>
            <p class="text-sm text-gray-400 mt-1">Configure a new embed server or player endpoint</p>
        </div>
        <a href="{{ route('admin.video-sources.index') }}" 
           class="px-4 py-2 bg-viewlix-gray hover:bg-gray-700 text-gray-300 text-sm font-medium rounded-xl transition-colors">
            Cancel
        </a>
    </div>

    @if($errors->any())
        <div class="p-4 bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl space-y-1 text-sm">
            @foreach($errors->all() as $error)
                <p>&bull; {{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('admin.video-sources.store') }}" class="space-y-6">
        @csrf

        {{-- Basic Information --}}
        <div class="bg-viewlix-dark border border-viewlix-border p-5 sm:p-6 rounded-2xl space-y-5">
            <h3 class="text-lg font-bold text-white border-b border-viewlix-border pb-3">Basic Information</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2">
                        Source Name <span class="text-red-400">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           placeholder="e.g. Viewlix [VLX] or Viewlix (ad-free)"
                           class="w-full bg-viewlix-gray text-white text-sm rounded-xl border border-viewlix-border px-4 py-2.5 focus:ring-2 focus:ring-viewlix-red focus:outline-none">
                    <p class="text-[11px] text-gray-400 mt-1">This name is displayed in the player's server selection dropdown.</p>
                </div>

                {{-- Priority --}}
                <div>
                    <label for="priority" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2">
                        Priority Order
                    </label>
                    <input type="number" id="priority" name="priority" value="{{ old('priority', 0) }}"
                           class="w-full bg-viewlix-gray text-white text-sm rounded-xl border border-viewlix-border px-4 py-2.5 focus:ring-2 focus:ring-viewlix-red focus:outline-none">
                    <p class="text-[11px] text-gray-400 mt-1">Higher numbers appear earlier in the server list.</p>
                </div>
            </div>

            {{-- Base URL --}}
            <div>
                <label for="base_url" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2">
                    Base URL / Domain Path
                </label>
                <input type="text" id="base_url" name="base_url" value="{{ old('base_url') }}"
                       placeholder="e.g. https://monitor.viewlix.online/vlx or https://monitor.viewlix.online/embed"
                       class="w-full bg-viewlix-gray text-white text-sm font-mono rounded-xl border border-viewlix-border px-4 py-2.5 focus:ring-2 focus:ring-viewlix-red focus:outline-none">
                <p class="text-[11px] text-gray-400 mt-1">
                    If specified, standard patterns (<code class="text-blue-400">/movie/{id}</code> and <code class="text-blue-400">/tv/{id}/{season}/{episode}</code>) will automatically be used if custom patterns below are left empty.
                </p>
            </div>
        </div>

        {{-- Custom URL Patterns --}}
        <div class="bg-viewlix-dark border border-viewlix-border p-5 sm:p-6 rounded-2xl space-y-5">
            <div class="flex items-center justify-between border-b border-viewlix-border pb-3">
                <h3 class="text-lg font-bold text-white">Custom URL Patterns (Optional)</h3>
                <span class="text-xs text-gray-400">Leave blank to use default base URL format</span>
            </div>

            {{-- Movie Pattern --}}
            <div>
                <label for="movie_url_pattern" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2">
                    Movie Embed Pattern
                </label>
                <input type="text" id="movie_url_pattern" name="movie_url_pattern" value="{{ old('movie_url_pattern') }}"
                       placeholder="Default: {base_url}/movie/{id} (e.g. https://domain.com/embed/movie/{id}?autoPlay=true)"
                       class="w-full bg-viewlix-gray text-white text-sm font-mono rounded-xl border border-viewlix-border px-4 py-2.5 focus:ring-2 focus:ring-viewlix-red focus:outline-none">
            </div>

            {{-- TV Pattern --}}
            <div>
                <label for="tv_url_pattern" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2">
                    TV Show Embed Pattern
                </label>
                <input type="text" id="tv_url_pattern" name="tv_url_pattern" value="{{ old('tv_url_pattern') }}"
                       placeholder="Default: {base_url}/tv/{id}/{season}/{episode} (e.g. https://domain.com/embed/tv?tmdb={id}&season={season}&episode={episode})"
                       class="w-full bg-viewlix-gray text-white text-sm font-mono rounded-xl border border-viewlix-border px-4 py-2.5 focus:ring-2 focus:ring-viewlix-red focus:outline-none">
            </div>

            {{-- Anime Pattern --}}
            <div>
                <label for="anime_url_pattern" class="block text-xs font-semibold text-gray-300 uppercase tracking-wider mb-2">
                    Anime Embed Pattern
                </label>
                <input type="text" id="anime_url_pattern" name="anime_url_pattern" value="{{ old('anime_url_pattern') }}"
                       placeholder="Default: Uses TV pattern if empty"
                       class="w-full bg-viewlix-gray text-white text-sm font-mono rounded-xl border border-viewlix-border px-4 py-2.5 focus:ring-2 focus:ring-viewlix-red focus:outline-none">
            </div>

            <div class="p-3 bg-black/40 rounded-xl text-xs text-gray-400 font-mono space-y-1">
                <span class="font-sans font-bold text-gray-300 block">Available Placeholders:</span>
                <div><code class="text-viewlix-red">{id}</code> - TMDB ID (e.g. 550)</div>
                <div><code class="text-viewlix-red">{season}</code> - Season number (e.g. 1)</div>
                <div><code class="text-viewlix-red">{episode}</code> - Episode number (e.g. 3)</div>
                <div><code class="text-viewlix-red">{slug}</code> - URL title slug</div>
                <div><code class="text-viewlix-red">{base_url}</code> - Content of Base URL field</div>
            </div>
        </div>

        {{-- Supported Content Types & Defaults --}}
        <div class="bg-viewlix-dark border border-viewlix-border p-5 sm:p-6 rounded-2xl space-y-5">
            <h3 class="text-lg font-bold text-white border-b border-viewlix-border pb-3">Supported Media & Defaults</h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                {{-- Movie Options --}}
                <div class="bg-viewlix-gray/40 border border-viewlix-border p-4 rounded-xl space-y-3">
                    <h4 class="text-sm font-bold text-blue-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                        </svg>
                        Movies
                    </h4>
                    <label class="flex items-center gap-2 text-xs text-gray-200 cursor-pointer">
                        <input type="checkbox" name="supports_movie" value="1" {{ old('supports_movie', '1') == '1' ? 'checked' : '' }}
                               class="rounded border-gray-600 text-viewlix-red focus:ring-viewlix-red bg-viewlix-dark">
                        Supports Movies
                    </label>
                    <label class="flex items-center gap-2 text-xs text-gray-200 cursor-pointer">
                        <input type="checkbox" name="is_default_movie" value="1" {{ old('is_default_movie') ? 'checked' : '' }}
                               class="rounded border-gray-600 text-viewlix-red focus:ring-viewlix-red bg-viewlix-dark">
                        Set as Default for Movies
                    </label>
                </div>

                {{-- TV Options --}}
                <div class="bg-viewlix-gray/40 border border-viewlix-border p-4 rounded-xl space-y-3">
                    <h4 class="text-sm font-bold text-purple-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        TV Shows
                    </h4>
                    <label class="flex items-center gap-2 text-xs text-gray-200 cursor-pointer">
                        <input type="checkbox" name="supports_tv" value="1" {{ old('supports_tv', '1') == '1' ? 'checked' : '' }}
                               class="rounded border-gray-600 text-viewlix-red focus:ring-viewlix-red bg-viewlix-dark">
                        Supports TV Shows
                    </label>
                    <label class="flex items-center gap-2 text-xs text-gray-200 cursor-pointer">
                        <input type="checkbox" name="is_default_tv" value="1" {{ old('is_default_tv') ? 'checked' : '' }}
                               class="rounded border-gray-600 text-viewlix-red focus:ring-viewlix-red bg-viewlix-dark">
                        Set as Default for TV Shows
                    </label>
                </div>

                {{-- Anime Options --}}
                <div class="bg-viewlix-gray/40 border border-viewlix-border p-4 rounded-xl space-y-3">
                    <h4 class="text-sm font-bold text-pink-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Anime
                    </h4>
                    <label class="flex items-center gap-2 text-xs text-gray-200 cursor-pointer">
                        <input type="checkbox" name="supports_anime" value="1" {{ old('supports_anime', '1') == '1' ? 'checked' : '' }}
                               class="rounded border-gray-600 text-viewlix-red focus:ring-viewlix-red bg-viewlix-dark">
                        Supports Anime
                    </label>
                    <label class="flex items-center gap-2 text-xs text-gray-200 cursor-pointer">
                        <input type="checkbox" name="is_default_anime" value="1" {{ old('is_default_anime') ? 'checked' : '' }}
                               class="rounded border-gray-600 text-viewlix-red focus:ring-viewlix-red bg-viewlix-dark">
                        Set as Default for Anime
                    </label>
                </div>
            </div>

            {{-- Active Switch --}}
            <div class="pt-2 border-t border-viewlix-border">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                           class="rounded border-gray-600 text-viewlix-red focus:ring-viewlix-red bg-viewlix-dark">
                    <span class="text-sm font-medium text-white">Enable this video source immediately</span>
                </label>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.video-sources.index') }}" 
               class="px-5 py-2.5 bg-viewlix-gray hover:bg-gray-700 text-gray-300 text-sm font-medium rounded-xl transition-colors">
                Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-2.5 bg-viewlix-red hover:bg-red-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-500/20 transition-all">
                Save Video Source
            </button>
        </div>
    </form>
</div>
@endsection

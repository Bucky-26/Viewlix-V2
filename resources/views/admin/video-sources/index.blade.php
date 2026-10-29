@extends('layouts.admin')

@section('title', 'Video Sources')

@section('content')
<div class="p-4 sm:p-6 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-white tracking-tight">Video Sources</h1>
            <p class="text-sm text-gray-400 mt-1">Manage streaming embed servers, custom URLs, and default players</p>
        </div>
        <div>
            <a href="{{ route('admin.video-sources.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-viewlix-red hover:bg-red-600 text-white text-sm font-semibold rounded-xl shadow-lg shadow-red-500/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Add Video Source
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 bg-green-500/10 border border-green-500/20 text-green-400 rounded-xl flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Default Sources Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Default Movie --}}
        <div class="bg-viewlix-dark border border-viewlix-border p-4 sm:p-5 rounded-2xl relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-blue-400 bg-blue-500/10 px-2.5 py-1 rounded-lg border border-blue-500/20 uppercase tracking-wider">
                    Movie Default
                </span>
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-white truncate">
                {{ $defaultMovieSource->name ?? 'No Default Set' }}
            </h3>
            <p class="text-xs text-gray-400 mt-1 font-mono truncate">
                {{ $defaultMovieSource->base_url ?? ($defaultMovieSource->movie_url_pattern ?? 'None') }}
            </p>

            <form method="POST" action="{{ route('admin.video-sources.set-default') }}" class="mt-4">
                @csrf
                <input type="hidden" name="type" value="movie">
                <div class="flex items-center gap-2">
                    <select name="source_id" onchange="this.form.submit()" class="text-xs bg-viewlix-gray text-white rounded-lg border border-viewlix-border px-2.5 py-1.5 w-full focus:ring-1 focus:ring-viewlix-red">
                        <option value="" disabled>Change Movie Default...</option>
                        @foreach($sources->where('supports_movie', true) as $s)
                            <option value="{{ $s->id }}" {{ $s->is_default_movie ? 'selected' : '' }}>
                                {{ $s->name }} (Priority: {{ $s->priority }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        {{-- Default TV Show --}}
        <div class="bg-viewlix-dark border border-viewlix-border p-4 sm:p-5 rounded-2xl relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-purple-400 bg-purple-500/10 px-2.5 py-1 rounded-lg border border-purple-500/20 uppercase tracking-wider">
                    TV Show Default
                </span>
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-white truncate">
                {{ $defaultTvSource->name ?? 'No Default Set' }}
            </h3>
            <p class="text-xs text-gray-400 mt-1 font-mono truncate">
                {{ $defaultTvSource->base_url ?? ($defaultTvSource->tv_url_pattern ?? 'None') }}
            </p>

            <form method="POST" action="{{ route('admin.video-sources.set-default') }}" class="mt-4">
                @csrf
                <input type="hidden" name="type" value="tv">
                <div class="flex items-center gap-2">
                    <select name="source_id" onchange="this.form.submit()" class="text-xs bg-viewlix-gray text-white rounded-lg border border-viewlix-border px-2.5 py-1.5 w-full focus:ring-1 focus:ring-viewlix-red">
                        <option value="" disabled>Change TV Default...</option>
                        @foreach($sources->where('supports_tv', true) as $s)
                            <option value="{{ $s->id }}" {{ $s->is_default_tv ? 'selected' : '' }}>
                                {{ $s->name }} (Priority: {{ $s->priority }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        {{-- Default Anime --}}
        <div class="bg-viewlix-dark border border-viewlix-border p-4 sm:p-5 rounded-2xl relative overflow-hidden">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-pink-400 bg-pink-500/10 px-2.5 py-1 rounded-lg border border-pink-500/20 uppercase tracking-wider">
                    Anime Default
                </span>
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-white truncate">
                {{ $defaultAnimeSource->name ?? 'No Default Set' }}
            </h3>
            <p class="text-xs text-gray-400 mt-1 font-mono truncate">
                {{ $defaultAnimeSource->base_url ?? ($defaultAnimeSource->anime_url_pattern ?? 'None') }}
            </p>

            <form method="POST" action="{{ route('admin.video-sources.set-default') }}" class="mt-4">
                @csrf
                <input type="hidden" name="type" value="anime">
                <div class="flex items-center gap-2">
                    <select name="source_id" onchange="this.form.submit()" class="text-xs bg-viewlix-gray text-white rounded-lg border border-viewlix-border px-2.5 py-1.5 w-full focus:ring-1 focus:ring-viewlix-red">
                        <option value="" disabled>Change Anime Default...</option>
                        @foreach($sources->where('supports_anime', true) as $s)
                            <option value="{{ $s->id }}" {{ $s->is_default_anime ? 'selected' : '' }}>
                                {{ $s->name }} (Priority: {{ $s->priority }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>
    </div>

    {{-- Sources Table --}}
    <div class="bg-viewlix-dark border border-viewlix-border rounded-2xl overflow-hidden shadow-xl">
        <div class="p-4 sm:p-5 border-b border-viewlix-border flex flex-col sm:flex-row sm:items-center justify-between gap-2">
            <div>
                <h2 class="text-lg font-bold text-white">All Configured Video Sources</h2>
                <p class="text-xs text-gray-400 mt-0.5">Player will display active sources in order of priority</p>
            </div>
            <div class="text-xs text-gray-400 flex items-center gap-2">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-green-500"></span>
                <span>{{ $sources->where('is_active', true)->count() }} Active</span>
                <span class="text-gray-600">|</span>
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-gray-500"></span>
                <span>{{ $sources->where('is_active', false)->count() }} Disabled</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left divide-y divide-viewlix-border">
                <thead class="bg-viewlix-darker/60 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Source Name</th>
                        <th class="px-5 py-3.5">Base URL / Patterns</th>
                        <th class="px-5 py-3.5">Supports</th>
                        <th class="px-5 py-3.5 text-center">Default For</th>
                        <th class="px-5 py-3.5 text-center">Priority</th>
                        <th class="px-5 py-3.5 text-center">Status</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-viewlix-border text-sm">
                    @forelse($sources as $source)
                        <tr class="hover:bg-viewlix-gray/30 transition-colors {{ !$source->is_active ? 'opacity-60' : '' }}">
                            {{-- Name --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-white">{{ $source->name }}</span>
                                </div>
                            </td>

                            {{-- Base URL / Pattern --}}
                            <td class="px-5 py-4 font-mono text-xs max-w-xs truncate text-gray-300">
                                @if($source->base_url)
                                    <div class="text-blue-400 truncate">{{ $source->base_url }}</div>
                                @endif
                                @if($source->movie_url_pattern && $source->movie_url_pattern !== $source->base_url . '/movie/{id}')
                                    <div class="text-gray-500 truncate text-[11px]">Movie: {{ $source->movie_url_pattern }}</div>
                                @endif
                                @if($source->tv_url_pattern && $source->tv_url_pattern !== $source->base_url . '/tv/{id}/{season}/{episode}')
                                    <div class="text-gray-500 truncate text-[11px]">TV: {{ $source->tv_url_pattern }}</div>
                                @endif
                            </td>

                            {{-- Supports --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    @if($source->supports_movie)
                                        <span class="px-2 py-0.5 text-[11px] font-semibold bg-blue-500/10 text-blue-400 border border-blue-500/20 rounded-md">Movie</span>
                                    @endif
                                    @if($source->supports_tv)
                                        <span class="px-2 py-0.5 text-[11px] font-semibold bg-purple-500/10 text-purple-400 border border-purple-500/20 rounded-md">TV</span>
                                    @endif
                                    @if($source->supports_anime)
                                        <span class="px-2 py-0.5 text-[11px] font-semibold bg-pink-500/10 text-pink-400 border border-pink-500/20 rounded-md">Anime</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Default Badges --}}
                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                <div class="flex items-center justify-center gap-1">
                                    @if($source->is_default_movie)
                                        <span class="px-2 py-0.5 text-[10px] font-bold bg-blue-600 text-white rounded-full">Movie</span>
                                    @endif
                                    @if($source->is_default_tv)
                                        <span class="px-2 py-0.5 text-[10px] font-bold bg-purple-600 text-white rounded-full">TV</span>
                                    @endif
                                    @if($source->is_default_anime)
                                        <span class="px-2 py-0.5 text-[10px] font-bold bg-pink-600 text-white rounded-full">Anime</span>
                                    @endif
                                    @if(!$source->is_default_movie && !$source->is_default_tv && !$source->is_default_anime)
                                        <span class="text-gray-500 text-xs">—</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Priority --}}
                            <td class="px-5 py-4 whitespace-nowrap text-center text-xs font-semibold text-gray-300">
                                {{ $source->priority }}
                            </td>

                            {{-- Status toggle --}}
                            <td class="px-5 py-4 whitespace-nowrap text-center">
                                <form method="POST" action="{{ route('admin.video-sources.toggle', $source) }}" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="px-2.5 py-1 text-xs font-semibold rounded-full border transition-all {{ $source->is_active ? 'bg-green-500/10 border-green-500/30 text-green-400 hover:bg-green-500/20' : 'bg-gray-500/10 border-gray-500/30 text-gray-400 hover:bg-gray-500/20' }}">
                                        {{ $source->is_active ? 'Active' : 'Disabled' }}
                                    </button>
                                </form>
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-4 whitespace-nowrap text-right text-xs">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.video-sources.edit', $source) }}" 
                                       class="p-1.5 text-gray-400 hover:text-white hover:bg-viewlix-gray rounded-lg transition-colors" title="Edit Source">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.video-sources.destroy', $source) }}" 
                                          onsubmit="return confirm('Are you sure you want to delete source {{ addslashes($source->name) }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-colors" title="Delete Source">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-8 text-center text-gray-500">
                                No video sources configured yet. Click "Add Video Source" to create your first one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Advanced Integration & RTDB Watch Progress Docs --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        {{-- Card 1: Dynamic URL Patterns & Custom Embeds --}}
        <div class="bg-viewlix-dark/80 border border-viewlix-border p-5 rounded-2xl text-xs text-gray-400 space-y-3">
            <h4 class="font-bold text-white text-sm flex items-center gap-2">
                <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Dynamic URL Patterns &amp; Placeholders
            </h4>
            <p>
                Configure custom route patterns for third-party embeds or your own player proxy:
            </p>
            <ul class="list-disc list-inside space-y-1 font-mono text-[11px] text-gray-300">
                <li><strong class="text-white font-sans">Viewlix [VLX]:</strong> <code class="text-blue-400">https://monitor.viewlix.online/vlx</code> &rarr; auto-formats to <code class="text-gray-400">/vlx/movie/{id}</code> and <code class="text-gray-400">/vlx/tv/{id}/{season}/{episode}</code></li>
                <li><strong class="text-white font-sans">Viewlix (Ad-Free):</strong> <code class="text-blue-400">https://monitor.viewlix.online/embed</code> &rarr; auto-formats to <code class="text-gray-400">/embed/movie/{id}</code> and <code class="text-gray-400">/embed/tv/{id}/{season}/{episode}</code></li>
            </ul>
            <div class="pt-2 border-t border-white/5 space-y-1">
                <div class="text-[11px] text-gray-300 font-semibold">Available Pattern Tokens:</div>
                <div class="flex flex-wrap gap-1.5 font-mono text-[10px]">
                    <span class="px-2 py-0.5 rounded bg-white/5 text-viewlix-red border border-white/5">{id} (TMDB ID)</span>
                    <span class="px-2 py-0.5 rounded bg-white/5 text-purple-400 border border-white/5">{season}</span>
                    <span class="px-2 py-0.5 rounded bg-white/5 text-purple-400 border border-white/5">{episode}</span>
                    <span class="px-2 py-0.5 rounded bg-white/5 text-blue-400 border border-white/5">{base_url}</span>
                    <span class="px-2 py-0.5 rounded bg-white/5 text-emerald-400 border border-white/5">{slug}</span>
                </div>
            </div>
        </div>

        {{-- Card 2: Viewlix Source PostMessage & RTDB Egress Guide --}}
        <div class="bg-viewlix-dark/80 border border-viewlix-border p-5 rounded-2xl text-xs text-gray-400 space-y-3">
            <h4 class="font-bold text-white text-sm flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Watch Progress &amp; RTDB Egress Optimization
                </span>
                <span class="px-2 py-0.5 text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 rounded-full">
                    Saves 95% Bandwidth
                </span>
            </h4>
            
            {{-- RTDB Egress Toggle --}}
            <div class="bg-black/30 border border-white/5 rounded-xl p-3 flex items-center justify-between">
                <div>
                    <div class="text-gray-200 font-semibold text-xs">Read Watch Progress from Local DB</div>
                    <div class="text-[10px] text-gray-500 mt-0.5">Disables RTDB reads to save massive egress costs. (Syncs via backend)</div>
                </div>
                <form method="POST" action="{{ route('admin.settings.toggle-rtdb-egress') }}">
                    @csrf
                    @php $optimizeRtdbEgress = \App\Models\AppSetting::getVal('optimize_rtdb_egress', true); @endphp
                    <button type="submit" class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-viewlix-dark transition-colors ease-in-out duration-200 {{ $optimizeRtdbEgress ? 'bg-emerald-500' : 'bg-gray-600' }}">
                        <span class="pointer-events-none absolute left-0.5 inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $optimizeRtdbEgress ? 'translate-x-4' : 'translate-x-0' }}"></span>
                    </button>
                </form>
            </div>

            <p>
                When building or configuring custom players (e.g. <code class="text-blue-400">vlx-contentv3</code>), emit postMessage events to <code class="text-gray-200">window.parent</code>:
            </p>
            <pre class="p-2.5 rounded-xl bg-black/50 border border-white/5 font-mono text-[10px] text-gray-300 overflow-x-auto"><code>window.parent.postMessage({
    type: 'PLAYER_EVENT',
    data: {
        player_status: 'playing', // 'playing' | 'paused' | 'seeked' | 'completed'
        player_progress: Math.floor(video.currentTime),
        player_duration: Math.floor(video.duration)
    }
}, '*');</code></pre>
            <div class="space-y-1.5 text-[11px] text-gray-300">
                <div class="font-semibold text-emerald-400 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    RTDB Egress Saving Best Practices:
                </div>
                <ul class="list-disc list-inside space-y-0.5 text-[11px] text-gray-400">
                    <li><strong class="text-gray-200">Throttle updates to 10s:</strong> Do not post on every HTML5 <code class="text-gray-300">timeupdate</code> (saves 14,000+ writes/hr/user).</li>
                    <li><strong class="text-gray-200">Single item queries:</strong> Read <code class="text-blue-400">users/{uid}/watchProgress/{id}</code> instead of listening to the root tree.</li>
                    <li><strong class="text-gray-200">Immediate trigger on pause/seek/ended:</strong> Guarantees progress is saved without continuous polling.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

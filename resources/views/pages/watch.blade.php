@extends('layouts.app')

@section('title', ($title ?? 'Watch') . ' - Viewlix')

@push('styles')
<style>
    .video-container {
        position: relative;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        height: 0;
        overflow: hidden;
        background: #000;
        border-radius: 1rem;
    }
    .video-container iframe,
    .video-container video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
        border-radius: 1rem;
    }
</style>
@endpush

@section('content')
<div class="min-h-[calc(100vh-200px)]">
    {{-- Video Player with VidLink --}}
    <div class="mb-6">
        <div class="video-container" style="position: relative; width: 100%; height: 0; padding-bottom: 56.25%;">
            <iframe 
                src="blob:{{ $embedUrl }}" 
                width="1000" height="600" frameborder="0" gesture="media" allow="encrypted-media" 
                allowfullscreen 
                referrerpolicy="origin"
                loading="lazy">
            </iframe>
        </div>
    </div>
    
    {{-- Player Controls --}}
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ $title ?? 'Unknown Title' }}</h1>
            <p class="text-gray-400">
                @if($type === 'tv' || $type === 'anime')
                    Season {{ $season ?? 1 }} • Episode {{ $episode ?? 1 }}
                @else
                    @if(!empty($media['release_date']))
                    {{ \Carbon\Carbon::parse($media['release_date'])->format('Y') }} • 
                    @endif
                    HD
                @endif
            </p>
        </div>
        
        <div class="flex items-center gap-3">
                {{-- Source / Server selector --}}
                <div class="flex items-center gap-2">
                    <label for="sourceSelect" class="text-sm text-gray-400 hidden md:inline">Source</label>
                    <select id="sourceSelect" class="text-sm bg-viewlix-gray text-white px-3 py-2 rounded-xl border border-viewlix-border hover:bg-viewlix-gray-light transition-colors">
                        {{-- Options injected by JS from server-provided $sources or fallback --}}
                    </select>
                    <button id="openSourceBtn" class="hidden md:inline-flex items-center gap-2 px-3 py-2 bg-viewlix-gray text-white text-sm font-medium rounded-xl border border-viewlix-border hover:bg-viewlix-gray-light transition-colors">
                        Open
                    </button>
                </div>
            {{-- Back to Details --}}
            @if($type === 'movie')
            <a href="{{ route('movie.show', ['slug' => $id . '-' . \Illuminate\Support\Str::slug($title)]) }}" class="flex items-center gap-2 px-4 py-2 bg-viewlix-gray text-white text-sm font-medium rounded-xl border border-viewlix-border hover:bg-viewlix-gray-light transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Details
            </a>
            @elseif($type === 'tv')
            <a href="{{ route('tv-show.show', ['slug' => $id . '-' . \Illuminate\Support\Str::slug($title)]) }}" class="flex items-center gap-2 px-4 py-2 bg-viewlix-gray text-white text-sm font-medium rounded-xl border border-viewlix-border hover:bg-viewlix-gray-light transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Details
            </a>
            @else
            <a href="{{ route('anime.show', ['id' => $id]) }}" class="flex items-center gap-2 px-4 py-2 bg-viewlix-gray text-white text-sm font-medium rounded-xl border border-viewlix-border hover:bg-viewlix-gray-light transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Details
            </a>
            @endif
            
            {{-- Favorite --}}
            @auth
            <button 
                id="favoriteBtn"
                onclick="toggleFavorite()"
                class="flex items-center justify-center w-10 h-10 bg-viewlix-gray hover:bg-viewlix-gray-light rounded-xl border border-viewlix-border transition-colors"
            >
                <svg id="favoriteIcon" class="w-5 h-5 text-gray-400 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </button>
            @else
            <a href="{{ route('login') }}" class="flex items-center justify-center w-10 h-10 bg-viewlix-gray text-gray-400 hover:text-viewlix-red rounded-xl border border-viewlix-border transition-colors" title="Login to add favorites">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </a>
            @endauth
        </div>
    </div>
    
    @if($type !== 'movie' && !empty($episodes))
    {{-- Episode Navigation --}}
    <div class="flex items-center justify-between gap-4 mb-6">
        @if(($episode ?? 1) > 1)
        <a href="{{ $type === 'tv' ? route('tv-show.watch', ['id' => $id, 'season' => $season, 'episode' => $episode - 1]) : route('anime.watch', ['id' => $id, 'season' => $season, 'episode' => $episode - 1]) }}" 
           class="flex items-center gap-2 px-4 py-2 bg-viewlix-gray text-white hover:bg-viewlix-gray-light rounded-xl border border-viewlix-border transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Previous
        </a>
        @else
        <button class="flex items-center gap-2 px-4 py-2 bg-viewlix-gray text-gray-500 rounded-xl border border-viewlix-border opacity-50 cursor-not-allowed" disabled>
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Previous
        </button>
        @endif
        
        @if(($episode ?? 1) < count($episodes))
        <a href="{{ $type === 'tv' ? route('tv-show.watch', ['id' => $id, 'season' => $season, 'episode' => $episode + 1]) : route('anime.watch', ['id' => $id, 'season' => $season, 'episode' => $episode + 1]) }}" 
           class="flex items-center gap-2 px-4 py-2 bg-viewlix-gray text-white hover:bg-viewlix-gray-light rounded-xl border border-viewlix-border transition-colors">
            Next
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
        @else
        <button class="flex items-center gap-2 px-4 py-2 bg-viewlix-gray text-gray-500 rounded-xl border border-viewlix-border opacity-50 cursor-not-allowed" disabled>
            Next
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        @endif
    </div>
    
    {{-- Episode List --}}
    <div class="mb-8">
        <h2 class="text-lg font-bold text-white mb-4">Episodes - Season {{ $season ?? 1 }}</h2>
        <div class="grid grid-cols-5 sm:grid-cols-8 md:grid-cols-10 lg:grid-cols-12 xl:grid-cols-16 gap-2">
            @foreach($episodes as $ep)
            <a href="{{ $type === 'tv' ? route('tv-show.watch', ['id' => $id, 'season' => $season, 'episode' => $ep['episode_number']]) : route('anime.watch', ['id' => $id, 'season' => $season, 'episode' => $ep['episode_number']]) }}" 
               class="aspect-square flex items-center justify-center rounded-lg border font-medium transition-colors
                      {{ ($episode ?? 1) == $ep['episode_number'] ? 'bg-viewlix-red border-viewlix-red text-white' : 'bg-viewlix-gray border-viewlix-border text-gray-400 hover:border-viewlix-red hover:text-white' }}"
               title="{{ $ep['name'] ?? 'Episode '.$ep['episode_number'] }}">
                {{ $ep['episode_number'] }}
            </a>
            @endforeach
        </div>
    </div>
    @endif
    
    {{-- Overview --}}
    @if(!empty($media['overview']))
    <div class="mb-8">
        <h2 class="text-lg font-bold text-white mb-4">Overview</h2>
        <p class="text-gray-400 leading-relaxed max-w-3xl">{{ $media['overview'] }}</p>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Media data for tracking
const mediaData = {
    id: {{ $id }},
    type: '{{ $type }}',
    title: @json($title ?? 'Unknown'),
    posterPath: @json($media['poster_path'] ?? null),
    backdropPath: @json($media['backdrop_path'] ?? null),
    season: {{ $season ?? 'null' }},
    episode: {{ $episode ?? 'null' }},
    voteAverage: {{ $media['vote_average'] ?? 'null' }},
    releaseDate: @json($media['release_date'] ?? $media['first_air_date'] ?? null)
};

let isFavorited = false;

// Check if favorited on load
@auth
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const response = await fetch(`{{ route('favorites.check') }}?media_id=${mediaData.id}&media_type=${mediaData.type}`);
        const data = await response.json();
        isFavorited = data.favorited;
        updateFavoriteIcon();
    } catch (error) {
        console.error('Error checking favorite status:', error);
    }
});
@endauth

function updateFavoriteIcon() {
    const icon = document.getElementById('favoriteIcon');
    if (!icon) return;
    
    if (isFavorited) {
        icon.setAttribute('fill', 'currentColor');
        icon.classList.remove('text-gray-400');
        icon.classList.add('text-viewlix-red');
    } else {
        icon.setAttribute('fill', 'none');
        icon.classList.remove('text-viewlix-red');
        icon.classList.add('text-gray-400');
    }
}

async function toggleFavorite() {
    try {
        const response = await fetch('{{ route("favorites.toggle") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                media_id: mediaData.id,
                media_type: mediaData.type,
                title: mediaData.title,
                poster_path: mediaData.posterPath,
                backdrop_path: mediaData.backdropPath,
                vote_average: mediaData.voteAverage,
                release_date: mediaData.releaseDate
            })
        });
        
        const data = await response.json();
        isFavorited = data.status === 'added';
        updateFavoriteIcon();
    } catch (error) {
        console.error('Error toggling favorite:', error);
    }
}

// ============================================================
// VidLink Continue Watching Progress Tracking
// Saves to Firebase (authenticated) + localStorage (fallback)
// ============================================================

let lastSavedProgress = 0;
let lastSavedTime = 0;
const SAVE_INTERVAL_SECONDS = 30; // Save to Firebase every 30 seconds

// Build progress data object for saving
function buildProgressData(watchedTime, duration) {
    const progressData = {
        id: mediaData.id,
        type: mediaData.type,
        title: mediaData.title,
        poster_path: mediaData.posterPath,
        backdrop_path: mediaData.backdropPath,
        progress: {
            watched: watchedTime,
            duration: duration
        },
        last_updated: Date.now()
    };
    
    // Add TV/Anime specific fields
    if (mediaData.type !== 'movie' && mediaData.season && mediaData.episode) {
        progressData.last_season_watched = String(mediaData.season);
        progressData.last_episode_watched = String(mediaData.episode);
        
        const seasonKey = `s${mediaData.season}e${mediaData.episode}`;
        progressData.show_progress = {
            [seasonKey]: {
                season: String(mediaData.season),
                episode: String(mediaData.episode),
                progress: {
                    watched: watchedTime,
                    duration: duration
                }
            }
        };
    }
    
    return progressData;
}

// Listen for VidLink postMessage events
window.addEventListener('message', function(event) {
    // Accept messages from VidLink
    if (event.origin !== 'https://vidlink.pro') return;
    
    // Handle MEDIA_DATA from VidLink
    if (event.data?.type === 'MEDIA_DATA') {
        const data = event.data.data;
        const mediaKey = String(mediaData.id);
        
        if (data[mediaKey]) {
            const progress = data[mediaKey].progress;
            if (progress) {
                const timeSinceLastSave = Math.abs(progress.watched - lastSavedTime);
                
                // Save to Firebase/localStorage using global function
                if (typeof window.saveWatchProgress === 'function') {
                    const progressData = buildProgressData(progress.watched, progress.duration);
                    
                    // Save frequently to localStorage, less frequently to Firebase
                    if (timeSinceLastSave >= SAVE_INTERVAL_SECONDS) {
                        window.saveWatchProgress(mediaData.id, progressData);
                        lastSavedTime = progress.watched;
                    } else {
                        // Just update localStorage for quick saves
                        try {
                            const allProgress = JSON.parse(localStorage.getItem('vidLinkProgress') || '{}');
                            allProgress[mediaKey] = progressData;
                            localStorage.setItem('vidLinkProgress', JSON.stringify(allProgress));
                        } catch (e) {}
                    }
                }
            }
        }
        return;
    }
    
    // Handle manual progress tracking (fallback)
    try {
        const data = typeof event.data === 'string' ? JSON.parse(event.data) : event.data;
        
        if (data.currentTime !== undefined && data.duration !== undefined) {
            const watchedTime = data.currentTime;
            const duration = data.duration;
            
            if (duration > 0) {
                const timeSinceLastSave = Math.abs(watchedTime - lastSavedTime);
                const progressData = buildProgressData(watchedTime, duration);
                
                // Save to Firebase/localStorage
                if (typeof window.saveWatchProgress === 'function') {
                    if (timeSinceLastSave >= SAVE_INTERVAL_SECONDS) {
                        window.saveWatchProgress(mediaData.id, progressData);
                        lastSavedTime = watchedTime;
                    } else {
                        // Just update localStorage
                        try {
                            const allProgress = JSON.parse(localStorage.getItem('vidLinkProgress') || '{}');
                            allProgress[String(mediaData.id)] = progressData;
                            localStorage.setItem('vidLinkProgress', JSON.stringify(allProgress));
                        } catch (e) {}
                    }
                }
            }
        }
    } catch (error) {
        // Silently ignore parsing errors
    }
});

// Save progress when leaving the page
window.addEventListener('beforeunload', function() {
    try {
        const allProgress = JSON.parse(localStorage.getItem('vidLinkProgress') || '{}');
        const mediaKey = String(mediaData.id);
        const currentMedia = allProgress[mediaKey];
        
        if (currentMedia?.progress && typeof window.saveWatchProgress === 'function') {
            // Force save to Firebase on page exit
            window.saveWatchProgress(mediaData.id, currentMedia);
        }
    } catch (e) {}
});

// Load saved progress on page load
document.addEventListener('DOMContentLoaded', async function() {
    // Try to get progress from Firebase first, then localStorage
    if (typeof window.getWatchProgress === 'function') {
        const saved = await window.getWatchProgress(mediaData.id);
        
        if (saved) {
            if (mediaData.type === 'movie') {
                if (saved.progress?.watched > 0) {
                    lastSavedProgress = (saved.progress.watched / saved.progress.duration) * 100;
                    lastSavedTime = saved.progress.watched;
                    console.log(`📺 Resuming from ${Math.floor(saved.progress.watched)}s`);
                }
            } else {
                // For TV shows, check episode-specific progress
                const seasonKey = `s${mediaData.season}e${mediaData.episode}`;
                if (saved.show_progress?.[seasonKey]?.progress?.watched > 0) {
                    const epProgress = saved.show_progress[seasonKey].progress;
                    lastSavedProgress = (epProgress.watched / epProgress.duration) * 100;
                    lastSavedTime = epProgress.watched;
                    console.log(`📺 Resuming S${mediaData.season}E${mediaData.episode} from ${Math.floor(epProgress.watched)}s`);
                }
            }
        }
    }
});

// ============================================================
// Multi-source / server switching (iframe source selection)
// Expectation: Controller may pass $sources = [ ['name'=>'Vidsrcme','embed'=>'https://vidsrcme.ru/embed/...'], ... ]
// Fallback: use existing `{{ $embedUrl }}` as a single source named 'Default'.
// ============================================================

const providedSources = @json($sources ?? null);
const defaultEmbed = @json($embedUrl);

function buildSources() {
    if (Array.isArray(providedSources) && providedSources.length > 0) {
        // Ensure each source has {name, embed}
        return providedSources.map(s => ({ name: s.name || 'Source', embed: s.embed || defaultEmbed }));
    }

    // fallback single source
    return [{ name: 'Default', embed: defaultEmbed }];
}

const sources = buildSources();
const sourceSelect = document.getElementById('sourceSelect');
const openSourceBtn = document.getElementById('openSourceBtn');
const playerIframe = document.querySelector('.video-container iframe');

function populateSourceOptions() {
    if (!sourceSelect) return;
    sourceSelect.innerHTML = '';
    sources.forEach((s, idx) => {
        const opt = document.createElement('option');
        opt.value = idx;
        opt.text = s.name;
        sourceSelect.appendChild(opt);
    });

    // Restore saved choice for this media
    try {
        const key = `preferredSource_${mediaData.type}_${mediaData.id}`;
        const saved = localStorage.getItem(key);
        if (saved !== null && !isNaN(parseInt(saved)) && sources[parseInt(saved)]) {
            sourceSelect.value = saved;
        } else {
            sourceSelect.value = 0;
        }
    } catch (e) {
        sourceSelect.value = 0;
    }

    // Apply initial selection
    applySelectedSource();
}

function applySelectedSource() {
    if (!playerIframe || !sourceSelect) return;
    const idx = parseInt(sourceSelect.value || 0);
    const src = sources[idx]?.embed || defaultEmbed;
    // update iframe src if different
    if (playerIframe.getAttribute('src') !== src) {
        playerIframe.setAttribute('src', src);
    }

   

    // persist selection per media
    try {
        const key = `preferredSource_${mediaData.type}_${mediaData.id}`;
        localStorage.setItem(key, String(idx));
    } catch (e) {}

    // toggle open button visibility (some mobile layouts hide it)
    if (openSourceBtn) {
        openSourceBtn.classList.remove('hidden');
    }
}

if (sourceSelect) {
    sourceSelect.addEventListener('change', applySelectedSource);
}

if (openSourceBtn) {
    openSourceBtn.addEventListener('click', function() {
        const idx = parseInt(sourceSelect.value || 0);
        const url = sources[idx]?.embed || defaultEmbed;
        // open raw embed in new tab
        window.open(url, '_blank');
    });
}

// Initialize after DOM loaded
document.addEventListener('DOMContentLoaded', populateSourceOptions);
</script>
@endpush

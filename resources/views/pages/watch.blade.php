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
        <div class="video-container">
            <iframe 
                src="{{ $embedUrl }}" 
                allowfullscreen 
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
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
            {{-- Back to Details --}}
            @if($type === 'movie')
            <a href="{{ route('movie.show', ['id' => $id]) }}" class="flex items-center gap-2 px-4 py-2 bg-viewlix-gray text-white text-sm font-medium rounded-xl border border-viewlix-border hover:bg-viewlix-gray-light transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Details
            </a>
            @elseif($type === 'tv')
            <a href="{{ route('tv-show.show', ['id' => $id]) }}" class="flex items-center gap-2 px-4 py-2 bg-viewlix-gray text-white text-sm font-medium rounded-xl border border-viewlix-border hover:bg-viewlix-gray-light transition-colors">
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

// VidLink Progress Tracking
let lastSavedProgress = 0;
const SAVE_INTERVAL = 30; // Save every 30 seconds of progress change

window.addEventListener('message', function(event) {
    // Validate origin (VidLink)
    if (!event.origin.includes('vidlink.pro')) return;
    
    try {
        const data = typeof event.data === 'string' ? JSON.parse(event.data) : event.data;
        
        // Handle progress updates from VidLink
        if (data.type === 'MEDIA_DATA' || data.type === 'progress') {
            const watchedTime = Math.floor(data.currentTime || data.watched || 0);
            const duration = Math.floor(data.duration || 0);
            
            if (duration > 0) {
                const currentProgress = (watchedTime / duration) * 100;
                
                // Save progress if changed significantly (every 5% or 30 seconds)
                if (Math.abs(currentProgress - lastSavedProgress) >= 5 || 
                    Math.abs(watchedTime - (lastSavedProgress * duration / 100)) >= SAVE_INTERVAL) {
                    saveWatchProgress(watchedTime, duration);
                    lastSavedProgress = currentProgress;
                }
            }
        }
    } catch (error) {
        // Silently ignore parsing errors
    }
});

@auth
async function saveWatchProgress(watchedTime, duration) {
    try {
        await fetch('{{ route("watch-history.store") }}', {
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
                season: mediaData.season,
                episode: mediaData.episode,
                watched_time: watchedTime,
                duration: duration
            })
        });
    } catch (error) {
        console.error('Error saving watch progress:', error);
    }
}

// Save progress when leaving the page
window.addEventListener('beforeunload', function() {
    // Use sendBeacon for reliable delivery
    const data = JSON.stringify({
        media_id: mediaData.id,
        media_type: mediaData.type,
        title: mediaData.title,
        poster_path: mediaData.posterPath,
        backdrop_path: mediaData.backdropPath,
        season: mediaData.season,
        episode: mediaData.episode,
        watched_time: 0, // Will be updated by last progress
        duration: 1
    });
    
    navigator.sendBeacon?.('{{ route("watch-history.store") }}', new Blob([data], {type: 'application/json'}));
});
@endauth
</script>
@endpush

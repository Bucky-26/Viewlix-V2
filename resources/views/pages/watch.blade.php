@extends('layouts.app')

@section('title', ($title ?? 'Watch') . ' - Viewlix')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/watch.css') }}">
@endpush

@section('content')
<div class="min-h-[calc(100vh-200px)]">
    {{-- Video Player with VidLink --}}
    <div class="mb-6">
        <div class="video-container" style="position: relative; width: 100%; height: 0; padding-bottom: 56.25%;">
            <iframe 
                id="playerIframe"
                src="blob:{{ $embedUrl }}" 
                scrolling="no"
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
    <a href="{{ $type === 'tv' ? route('tv-show.watch', ['slug' => $id . '-' . \Illuminate\Support\Str::slug($title), 'season' => $season, 'episode' => $episode - 1]) : route('anime.watch', ['id' => $id, 'season' => $season, 'episode' => $episode - 1]) }}" 
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
    <a href="{{ $type === 'tv' ? route('tv-show.watch', ['slug' => $id . '-' . \Illuminate\Support\Str::slug($title), 'season' => $season, 'episode' => $episode + 1]) : route('anime.watch', ['id' => $id, 'season' => $season, 'episode' => $episode + 1]) }}" 
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
         <a href="{{ $type === 'tv' ? route('tv-show.watch', ['slug' => $id . '-' . \Illuminate\Support\Str::slug($title), 'season' => $season, 'episode' => $ep['episode_number']]) : route('anime.watch', ['id' => $id, 'season' => $season, 'episode' => $ep['episode_number']]) }}" 
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
// Media data for tracking (Blade-populated)
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

// Check if favorited on load (uses Blade route helper, keep inline)
@auth
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const response = await fetch(`{{ route('favorites.check') }}?media_id=${mediaData.id}&media_type=${mediaData.type}`);
        const data = await response.json();
        isFavorited = data.favorited;
        // updateFavoriteIcon is defined in external file; attempt immediate call or short retry
        if (typeof updateFavoriteIcon === 'function') {
            updateFavoriteIcon();
        } else {
            setTimeout(() => { if (typeof updateFavoriteIcon === 'function') updateFavoriteIcon(); }, 250);
        }
    } catch (error) {
        console.error('Error checking favorite status:', error);
    }
});
@endauth

// Toggle favorite (uses Blade route helper and CSRF token — keep inline)
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
        if (typeof updateFavoriteIcon === 'function') updateFavoriteIcon();
    } catch (error) {
        console.error('Error toggling favorite:', error);
    }
}

// Pass server-provided sources/embed to external script via globals
const providedSources = @json($sources ?? null);
const defaultEmbed = @json($embedUrl);
</script>

<script src="{{ asset('js/watch.js') }}"></script>
@endpush

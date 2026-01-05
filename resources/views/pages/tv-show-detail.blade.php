@extends('layouts.app')

@section('title', ($tvShow['name'] ?? 'TV Show') . ' - Viewlix')

@section('content')
<div class="min-h-[calc(100vh-200px)]">
    {{-- Hero/Banner Section --}}
    <div class="relative -mx-4 lg:-mx-6 -mt-4 lg:-mt-6 mb-8">
        <div class="aspect-[21/9] bg-viewlix-darker relative overflow-hidden">
            @if(!empty($tvShow['backdrop_path']))
            <img src="{{ $backdropUrl }}" alt="{{ $tvShow['name'] }}" class="absolute inset-0 w-full h-full object-cover">
            @endif
            <div class="absolute inset-0 bg-gradient-to-r from-viewlix-dark via-viewlix-dark/80 to-transparent z-10"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-viewlix-dark via-transparent to-transparent z-10"></div>
        </div>
        
        {{-- Content Overlay --}}
        <div class="absolute bottom-0 left-0 right-0 z-20 p-4 lg:p-8">
            <div class="flex gap-6">
                <div class="hidden sm:block flex-shrink-0 w-40 lg:w-52 -mb-20">
                    <div class="aspect-[2/3] bg-viewlix-gray rounded-xl border border-viewlix-border overflow-hidden shadow-2xl">
                        @if(!empty($tvShow['poster_path']))
                        <img src="{{ $posterUrl }}" alt="{{ $tvShow['name'] }}" class="w-full h-full object-cover">
                        @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="flex-1">
                    <h1 class="text-3xl lg:text-5xl font-bold text-white mb-3">{{ $tvShow['name'] ?? 'Unknown Title' }}</h1>
                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-400 mb-4">
                        <span class="px-2 py-1 bg-viewlix-red text-white text-xs font-semibold rounded">HD</span>
                        @if(!empty($tvShow['first_air_date']))
                        <span>{{ \Carbon\Carbon::parse($tvShow['first_air_date'])->format('Y') }}{{ !empty($tvShow['last_air_date']) && $tvShow['status'] !== 'Ended' ? ' - Present' : '' }}</span>
                        <span>•</span>
                        @endif
                        @if(!empty($tvShow['number_of_seasons']))
                        <span>{{ $tvShow['number_of_seasons'] }} Season{{ $tvShow['number_of_seasons'] > 1 ? 's' : '' }}</span>
                        <span>•</span>
                        @endif
                        @if(!empty($tvShow['genres']))
                        <span>{{ implode(', ', array_column($tvShow['genres'], 'name')) }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex items-center gap-1 text-yellow-500">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="font-semibold text-white">{{ number_format($tvShow['vote_average'] ?? 0, 1) }}</span>
                        </div>
                        <span class="text-gray-500">/10</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Action Buttons --}}
    <div class="flex flex-wrap items-center gap-3 mb-8 sm:ml-52 lg:ml-60 pt-8 sm:pt-0">
        <a href="{{ route('tv-show.watch', ['slug' => $tvShow['id'] . '-' . \Illuminate\Support\Str::slug($tvShow['name'] ?? 'tv'), 'season' => 1, 'episode' => 1]) }}" class="flex items-center gap-2 px-6 py-3 bg-viewlix-red hover:bg-viewlix-red-dark text-white font-semibold rounded-xl transition-colors">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
            </svg>
            Watch S1 E1
        </a>
        <button 
            id="downloadBtn"
            onclick="openTVDownloadModal(mediaData.id, mediaData.type, mediaData.title, {{ $tvShow['number_of_seasons'] ?? 1 }})"
            class="flex items-center gap-2 px-6 py-3 bg-viewlix-gray hover:bg-viewlix-gray-light text-white font-semibold rounded-xl border border-viewlix-border transition-colors"
            title="Download"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Download
        </button>
        @auth
        <button 
            id="favoriteBtn"
            onclick="toggleFavorite()"
            class="flex items-center gap-2 px-6 py-3 bg-viewlix-gray hover:bg-viewlix-gray-light text-white font-semibold rounded-xl border border-viewlix-border transition-colors"
        >
            <svg id="favoriteIcon" class="w-5 h-5 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <span id="favoriteBtnText">Add to Favorites</span>
        </button>
        @else
        <a href="{{ route('login') }}" class="flex items-center gap-2 px-6 py-3 bg-viewlix-gray hover:bg-viewlix-gray-light text-white font-semibold rounded-xl border border-viewlix-border transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            Add to Favorites
        </a>
        @endauth
        <button 
            onclick="openShare('{{ addslashes($tvShow['name'] ?? 'TV Show') }}', 'Check out {{ addslashes($tvShow['name'] ?? 'this TV show') }} on Viewlix!', '{{ url()->current() }}')"
            class="flex items-center justify-center w-12 h-12 bg-viewlix-gray hover:bg-viewlix-gray-light text-white rounded-xl border border-viewlix-border transition-colors"
            title="Share"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
            </svg>
        </button>
    </div>
    
    {{-- Overview --}}
    <div class="mb-8">
        <h2 class="text-xl font-bold text-white mb-4">Overview</h2>
        <p class="text-gray-400 leading-relaxed max-w-3xl">
            {{ $tvShow['overview'] ?? 'No overview available.' }}
        </p>
    </div>
    
    {{-- Seasons --}}
    @if(!empty($tvShow['seasons']))
    <div class="mb-8">
        <h2 class="text-xl font-bold text-white mb-4">Seasons</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($tvShow['seasons'] as $season)
            @if($season['season_number'] > 0)
                <a href="{{ route('tv-show.season', ['slug' => $tvShow['id'] . '-' . \Illuminate\Support\Str::slug($tvShow['name']), 'season' => $season['season_number']]) }}" 
               class="group bg-viewlix-gray rounded-xl overflow-hidden border border-viewlix-border hover:border-viewlix-red transition-colors">
                <div class="aspect-[2/3] bg-viewlix-darker overflow-hidden">
                    @if(!empty($season['poster_path']))
                    <img src="{{ config('services.tmdb.image_base_url') }}/w342{{ $season['poster_path'] }}" alt="Season {{ $season['season_number'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                    <div class="w-full h-full flex items-center justify-center">
                        <span class="text-4xl font-bold text-gray-600">{{ $season['season_number'] }}</span>
                    </div>
                    @endif
                </div>
                <div class="p-3">
                    <h3 class="text-sm font-medium text-white truncate">{{ $season['name'] }}</h3>
                    <p class="text-xs text-gray-500">{{ $season['episode_count'] }} Episodes</p>
                </div>
            </a>
            @endif
            @endforeach
        </div>
    </div>
    @endif
    
    {{-- Cast --}}
    @if(!empty($credits['cast']))
    <div class="mb-8">
        <h2 class="text-xl font-bold text-white mb-4">Cast</h2>
        <div class="flex gap-4 overflow-x-auto pb-4 scrollbar-hide">
            @foreach(array_slice($credits['cast'], 0, 10) as $cast)
            <div class="flex-shrink-0 text-center w-24">
                <div class="w-20 h-20 mx-auto bg-viewlix-gray rounded-full mb-2 overflow-hidden">
                    @if(!empty($cast['profile_path']))
                    <img src="{{ config('services.tmdb.image_base_url') }}/w185{{ $cast['profile_path'] }}" alt="{{ $cast['name'] }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                        </svg>
                    </div>
                    @endif
                </div>
                <p class="text-sm text-white font-medium truncate">{{ $cast['name'] }}</p>
                <p class="text-xs text-gray-500 truncate">{{ $cast['character'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    
    {{-- Similar TV Shows --}}
    @if(!empty($similar))
    <div class="mb-8">
        <h2 class="text-xl font-bold text-white mb-4">Similar TV Shows</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach(array_slice($similar, 0, 6) as $item)
                @include('partials.media-card', ['item' => $item, 'type' => 'tv'])
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
const mediaData = {
    id: {{ $tvShow['id'] }},
    type: 'tv',
    title: @json($tvShow['name'] ?? 'Unknown'),
    posterPath: @json($tvShow['poster_path'] ?? null),
    backdropPath: @json($tvShow['backdrop_path'] ?? null),
    voteAverage: {{ $tvShow['vote_average'] ?? 'null' }},
    releaseDate: @json($tvShow['first_air_date'] ?? null)
};

let isFavorited = false;

@auth
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const response = await fetch(`{{ route('favorites.check') }}?media_id=${mediaData.id}&media_type=${mediaData.type}`);
        const data = await response.json();
        isFavorited = data.favorited;
        if (typeof updateFavoriteButton === 'function') {
            updateFavoriteButton();
        } else {
            // External script may not yet be loaded; retry shortly
            setTimeout(() => { if (typeof updateFavoriteButton === 'function') updateFavoriteButton(); }, 250);
        }
    } catch (error) {
        console.error('Error checking favorite status:', error);
    }
});
@endauth

function updateFavoriteButton() {
    // Deprecated inline implementation moved to external file.
    // This placeholder exists so calls from Blade won't throw if the external script
    // hasn't loaded yet. The real implementation lives in public/js/tv-show-detail.js
    const fn = window.updateFavoriteButton;
    if (typeof fn === 'function') {
        return fn();
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
        updateFavoriteButton();
    } catch (error) {
        console.error('Error toggling favorite:', error);
    }
}
</script>
<script src="{{ asset('js/tv-show-detail.js') }}"></script>
@endpush

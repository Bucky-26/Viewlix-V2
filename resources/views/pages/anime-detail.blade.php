@extends('layouts.app')

@section('title', ($anime['name'] ?? 'Anime') . ' - Viewlix')

@section('content')
<div class="min-h-[calc(100vh-200px)]">
    {{-- Hero/Banner Section --}}
    <div class="relative -mx-4 lg:-mx-6 -mt-4 lg:-mt-6 mb-8">
        <div class="aspect-[21/9] bg-viewlix-darker relative overflow-hidden">
            @if(!empty($anime['backdrop_path']))
            <img src="{{ $backdropUrl }}" alt="{{ $anime['name'] }}" class="absolute inset-0 w-full h-full object-cover">
            @endif
            <div class="absolute inset-0 bg-gradient-to-r from-viewlix-dark via-viewlix-dark/80 to-transparent z-10"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-viewlix-dark via-transparent to-transparent z-10"></div>
        </div>
        
        {{-- Content Overlay --}}
        <div class="absolute bottom-0 left-0 right-0 z-20 p-4 lg:p-8">
            <div class="flex gap-6">
                <div class="hidden sm:block flex-shrink-0 w-40 lg:w-52 -mb-20">
                    <div class="aspect-[2/3] bg-viewlix-gray rounded-xl border border-viewlix-border overflow-hidden shadow-2xl">
                        @if(!empty($anime['poster_path']))
                        <img src="{{ $posterUrl }}" alt="{{ $anime['name'] }}" class="w-full h-full object-cover">
                        @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="flex-1">
                    <h1 class="text-3xl lg:text-5xl font-bold text-white mb-3">{{ $anime['name'] ?? 'Unknown Title' }}</h1>
                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-400 mb-4">
                        <span class="px-2 py-1 bg-viewlix-red text-white text-xs font-semibold rounded">SUB</span>
                        @if(!empty($anime['first_air_date']))
                        <span>{{ \Carbon\Carbon::parse($anime['first_air_date'])->format('Y') }}</span>
                        <span>•</span>
                        @endif
                        @if(!empty($anime['number_of_episodes']))
                        <span>{{ $anime['number_of_episodes'] }} Episodes</span>
                        <span>•</span>
                        @endif
                        @if(!empty($anime['genres']))
                        <span>{{ implode(', ', array_column($anime['genres'], 'name')) }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex items-center gap-1 text-yellow-500">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="font-semibold text-white">{{ number_format($anime['vote_average'] ?? 0, 1) }}</span>
                        </div>
                        <span class="text-gray-500">/10</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Action Buttons --}}
    <div class="flex flex-wrap items-center gap-3 mb-8 sm:ml-52 lg:ml-60 pt-8 sm:pt-0">
        <a href="{{ route('anime.watch', ['id' => $anime['id'], 'season' => 1, 'episode' => 1]) }}" class="flex items-center gap-2 px-6 py-3 bg-viewlix-red hover:bg-viewlix-red-dark text-white font-semibold rounded-xl transition-colors">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
            </svg>
            Watch Episode 1
        </a>
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
            onclick="openShare('{{ addslashes($anime['name'] ?? 'Anime') }}', 'Check out {{ addslashes($anime['name'] ?? 'this anime') }} on Viewlix!', '{{ url()->current() }}')"
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
        <h2 class="text-xl font-bold text-white mb-4">Synopsis</h2>
        <p class="text-gray-400 leading-relaxed max-w-3xl">
            {{ $anime['overview'] ?? 'No synopsis available.' }}
        </p>
    </div>
    
    {{-- Seasons --}}
    @if(!empty($anime['seasons']))
    <div class="mb-8">
        <h2 class="text-xl font-bold text-white mb-4">Seasons</h2>
        <div class="flex flex-wrap gap-3 mb-6">
            @foreach($anime['seasons'] as $season)
            @if($season['season_number'] > 0)
            <a href="{{ route('anime.show', ['id' => $anime['id'], 'season' => $season['season_number']]) }}" 
               class="px-4 py-2 text-sm font-medium rounded-xl transition-colors {{ ($currentSeason ?? 1) == $season['season_number'] ? 'bg-viewlix-red text-white' : 'bg-viewlix-gray text-gray-300 hover:bg-viewlix-gray-light' }}">
                Season {{ $season['season_number'] }}
            </a>
            @endif
            @endforeach
        </div>
    </div>
    @endif
    
    {{-- Episodes Grid --}}
    @if(!empty($episodes))
    <div class="mb-8">
        <h2 class="text-xl font-bold text-white mb-4">Episodes</h2>
        <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 xl:grid-cols-12 gap-2">
            @foreach($episodes as $episode)
            <a href="{{ route('anime.watch', ['id' => $anime['id'], 'season' => $currentSeason ?? 1, 'episode' => $episode['episode_number']]) }}" 
               class="aspect-square flex items-center justify-center bg-viewlix-gray rounded-lg border border-viewlix-border hover:border-viewlix-red hover:bg-viewlix-red text-gray-400 hover:text-white font-medium transition-colors"
               title="{{ $episode['name'] ?? 'Episode '.$episode['episode_number'] }}">
                {{ $episode['episode_number'] }}
            </a>
            @endforeach
        </div>
    </div>
    @endif
    
    {{-- Similar Anime --}}
    @if(!empty($similar))
    <div class="mb-8">
        <h2 class="text-xl font-bold text-white mb-4">Similar Anime</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach(array_slice($similar, 0, 6) as $item)
                @include('partials.media-card', ['item' => $item, 'type' => 'anime'])
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
const mediaData = {
    id: {{ $anime['id'] }},
    type: 'anime',
    title: @json($anime['name'] ?? 'Unknown'),
    posterPath: @json($anime['poster_path'] ?? null),
    backdropPath: @json($anime['backdrop_path'] ?? null),
    voteAverage: {{ $anime['vote_average'] ?? 'null' }},
    releaseDate: @json($anime['first_air_date'] ?? null)
};

let isFavorited = false;

@auth
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const response = await fetch(`{{ route('favorites.check') }}?media_id=${mediaData.id}&media_type=${mediaData.type}`);
        const data = await response.json();
        isFavorited = data.favorited;
        updateFavoriteButton();
    } catch (error) {
        console.error('Error checking favorite status:', error);
    }
});
@endauth

function updateFavoriteButton() {
    const icon = document.getElementById('favoriteIcon');
    const text = document.getElementById('favoriteBtnText');
    if (!icon || !text) return;
    
    if (isFavorited) {
        icon.setAttribute('fill', 'currentColor');
        icon.classList.add('text-viewlix-red');
        text.textContent = 'Remove from Favorites';
    } else {
        icon.setAttribute('fill', 'none');
        icon.classList.remove('text-viewlix-red');
        text.textContent = 'Add to Favorites';
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
@endpush

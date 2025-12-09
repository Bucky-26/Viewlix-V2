@extends('layouts.app')

@section('title', ($movie['title'] ?? 'Movie') . ' - Viewlix')

@section('content')
<div class="min-h-[calc(100vh-200px)]">
    {{-- Hero/Banner Section --}}
    <div class="relative -mx-4 lg:-mx-6 -mt-4 lg:-mt-6 mb-8">
        <div class="aspect-[21/9] bg-viewlix-darker relative overflow-hidden">
            {{-- Backdrop Image --}}
            @if(!empty($movie['backdrop_path']))
            <img src="{{ $backdropUrl }}" alt="{{ $movie['title'] }}" class="absolute inset-0 w-full h-full object-cover">
            @endif
            <div class="absolute inset-0 bg-gradient-to-r from-viewlix-dark via-viewlix-dark/80 to-transparent z-10"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-viewlix-dark via-transparent to-transparent z-10"></div>
        </div>
        
        {{-- Content Overlay --}}
        <div class="absolute bottom-0 left-0 right-0 z-20 p-4 lg:p-8">
            <div class="flex gap-6">
                {{-- Poster --}}
                <div class="hidden sm:block flex-shrink-0 w-40 lg:w-52 -mb-20">
                    <div class="aspect-[2/3] bg-viewlix-gray rounded-xl border border-viewlix-border overflow-hidden shadow-2xl">
                        @if(!empty($movie['poster_path']))
                        <img src="{{ $posterUrl }}" alt="{{ $movie['title'] }}" class="w-full h-full object-cover">
                        @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        @endif
                    </div>
                </div>
                
                {{-- Info --}}
                <div class="flex-1">
                    <h1 class="text-3xl lg:text-5xl font-bold text-white mb-3">{{ $movie['title'] ?? 'Unknown Title' }}</h1>
                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-400 mb-4">
                        <span class="px-2 py-1 bg-viewlix-red text-white text-xs font-semibold rounded">HD</span>
                        @if(!empty($movie['release_date']))
                        <span>{{ \Carbon\Carbon::parse($movie['release_date'])->format('Y') }}</span>
                        <span>•</span>
                        @endif
                        @if(!empty($movie['runtime']))
                        <span>{{ floor($movie['runtime'] / 60) }}h {{ $movie['runtime'] % 60 }}m</span>
                        <span>•</span>
                        @endif
                        @if(!empty($movie['genres']))
                        <span>{{ implode(', ', array_column($movie['genres'], 'name')) }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 mb-4">
                        <div class="flex items-center gap-1 text-yellow-500">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="font-semibold text-white">{{ number_format($movie['vote_average'] ?? 0, 1) }}</span>
                        </div>
                        <span class="text-gray-500">/10</span>
                        @if(!empty($movie['vote_count']))
                        <span class="text-gray-500 text-sm">({{ number_format($movie['vote_count']) }} votes)</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Action Buttons --}}
    <div class="flex flex-wrap items-center gap-3 mb-8 sm:ml-52 lg:ml-60 pt-8 sm:pt-0">
        <a href="{{ route('movie.watch', ['id' => $movie['id']]) }}" class="flex items-center gap-2 px-6 py-3 bg-viewlix-red hover:bg-viewlix-red-dark text-white font-semibold rounded-xl transition-colors">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
            </svg>
            Watch Now
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
            onclick="openShare('{{ addslashes($movie['title'] ?? 'Movie') }}', 'Check out {{ addslashes($movie['title'] ?? 'this movie') }} on Viewlix!', '{{ url()->current() }}')"
            class="flex items-center justify-center w-12 h-12 bg-viewlix-gray hover:bg-viewlix-gray-light text-white rounded-xl border border-viewlix-border transition-colors"
            title="Share"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
            </svg>
        </button>
    </div>
    
    {{-- Tagline --}}
    @if(!empty($movie['tagline']))
    <div class="mb-6">
        <p class="text-xl text-gray-400 italic">"{{ $movie['tagline'] }}"</p>
    </div>
    @endif
    
    {{-- Overview --}}
    <div class="mb-8">
        <h2 class="text-xl font-bold text-white mb-4">Overview</h2>
        <p class="text-gray-400 leading-relaxed max-w-3xl">
            {{ $movie['overview'] ?? 'No overview available.' }}
        </p>
    </div>
    
    {{-- Movie Info --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
        @if(!empty($movie['status']))
        <div class="bg-viewlix-gray rounded-xl p-4 border border-viewlix-border">
            <p class="text-gray-500 text-sm mb-1">Status</p>
            <p class="text-white font-medium">{{ $movie['status'] }}</p>
        </div>
        @endif
        @if(!empty($movie['release_date']))
        <div class="bg-viewlix-gray rounded-xl p-4 border border-viewlix-border">
            <p class="text-gray-500 text-sm mb-1">Release Date</p>
            <p class="text-white font-medium">{{ \Carbon\Carbon::parse($movie['release_date'])->format('M d, Y') }}</p>
        </div>
        @endif
        @if(!empty($movie['budget']) && $movie['budget'] > 0)
        <div class="bg-viewlix-gray rounded-xl p-4 border border-viewlix-border">
            <p class="text-gray-500 text-sm mb-1">Budget</p>
            <p class="text-white font-medium">${{ number_format($movie['budget']) }}</p>
        </div>
        @endif
        @if(!empty($movie['revenue']) && $movie['revenue'] > 0)
        <div class="bg-viewlix-gray rounded-xl p-4 border border-viewlix-border">
            <p class="text-gray-500 text-sm mb-1">Revenue</p>
            <p class="text-white font-medium">${{ number_format($movie['revenue']) }}</p>
        </div>
        @endif
    </div>
    
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
    
    {{-- Similar Movies --}}
    @if(!empty($similar))
    <div class="mb-8">
        <h2 class="text-xl font-bold text-white mb-4">Similar Movies</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach(array_slice($similar, 0, 6) as $item)
                @include('partials.media-card', ['item' => $item, 'type' => 'movie'])
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
const mediaData = {
    id: {{ $movie['id'] }},
    type: 'movie',
    title: @json($movie['title'] ?? 'Unknown'),
    posterPath: @json($movie['poster_path'] ?? null),
    backdropPath: @json($movie['backdrop_path'] ?? null),
    voteAverage: {{ $movie['vote_average'] ?? 'null' }},
    releaseDate: @json($movie['release_date'] ?? null)
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

@extends('layouts.app')

@section('title', 'My Favorites - Viewlix')

@section('content')
<div class="min-h-[calc(100vh-200px)]">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">My Favorites</h1>
        <p class="text-gray-400">Your personal watchlist</p>
    </div>
    
    {{-- Filter Tabs --}}
    <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-2">
        <a href="{{ route('favorites') }}" 
           class="px-4 py-2 rounded-xl text-sm font-medium transition-colors whitespace-nowrap {{ $activeType === 'all' ? 'bg-viewlix-red text-white' : 'bg-viewlix-gray text-gray-400 hover:text-white' }}">
            All
        </a>
        <a href="{{ route('favorites', ['type' => 'movie']) }}" 
           class="px-4 py-2 rounded-xl text-sm font-medium transition-colors whitespace-nowrap {{ $activeType === 'movie' ? 'bg-viewlix-red text-white' : 'bg-viewlix-gray text-gray-400 hover:text-white' }}">
            Movies
        </a>
        <a href="{{ route('favorites', ['type' => 'tv']) }}" 
           class="px-4 py-2 rounded-xl text-sm font-medium transition-colors whitespace-nowrap {{ $activeType === 'tv' ? 'bg-viewlix-red text-white' : 'bg-viewlix-gray text-gray-400 hover:text-white' }}">
            TV Shows
        </a>
        <a href="{{ route('favorites', ['type' => 'anime']) }}" 
           class="px-4 py-2 rounded-xl text-sm font-medium transition-colors whitespace-nowrap {{ $activeType === 'anime' ? 'bg-viewlix-red text-white' : 'bg-viewlix-gray text-gray-400 hover:text-white' }}">
            Anime
        </a>
    </div>
    
    @if($favorites->count() > 0)
        {{-- Favorites Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach($favorites as $favorite)
                @php
                    $routeName = match($favorite->media_type) {
                        'movie' => 'movie.show',
                        'tv' => 'tv-show.show',
                        'anime' => 'anime.show',
                        default => 'movie.show'
                    };
                @endphp
                <div class="group relative bg-viewlix-gray rounded-xl overflow-hidden transition-transform hover:scale-[1.02]">
                    <a href="{{ route($routeName, $favorite->media_id) }}">
                        <div class="aspect-[2/3] relative">
                            <img 
                                src="{{ $favorite->poster_url }}" 
                                alt="{{ $favorite->title }}"
                                class="w-full h-full object-cover"
                                loading="lazy"
                                onerror="this.src='https://via.placeholder.com/500x750/1a1a2e/e94560?text=No+Image'"
                            >
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            
                            {{-- Rating Badge --}}
                            @if($favorite->vote_average)
                            <div class="absolute top-2 left-2 px-2 py-1 bg-black/70 rounded-lg flex items-center gap-1">
                                <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="text-xs text-white font-medium">{{ number_format($favorite->vote_average, 1) }}</span>
                            </div>
                            @endif
                            
                            {{-- Media Type Badge --}}
                            <div class="absolute top-2 right-2 px-2 py-1 bg-viewlix-red/80 rounded-lg">
                                <span class="text-xs text-white font-medium uppercase">{{ $favorite->media_type }}</span>
                            </div>
                        </div>
                        <div class="p-3">
                            <h3 class="text-white font-medium text-sm truncate">{{ $favorite->title }}</h3>
                            @if($favorite->release_date)
                                <p class="text-gray-400 text-xs mt-1">{{ \Carbon\Carbon::parse($favorite->release_date)->format('Y') }}</p>
                            @endif
                        </div>
                    </a>
                    
                    {{-- Remove Button --}}
                    <button 
                        onclick="removeFavorite({{ $favorite->media_id }}, '{{ $favorite->media_type }}')"
                        class="absolute bottom-3 right-3 p-2 bg-viewlix-red/80 hover:bg-viewlix-red rounded-lg opacity-0 group-hover:opacity-100 transition-opacity"
                        title="Remove from favorites"
                    >
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            @endforeach
        </div>
        
        {{-- Pagination --}}
        <div class="mt-8">
            {{ $favorites->links() }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-24 h-24 bg-viewlix-gray rounded-full flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-white mb-2">No favorites yet</h3>
            <p class="text-gray-400 mb-6 max-w-md">Start adding movies, TV shows, and anime to your favorites to see them here.</p>
            <a href="{{ route('movies') }}" class="px-6 py-3 bg-viewlix-red hover:bg-viewlix-red-dark text-white font-semibold rounded-xl transition-colors">
                Browse Content
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
async function removeFavorite(mediaId, mediaType) {
    if (!confirm('Remove from favorites?')) return;
    
    try {
        const response = await fetch('{{ route("favorites.destroy") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                media_id: mediaId,
                media_type: mediaType
            })
        });
        
        if (response.ok) {
            window.location.reload();
        }
    } catch (error) {
        console.error('Error removing favorite:', error);
    }
}
</script>
@endpush

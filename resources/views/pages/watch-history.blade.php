@extends('layouts.app')

@section('title', 'Watch History - Viewlix')

@section('content')
<div class="min-h-[calc(100vh-200px)]" x-data="watchHistoryPage()" x-init="init()">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Watch History</h1>
            <p class="text-gray-400">Keep track of what you've watched</p>
        </div>
        
        <button 
            x-show="items.length > 0"
            x-on:click="clearHistory()"
            class="px-4 py-2 bg-viewlix-gray hover:bg-red-600 text-gray-400 hover:text-white rounded-xl transition-colors flex items-center gap-2"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Clear History
        </button>
    </div>
    
    {{-- Filter Tabs --}}
    <div class="flex items-center gap-2 mb-6 overflow-x-auto pb-2">
        <button 
            x-on:click="activeType = 'all'"
            :class="activeType === 'all' ? 'bg-viewlix-red text-white' : 'bg-viewlix-gray text-gray-400 hover:text-white'"
            class="px-4 py-2 rounded-xl text-sm font-medium transition-colors whitespace-nowrap">
            All
        </button>
        <button 
            x-on:click="activeType = 'movie'"
            :class="activeType === 'movie' ? 'bg-viewlix-red text-white' : 'bg-viewlix-gray text-gray-400 hover:text-white'"
            class="px-4 py-2 rounded-xl text-sm font-medium transition-colors whitespace-nowrap">
            Movies
        </button>
        <button 
            x-on:click="activeType = 'tv'"
            :class="activeType === 'tv' ? 'bg-viewlix-red text-white' : 'bg-viewlix-gray text-gray-400 hover:text-white'"
            class="px-4 py-2 rounded-xl text-sm font-medium transition-colors whitespace-nowrap">
            TV Shows
        </button>
        <button 
            x-on:click="activeType = 'anime'"
            :class="activeType === 'anime' ? 'bg-viewlix-red text-white' : 'bg-viewlix-gray text-gray-400 hover:text-white'"
            class="px-4 py-2 rounded-xl text-sm font-medium transition-colors whitespace-nowrap">
            Anime
        </button>
    </div>
    
    {{-- Loading State --}}
    <div x-show="loading" class="flex justify-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-4 border-viewlix-red border-t-transparent"></div>
    </div>
    
    {{-- History Grid --}}
    <div x-show="!loading && filteredItems.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
        <template x-for="item in filteredItems" :key="item.id">
            <div class="group relative bg-viewlix-gray rounded-xl overflow-hidden transition-transform hover:scale-[1.02]">
                <a :href="getDetailUrl(item)">
                    <div class="aspect-[2/3] relative">
                        <img 
                            :src="getPosterUrl(item)"
                            :alt="item.title"
                            class="w-full h-full object-cover"
                            loading="lazy"
                            onerror="this.src='https://via.placeholder.com/500x750/1a1a2e/e94560?text=No+Image'"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>
                        
                        {{-- Progress Bar --}}
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-700">
                            <div class="h-full bg-viewlix-red transition-all" :style="'width: ' + getProgressPercent(item) + '%'"></div>
                        </div>
                        
                        {{-- Play Button Overlay --}}
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="w-14 h-14 bg-viewlix-red/90 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M8 5v14l11-7z"/>
                                </svg>
                            </div>
                        </div>
                        
                        {{-- Media Type Badge --}}
                        <div class="absolute top-2 right-2 px-2 py-1 bg-viewlix-red/80 rounded-lg">
                            <span class="text-xs text-white font-medium uppercase" x-text="item.type"></span>
                        </div>
                        
                        {{-- Completed Badge --}}
                        <div x-show="getProgressPercent(item) >= 90" class="absolute top-2 left-2 px-2 py-1 bg-green-500/80 rounded-lg">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                    <div class="p-3">
                        <h3 class="text-white font-medium text-sm truncate" x-text="item.title"></h3>
                        <div class="flex items-center justify-between mt-1">
                            <p class="text-gray-400 text-xs" x-text="getEpisodeText(item)"></p>
                            <p class="text-gray-500 text-xs" x-text="formatTimeAgo(item.last_updated)"></p>
                        </div>
                    </div>
                </a>
                
                {{-- Continue Watching Button --}}
                <a 
                    x-show="getProgressPercent(item) < 90"
                    :href="getWatchUrl(item)"
                    class="absolute bottom-14 left-3 right-3 py-2 bg-viewlix-red hover:bg-viewlix-red-dark text-white text-xs font-medium rounded-lg text-center opacity-0 group-hover:opacity-100 transition-opacity"
                >
                    Continue Watching
                </a>
                
                {{-- Remove Button --}}
                <button 
                    x-on:click.prevent="removeItem(item)"
                    class="absolute top-10 right-2 p-2 bg-black/50 hover:bg-red-600 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity"
                    title="Remove from history"
                >
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </template>
    </div>
    
    {{-- Empty State --}}
    <div x-show="!loading && filteredItems.length === 0" class="flex flex-col items-center justify-center py-20 text-center">
        <div class="w-24 h-24 bg-viewlix-gray rounded-full flex items-center justify-center mb-6">
            <svg class="w-12 h-12 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h3 class="text-xl font-semibold text-white mb-2">No watch history</h3>
        <p class="text-gray-400 mb-6 max-w-md">Start watching movies, TV shows, or anime to build your watch history.</p>
        <a href="{{ route('movies') }}" class="px-6 py-3 bg-viewlix-red hover:bg-viewlix-red-dark text-white font-semibold rounded-xl transition-colors">
            Start Watching
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/watch-history.js') }}"></script>
@endpush

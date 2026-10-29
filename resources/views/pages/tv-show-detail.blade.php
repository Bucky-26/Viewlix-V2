@extends('layouts.app')
@section('title', ($tvShow['name'] ?? 'TV Show') . ' - Viewlix')
@section('content')
<div class="relative min-h-[calc(100vh-200px)] -mx-4 lg:-mx-6 -mt-4 lg:-mt-6 overflow-hidden w-full" x-data="tvShowPage()">
    {{-- Background --}}
    <div class="absolute inset-0 z-0">
        @if(!empty($tvShow['backdrop_path']))
        <img src="{{ $backdropUrl }}" alt="{{ $tvShow['name'] }}" class="w-full h-full object-cover opacity-50 filter saturate-150">
        @endif
        <div class="absolute inset-0 bg-gradient-to-r from-[#08080a] via-[#08080a]/90 to-transparent"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-[#08080a] via-[#08080a]/40 to-transparent"></div>
    </div>

    {{-- Content --}}
    <div class="relative z-10 w-full max-w-[1400px] mx-auto px-4 py-8 lg:px-12 lg:py-16 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16">
        
        {{-- Left Column (Details) --}}
        <div class="lg:col-span-7 flex flex-col justify-start pt-4 lg:pt-8 pb-12 lg:sticky lg:top-24 self-start lg:max-h-[calc(100vh-6rem)] lg:overflow-y-auto scrollbar-hide">
            {{-- Network Logo / Provider --}}
            <div class="mb-4 h-8 flex items-start">
                <img src="/viewlix.png" alt="Viewlix" class="h-6 object-contain opacity-90">
            </div>

            <h1 class="text-5xl lg:text-7xl font-bold text-white mb-4 tracking-tight drop-shadow-lg" style="font-family: 'Instrument Sans', sans-serif;">{{ $tvShow['name'] }}</h1>
            
            <p class="text-gray-300 text-lg leading-snug max-w-2xl mb-6 drop-shadow-md">
                {{ Str::limit($tvShow['overview'], 250) }}
            </p>

            {{-- Meta Info --}}
            <div class="flex flex-wrap items-center gap-6 text-sm font-bold text-white mb-6 drop-shadow-md">
                @if(!empty($tvShow['genres']))
                <span>{{ $tvShow['genres'][0]['name'] ?? 'Drama' }}</span>
                @endif
                @if(!empty($tvShow['first_air_date']))
                <span>{{ \Carbon\Carbon::parse($tvShow['first_air_date'])->format('Y') }}</span>
                @endif
                @if(!empty($tvShow['number_of_seasons']))
                <span>{{ $tvShow['number_of_seasons'] }} Season{{ $tvShow['number_of_seasons'] > 1 ? 's' : '' }}</span>
                @endif
                <div class="flex items-center gap-1 text-yellow-500">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    <span>{{ number_format($tvShow['vote_average'] ?? 0, 1) }}</span>
                </div>
            </div>

            {{-- Progress bar indicator --}}
            <div class="max-w-md mb-6">
                <div class="flex items-center gap-4 text-sm font-medium text-gray-300 mb-2">
                    <div class="flex-1 h-1 bg-white/20 rounded-full overflow-hidden relative">
                        <div class="absolute left-0 top-0 bottom-0 w-1/3 bg-red-600 rounded-full shadow-[0_0_10px_rgba(220,38,38,0.8)]"></div>
                    </div>
                    @if(count($episodes) > 0)
                    <span class="text-white drop-shadow-sm font-semibold">S{{ str_pad($firstSeasonNumber, 2, '0', STR_PAD_LEFT) }} E01 · {{ $episodes[0]['name'] ?? 'Episode 1' }}</span>
                    @endif
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap items-center gap-4 mb-8">
                <a href="{{ route('tv-show.watch', ['slug' => $tvShow['id'] . '-' . \Illuminate\Support\Str::slug($tvShow['name'] ?? 'tv'), 'season' => $firstSeasonNumber, 'episode' => 1]) }}" class="flex items-center gap-2 px-8 py-3 bg-white text-black font-extrabold rounded-full hover:bg-gray-200 transition-transform hover:scale-105 active:scale-95 shadow-lg">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                    </svg>
                    Resume
                </a>
                
                <button @click="toggleFavorite()" class="flex items-center gap-2 px-8 py-3 bg-[#18181f]/80 hover:bg-[#252530]/80 text-white font-bold rounded-full backdrop-blur-md transition-transform hover:scale-105 active:scale-95 border border-white/5 shadow-lg">
                    <span x-text="isFavorited ? 'ADDED TO WATCHLIST' : 'WATCHLIST'"></span>
                </button>
            </div>

            {{-- Seasons List (Pills) --}}
            @if(!empty($tvShow['seasons']))
            <div class="flex flex-wrap gap-3">
                @foreach($tvShow['seasons'] as $season)
                    @if($season['season_number'] > 0)
                        <button 
                            @click="loadSeason({{ $season['season_number'] }})"
                            :class="activeSeason === {{ $season['season_number'] }} ? 'bg-red-600 text-white border-red-600 shadow-[0_0_15px_rgba(220,38,38,0.5)]' : 'bg-[#1a1c23]/60 text-gray-400 border-white/5 hover:bg-[#2a2c33]/80 hover:text-white'"
                            class="px-6 py-2.5 rounded-2xl text-xs font-bold border transition-all duration-300 backdrop-blur-md uppercase tracking-wider"
                        >
                            SEASON {{ $season['season_number'] }}
                        </button>
                    @endif
                @endforeach
            </div>
            @endif
        </div>

        {{-- Right Column (Episodes List) --}}
        <div class="lg:col-span-5 flex flex-col relative z-20 pb-20">
            <div class="space-y-4 py-4 lg:py-10 w-full" id="episodes-container" x-ref="episodesContainer">
                {{-- Template for episodes --}}
                <template x-for="(ep, index) in episodes" :key="ep.id">
                    <div 
                        @mouseenter="hoveredEpisode = ep.id"
                        @mouseleave="hoveredEpisode = null"
                        class="relative cursor-pointer group"
                    >
                        {{-- Always visible collapsed row --}}
                        <div class="flex items-center gap-4 text-gray-300 p-2 hover:bg-white/5 rounded-2xl transition-colors">
                            <div class="w-32 h-20 sm:w-40 sm:h-24 rounded-lg overflow-hidden bg-gray-800 flex-shrink-0 relative shadow-md">
                                <img x-show="ep.still_path" :src="'{{ config('services.tmdb.image_base_url') }}/w300' + ep.still_path" class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-bold text-white truncate">
                                    S<span x-text="String(ep.season_number).padStart(2, '0')"></span> E<span x-text="String(ep.episode_number).padStart(2, '0')"></span> - <span x-text="ep.name"></span>
                                </h4>
                            </div>
                            <div class="text-xs font-semibold whitespace-nowrap text-right flex flex-col items-end gap-1">
                                <div x-text="ep.runtime ? ep.runtime + 'm' : '45m'" class="text-gray-400"></div>
                                <div class="text-yellow-500 flex items-center gap-1"><span x-text="ep.vote_average ? ep.vote_average.toFixed(1) : ''"></span> <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg></div>
                            </div>
                        </div>

                        {{-- Expanded Overlay Card (Absolute) --}}
                        <div 
                            x-show="isExpanded(ep.id)"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-x-4"
                            x-transition:enter-end="opacity-100 translate-x-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-x-0"
                            x-transition:leave-end="opacity-0 translate-x-4"
                            class="absolute top-1/2 right-0 -translate-y-1/2 bg-white text-black rounded-xl shadow-[0_10px_40px_rgba(0,0,0,0.5)] z-50 flex flex-row items-stretch border border-gray-200 overflow-hidden"
                            style="width: 135%; min-height: 160px;"
                        >
                            {{-- Image (Left Side) --}}
                            <div class="w-[45%] relative group/play flex-shrink-0">
                                <img x-show="ep.still_path" :src="'{{ config('services.tmdb.image_base_url') }}/original' + ep.still_path" class="w-full h-full object-cover">
                                <div x-show="!ep.still_path" class="w-full h-full bg-gray-800 flex items-center justify-center text-gray-500">No Image</div>
                                <a :href="'/tv-show/{{ $tvShow['id'] }}-{{ \Illuminate\Support\Str::slug($tvShow['name'] ?? 'tv') }}/watch/' + ep.season_number + '/' + ep.episode_number" class="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 group-hover/play:opacity-100 transition-opacity">
                                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-black shadow-xl transform transition-transform group-hover/play:scale-110">
                                        <svg class="w-5 h-5 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </a>
                            </div>

                            {{-- Content (Right Side) --}}
                            <div class="w-[55%] p-4 lg:p-5 flex flex-col">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="text-sm lg:text-base font-extrabold text-black tracking-tight leading-tight pr-2">
                                        S<span x-text="String(ep.season_number).padStart(2, '0')"></span> E<span x-text="String(ep.episode_number).padStart(2, '0')"></span> · <span x-text="ep.name"></span>
                                    </h4>
                                    <div class="text-xs lg:text-sm font-bold whitespace-nowrap text-gray-500" x-text="ep.runtime ? ep.runtime + 'm' : '45m'"></div>
                                </div>
                                
                                <div class="w-full bg-gray-200 h-[2px] mb-3 rounded-full overflow-hidden flex-shrink-0">
                                    <div class="h-full bg-red-600 w-1/3"></div>
                                </div>

                                <p class="text-xs lg:text-sm text-gray-700 leading-relaxed line-clamp-3 font-medium flex-1" x-text="ep.overview || 'No overview available.'"></p>
                                
                                <div class="flex justify-end items-center text-yellow-500 font-bold mt-2 text-sm flex-shrink-0">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                    <span x-text="ep.vote_average ? ep.vote_average.toFixed(1) : 'N/A'" class="text-black"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
                
                <div x-show="episodes.length === 0" class="text-gray-400 text-center py-8">
                    No episodes found for this season.
                </div>
                <div x-show="loading" class="text-gray-400 text-center py-8">
                    Loading episodes...
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('tvShowPage', () => ({
        activeSeason: {{ $firstSeasonNumber }},
        episodes: @json($episodes),
        hoveredEpisode: null,
        loading: false,
        isFavorited: false,
        
        init() {
            @auth
            fetch(`{{ route('favorites.check') }}?media_id={{ $tvShow['id'] }}&media_type=tv`)
                .then(res => res.json())
                .then(data => {
                    this.isFavorited = data.favorited;
                });
            @endauth
        },
        
        isExpanded(id) {
            return this.hoveredEpisode === id;
        },

        async loadSeason(seasonNum) {
            this.loading = true;
            this.episodes = []; // Optional: clear current episodes for visual feedback
            this.activeSeason = seasonNum;
            
            try {
                const response = await fetch(`/tv-show/{{ $tvShow['id'] }}-{{ \Illuminate\Support\Str::slug($tvShow['name'] ?? 'tv') }}/season/${seasonNum}/episodes.json`);
                if (response.ok) {
                    const data = await response.json();
                    this.episodes = data.episodes || [];
                } else {
                    console.error("Failed to load episodes for season", seasonNum);
                }
            } catch (error) {
                console.error("Error loading season episodes:", error);
            } finally {
                this.loading = false;
            }
        },

        async toggleFavorite() {
            @auth
            try {
                const response = await fetch('{{ route("favorites.toggle") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        media_id: {{ $tvShow['id'] }},
                        media_type: 'tv',
                        title: @json($tvShow['name'] ?? 'Unknown'),
                        poster_path: @json($tvShow['poster_path'] ?? null),
                        backdrop_path: @json($tvShow['backdrop_path'] ?? null),
                        vote_average: {{ $tvShow['vote_average'] ?? 'null' }},
                        release_date: @json($tvShow['first_air_date'] ?? null)
                    })
                });
                const data = await response.json();
                this.isFavorited = data.status === 'added';
            } catch (error) {
                console.error('Error toggling favorite:', error);
            }
            @else
            window.location.href = '{{ route("login") }}';
            @endauth
        }
    }));
});
</script>
<style>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
@endpush

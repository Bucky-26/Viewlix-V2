@extends('layouts.app')

@section('title', ($tvShow['name'] ?? 'TV Show') . ' - Season ' . $season['season_number'] . ' - Viewlix')

@section('content')
<div class="min-h-[calc(100vh-200px)]">
    {{-- Header --}}
    <div class="mb-8">
        <nav class="flex items-center gap-2 text-sm text-gray-400 mb-4">
            <a href="{{ route('tv-shows') }}" class="hover:text-white transition-colors">TV Shows</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
            <a href="{{ route('tv-show.show', ['id' => $tvShow['id']]) }}" class="hover:text-white transition-colors">{{ $tvShow['name'] }}</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
            </svg>
            <span class="text-white">Season {{ $season['season_number'] }}</span>
        </nav>
        
        <div class="flex gap-6">
            {{-- Season Poster --}}
            <div class="hidden sm:block flex-shrink-0 w-40">
                <div class="aspect-[2/3] bg-viewlix-gray rounded-xl border border-viewlix-border overflow-hidden">
                    @if(!empty($season['poster_path']))
                    <img src="{{ config('services.tmdb.image_base_url') }}/w342{{ $season['poster_path'] }}" alt="Season {{ $season['season_number'] }}" class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center">
                        <span class="text-4xl font-bold text-gray-600">{{ $season['season_number'] }}</span>
                    </div>
                    @endif
                </div>
            </div>
            
            {{-- Season Info --}}
            <div class="flex-1">
                <h1 class="text-3xl font-bold text-white mb-2">{{ $tvShow['name'] }}</h1>
                <h2 class="text-xl text-gray-400 mb-4">{{ $season['name'] }}</h2>
                
                <div class="flex flex-wrap items-center gap-3 text-sm text-gray-400 mb-4">
                    @if(!empty($season['air_date']))
                    <span>{{ \Carbon\Carbon::parse($season['air_date'])->format('Y') }}</span>
                    <span>•</span>
                    @endif
                    <span>{{ count($episodes ?? []) }} Episodes</span>
                </div>
                
                @if(!empty($season['overview']))
                <p class="text-gray-400 leading-relaxed max-w-2xl">{{ $season['overview'] }}</p>
                @endif
            </div>
        </div>
    </div>
    
    {{-- Season Navigation --}}
    @if(!empty($tvShow['seasons']))
    <div class="flex flex-wrap gap-2 mb-8">
        @foreach($tvShow['seasons'] as $s)
        @if($s['season_number'] > 0)
        <a href="{{ route('tv-show.season', ['id' => $tvShow['id'], 'season' => $s['season_number']]) }}" 
           class="px-4 py-2 text-sm font-medium rounded-xl transition-colors {{ $s['season_number'] == $season['season_number'] ? 'bg-viewlix-red text-white' : 'bg-viewlix-gray text-gray-300 hover:bg-viewlix-gray-light' }}">
            Season {{ $s['season_number'] }}
        </a>
        @endif
        @endforeach
    </div>
    @endif
    
    {{-- Episodes List --}}
    <div class="space-y-3">
        @forelse($episodes ?? [] as $episode)
        <a href="{{ route('tv-show.watch', ['id' => $tvShow['id'], 'season' => $season['season_number'], 'episode' => $episode['episode_number']]) }}" 
           class="flex gap-4 p-4 bg-viewlix-gray rounded-xl border border-viewlix-border hover:border-viewlix-red transition-colors group">
            <div class="flex-shrink-0 w-40 aspect-video bg-viewlix-darker rounded-lg overflow-hidden relative">
                @if(!empty($episode['still_path']))
                <img src="{{ config('services.tmdb.image_base_url') }}/w300{{ $episode['still_path'] }}" alt="Episode {{ $episode['episode_number'] }}" class="w-full h-full object-cover">
                @else
                <div class="w-full h-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-gray-600">{{ $episode['episode_number'] }}</span>
                </div>
                @endif
                <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-sm text-gray-500 font-medium">E{{ $episode['episode_number'] }}</span>
                    <h3 class="text-white font-medium truncate">{{ $episode['name'] ?? 'Episode '.$episode['episode_number'] }}</h3>
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-500 mb-2">
                    @if(!empty($episode['runtime']))
                    <span>{{ $episode['runtime'] }} min</span>
                    @endif
                    @if(!empty($episode['air_date']))
                    <span>{{ \Carbon\Carbon::parse($episode['air_date'])->format('M d, Y') }}</span>
                    @endif
                    @if(!empty($episode['vote_average']))
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        {{ number_format($episode['vote_average'], 1) }}
                    </span>
                    @endif
                </div>
                @if(!empty($episode['overview']))
                <p class="text-sm text-gray-400 line-clamp-2">{{ $episode['overview'] }}</p>
                @endif
            </div>
        </a>
        @empty
        <div class="text-center py-12">
            <p class="text-gray-400">No episodes available.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection

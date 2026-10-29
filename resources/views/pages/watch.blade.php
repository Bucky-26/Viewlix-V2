@extends('layouts.app')

@php
    $currentEpNum = $episode ?? 1;
    $currentSeasonNum = $season ?? 1;
    $epTitle = $currentEpisode['name'] ?? null;
    $pageTitle = ($title ?? 'Watch') . ($type !== 'movie' ? " - S{$currentSeasonNum}:E{$currentEpNum}" . ($epTitle ? " \"{$epTitle}\"" : '') : '') . ' - Viewlix';
    
    // Slug / URL resolution
    $slugStr = $slug ?? ($id . '-' . \Illuminate\Support\Str::slug($title ?? 'show'));
    $showRoute = match($type) {
        'tv' => route('tv-show.show', ['slug' => $slugStr]),
        'anime' => route('anime.show', ['id' => $id]),
        'movie' => route('movie.show', ['slug' => $slugStr]),
        default => '#'
    };
    
    // Season template URL for JS season switching
    $seasonUrlTemplate = match($type) {
        'tv' => route('tv-show.watch', ['slug' => $slugStr, 'season' => '__SEASON__', 'episode' => 1]),
        'anime' => route('anime.watch', ['id' => $id, 'season' => '__SEASON__', 'episode' => 1]),
        default => '#'
    };
@endphp

@section('title', $pageTitle)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/watch.css') }}">
@endpush

@section('content')
<div id="page-wrapper" class="max-w-7xl mx-auto">
    {{-- Video Player Section with Cinema Ambient Glow --}}
    <div class="relative mb-6">
        @if(!empty($media['backdrop_path']))
        <div class="cinema-ambient-glow" style="background-image: url('{{ $tmdb->getImageUrl($media['backdrop_path'], 'original') }}');"></div>
        @endif
        
        <div class="video-container relative z-10">
            {{-- Loading Spinner --}}
            <div id="playerLoadingSpinner" class="absolute inset-0 flex flex-col items-center justify-center bg-[#050811] z-0 transition-opacity duration-500">
                <div class="w-12 h-12 border-4 border-viewlix-red/30 border-t-viewlix-red rounded-full animate-spin mb-3"></div>
                <span class="text-xs text-gray-400 font-medium tracking-wide">Loading Viewlix Player...</span>
            </div>

            <iframe 
                id="playerIframe"
                src="about:blank" 
                scrolling="no"
                frameborder="0" gesture="media" allow="encrypted-media" 
                allowfullscreen 
                referrerpolicy="origin"
                loading="lazy">
            </iframe>

            {{-- Resume Playback Notification Toast --}}
            <div id="resumeToast" class="hidden absolute top-4 left-4 z-20 flex items-center gap-3 bg-black/85 backdrop-blur-md border border-white/20 text-white text-xs sm:text-sm px-3.5 py-2 rounded-xl shadow-2xl transition-all duration-300 transform -translate-y-2 opacity-0">
                <div class="flex items-center gap-1.5 text-blue-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span id="resumeToastText" class="font-medium">Resuming from 0:00</span>
                </div>
                <button type="button" id="resumeStartOverBtn" class="text-xs font-semibold px-2 py-1 bg-white/10 hover:bg-white/20 text-gray-200 hover:text-white rounded-lg transition-colors border border-white/10">
                    Start Over
                </button>
                <button type="button" id="resumeDismissBtn" class="text-gray-400 hover:text-white p-0.5" title="Dismiss">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Up Next Auto-Advance Overlay --}}
            <div id="upNextOverlay" class="hidden absolute bottom-6 right-6 z-20 flex flex-col sm:flex-row items-center gap-4 bg-black/90 backdrop-blur-lg border border-viewlix-red/40 p-4 rounded-2xl shadow-2xl max-w-md transition-all duration-300 transform translate-y-4 opacity-0">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 text-[11px] font-bold tracking-wider uppercase text-blue-400 mb-1">
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                        Up Next in <span id="upNextCountdown" class="text-white font-mono font-black text-xs">5</span>s
                    </div>
                    <div id="upNextTitle" class="text-sm font-semibold text-white truncate">Next Episode</div>
                </div>
                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <button type="button" id="upNextCancelBtn" class="px-3 py-1.5 bg-zinc-800 hover:bg-zinc-700 text-gray-300 text-xs font-medium rounded-xl border border-white/10 transition-colors">
                        Cancel
                    </button>
                    <button type="button" id="upNextPlayNowBtn" class="flex items-center gap-1.5 px-3.5 py-1.5 bg-viewlix-red hover:bg-blue-600 text-white text-xs font-bold rounded-xl shadow-lg transition-colors">
                        <span>Play Now</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Media Information & Interactive Controls Bar --}}
    <div class="bg-[#121217]/90 backdrop-blur-md rounded-2xl border border-white/10 p-4 md:p-5 mb-6 shadow-xl">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
            {{-- Left: Show and Current Episode Details --}}
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold text-gray-400 mb-1.5">
                    @if($type === 'tv' || $type === 'anime')
                        <span class="px-2.5 py-1 bg-viewlix-red/20 border border-viewlix-red/40 text-blue-300 rounded-lg">
                            Season {{ $currentSeasonNum }} • Episode {{ $currentEpNum }}
                        </span>
                        @if(!empty($currentEpisode['runtime']))
                            <span class="text-gray-400">•</span>
                            <span class="flex items-center gap-1 text-gray-300">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $currentEpisode['runtime'] }} min
                            </span>
                        @endif
                        @if(!empty($currentEpisode['air_date']))
                            <span class="text-gray-400">•</span>
                            <span class="text-gray-400">{{ \Carbon\Carbon::parse($currentEpisode['air_date'])->format('M d, Y') }}</span>
                        @endif
                        @if(!empty($currentEpisode['vote_average']))
                            <span class="text-gray-400">•</span>
                            <span class="flex items-center gap-1 text-amber-400">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                {{ number_format($currentEpisode['vote_average'], 1) }}
                            </span>
                        @endif
                        <span class="px-2 py-0.5 bg-zinc-800 text-gray-300 rounded text-[10px] uppercase font-bold tracking-wider">HD</span>
                    @else
                        @if(!empty($media['release_date']))
                            <span class="text-gray-300">{{ \Carbon\Carbon::parse($media['release_date'])->format('Y') }}</span>
                            <span class="text-gray-500">•</span>
                        @endif
                        <span class="px-2 py-0.5 bg-zinc-800 text-gray-300 rounded text-[10px] uppercase font-bold tracking-wider">HD</span>
                    @endif
                </div>

                {{-- Show & Episode Titles --}}
                <div class="flex items-baseline flex-wrap gap-2.5">
                    <a href="{{ $showRoute }}" class="text-xl md:text-2xl font-black text-white hover:text-blue-400 transition-colors truncate" title="View show details">
                        {{ $title ?? 'Unknown Title' }}
                    </a>
                    @if($type !== 'movie' && !empty($currentEpisode['name']))
                        <span class="text-gray-500 hidden sm:inline">•</span>
                        <span class="text-base md:text-lg font-medium text-gray-300 truncate">
                            {{ $currentEpisode['name'] }}
                        </span>
                    @endif
                </div>
            </div>
            
            {{-- Right: Server Source & Navigation Controls --}}
            <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                {{-- Server / Source selector --}}
                <div class="flex items-center gap-1.5 bg-black/40 p-1 rounded-xl border border-white/10">
                    <span class="text-xs font-semibold text-gray-400 px-2 hidden sm:inline flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                        </svg>
                        Server
                    </span>
                    <select id="sourceSelect" class="text-xs sm:text-sm bg-viewlix-gray text-white font-medium pl-3 pr-8 py-2 rounded-lg border-0 focus:ring-1 focus:ring-viewlix-red cursor-pointer">
                        {{-- Options injected by JS --}}
                    </select>
                    <button id="openSourceBtn" class="hidden sm:inline-flex items-center justify-center p-2 text-gray-400 hover:text-white hover:bg-white/10 rounded-lg transition-colors" title="Open server link in new tab">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </button>
                </div>

                {{-- Previous / Next Episode Quick Buttons (TV / Anime only) --}}
                @if($type !== 'movie' && !empty($episodes))
                    <div class="flex items-center gap-1 bg-black/40 p-1 rounded-xl border border-white/10">
                        @if($prevEpisode)
                            @php
                                $prevUrl = $type === 'tv' 
                                    ? route('tv-show.watch', ['slug' => $slugStr, 'season' => $currentSeasonNum, 'episode' => $prevEpisode['episode_number']])
                                    : route('anime.watch', ['id' => $id, 'season' => $currentSeasonNum, 'episode' => $prevEpisode['episode_number']]);
                            @endphp
                            <a href="{{ $prevUrl }}" 
                               class="flex items-center gap-1.5 px-3 py-2 bg-viewlix-gray hover:bg-viewlix-gray-light text-white text-xs sm:text-sm font-semibold rounded-lg transition-colors"
                               title="Previous Episode: {{ $prevEpisode['name'] ?? 'E'.$prevEpisode['episode_number'] }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                                </svg>
                                <span class="hidden md:inline">Prev</span>
                            </a>
                        @else
                            <button class="flex items-center gap-1.5 px-3 py-2 bg-viewlix-gray/40 text-gray-600 text-xs sm:text-sm font-semibold rounded-lg cursor-not-allowed opacity-50" disabled>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                                </svg>
                                <span class="hidden md:inline">Prev</span>
                            </button>
                        @endif

                        @if($nextEpisode)
                            @php
                                $nextUrl = $type === 'tv' 
                                    ? route('tv-show.watch', ['slug' => $slugStr, 'season' => $currentSeasonNum, 'episode' => $nextEpisode['episode_number']])
                                    : route('anime.watch', ['id' => $id, 'season' => $currentSeasonNum, 'episode' => $nextEpisode['episode_number']]);
                            @endphp
                            <a href="{{ $nextUrl }}" 
                               class="flex items-center gap-1.5 px-3 py-2 bg-viewlix-red hover:bg-blue-600 text-white text-xs sm:text-sm font-semibold rounded-lg shadow-md transition-colors"
                               title="Next Episode: {{ $nextEpisode['name'] ?? 'E'.$nextEpisode['episode_number'] }}">
                                <span class="hidden md:inline">Next</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        @else
                            <button class="flex items-center gap-1.5 px-3 py-2 bg-viewlix-gray/40 text-gray-600 text-xs sm:text-sm font-semibold rounded-lg cursor-not-allowed opacity-50" disabled>
                                <span class="hidden md:inline">Next</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        @endif
                    </div>
                @endif

                {{-- Action Buttons: Favorite, Share, Details --}}
                <div class="flex items-center gap-2">
                    {{-- Favorite Button --}}
                    @auth
                    <button 
                        id="favoriteBtn"
                        onclick="toggleFavorite()"
                        class="flex items-center justify-center w-10 h-10 bg-viewlix-gray hover:bg-viewlix-gray-light rounded-xl border border-white/10 transition-colors"
                        title="Add to Favorites"
                    >
                        <svg id="favoriteIcon" class="w-5 h-5 text-gray-400 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </button>
                    @else
                    <a href="{{ route('login') }}" class="flex items-center justify-center w-10 h-10 bg-viewlix-gray text-gray-400 hover:text-viewlix-red rounded-xl border border-white/10 transition-colors" title="Login to add favorites">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </a>
                    @endauth

                    {{-- Share Button --}}
                    <button 
                        type="button"
                        onclick="shareCurrentEpisode('{{ addslashes($currentEpisode['name'] ?? 'Episode '.$currentEpNum) }}', '{{ addslashes($title ?? 'Show') }}')"
                        class="flex items-center justify-center w-10 h-10 bg-viewlix-gray hover:bg-viewlix-gray-light text-gray-300 hover:text-white rounded-xl border border-white/10 transition-colors"
                        title="Share Episode"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                        </svg>
                    </button>

                    {{-- Full Details Link --}}
                    <a href="{{ $showRoute }}" class="flex items-center gap-1.5 px-3.5 py-2 bg-viewlix-gray text-gray-300 hover:text-white text-xs sm:text-sm font-semibold rounded-xl border border-white/10 hover:bg-viewlix-gray-light transition-colors whitespace-nowrap" title="Show overview and episodes">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Details</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- "UP NEXT" HIGHLIGHT BANNER (If next episode exists) --}}
    @if($type !== 'movie' && $nextEpisode)
        @php
            $nextUrl = $type === 'tv' 
                ? route('tv-show.watch', ['slug' => $slugStr, 'season' => $currentSeasonNum, 'episode' => $nextEpisode['episode_number']])
                : route('anime.watch', ['id' => $id, 'season' => $currentSeasonNum, 'episode' => $nextEpisode['episode_number']]);
            $nextStill = !empty($nextEpisode['still_path']) 
                ? $tmdb->getImageUrl($nextEpisode['still_path'], 'w500') 
                : (!empty($media['backdrop_path']) ? $tmdb->getImageUrl($media['backdrop_path'], 'w500') : null);
        @endphp
        <div class="relative overflow-hidden bg-gradient-to-r from-blue-950/40 via-zinc-900/60 to-[#121217] border border-blue-500/20 rounded-2xl p-4 md:p-5 mb-8 shadow-lg group hover:border-blue-500/40 transition-all">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4 min-w-0">
                    {{-- Thumbnail preview --}}
                    <a href="{{ $nextUrl }}" class="relative flex-shrink-0 w-28 sm:w-36 aspect-video rounded-xl overflow-hidden bg-zinc-950 border border-white/10 group-hover:border-blue-500/50 transition-colors">
                        @if($nextStill)
                            <img src="{{ $nextStill }}" alt="{{ $nextEpisode['name'] ?? 'Next Episode' }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-500 font-bold">
                                E{{ $nextEpisode['episode_number'] }}
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-80 group-hover:opacity-100 transition-opacity">
                            <div class="w-8 h-8 rounded-full bg-viewlix-red/90 text-white flex items-center justify-center shadow-lg transform group-hover:scale-110 transition-transform">
                                <svg class="w-4 h-4 fill-current ml-0.5" viewBox="0 0 20 20">
                                    <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                                </svg>
                            </div>
                        </div>
                    </a>
                    {{-- Text info --}}
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-blue-500/20 text-blue-300 border border-blue-500/30 text-[11px] font-bold uppercase tracking-wider rounded-md">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                                Up Next
                            </span>
                            <span class="text-xs font-semibold text-gray-400">
                                Episode {{ $nextEpisode['episode_number'] }}
                                @if(!empty($nextEpisode['runtime']))
                                    • {{ $nextEpisode['runtime'] }}m
                                @endif
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-white group-hover:text-blue-300 transition-colors truncate">
                            {{ $nextEpisode['name'] ?? 'Episode '.$nextEpisode['episode_number'] }}
                        </h3>
                        @if(!empty($nextEpisode['overview']))
                            <p class="text-xs text-gray-400 line-clamp-1 mt-0.5 max-w-xl">
                                {{ $nextEpisode['overview'] }}
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Action button --}}
                <div class="w-full sm:w-auto flex-shrink-0">
                    <a href="{{ $nextUrl }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-viewlix-red hover:bg-blue-600 text-white text-sm font-bold rounded-xl shadow-lg transition-all transform active:scale-95">
                        <span>Play Next Episode</span>
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- REDESIGNED EPISODES SECTION --}}
    @if($type !== 'movie' && !empty($episodes))
    <div class="mb-12">
        {{-- Section Header & Interactive Toolbar --}}
        <div class="bg-[#111116] rounded-2xl border border-white/10 p-4 md:p-5 mb-5 shadow-lg">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                {{-- Season Switcher & Episode Count --}}
                <div class="flex items-center gap-3 flex-wrap">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-viewlix-red" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <h2 class="text-lg md:text-xl font-bold text-white tracking-wide">Episodes</h2>
                    </div>

                    {{-- Season Dropdown Selector --}}
                    @if(!empty($seasons) && count($seasons) > 1)
                        <div class="relative">
                            <select id="seasonSelector" 
                                    onchange="switchSeason(this.value, '{{ $seasonUrlTemplate }}')"
                                    class="bg-viewlix-gray hover:bg-zinc-800 text-white text-xs md:text-sm font-bold pl-3 pr-8 py-2 rounded-xl border border-white/15 focus:border-viewlix-red focus:ring-1 focus:ring-viewlix-red cursor-pointer transition-colors">
                                @foreach($seasons as $s)
                                    <option value="{{ $s['season_number'] }}" {{ $s['season_number'] == $currentSeasonNum ? 'selected' : '' }}>
                                        {{ $s['name'] ?? ('Season ' . $s['season_number']) }} ({{ $s['episode_count'] ?? '?' }} Ep)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <span class="px-3 py-1 bg-white/5 border border-white/10 text-gray-300 text-xs font-semibold rounded-lg">
                            Season {{ $currentSeasonNum }}
                        </span>
                    @endif

                    <span class="text-xs font-medium text-gray-400">
                        {{ count($episodes) }} {{ \Illuminate\Support\Str::plural('Episode', count($episodes)) }}
                    </span>
                </div>

                {{-- Toolbar: Search & View Mode Switcher --}}
                <div class="flex items-center gap-3">
                    {{-- Filter / Search Input --}}
                    <div class="relative flex-1 sm:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" 
                               id="episodeSearchInput" 
                               placeholder="Search episode or #..." 
                               class="w-full pl-9 pr-8 py-2 bg-black/50 border border-white/10 rounded-xl text-xs md:text-sm text-white placeholder-gray-500 focus:outline-none focus:border-viewlix-red focus:ring-1 focus:ring-viewlix-red transition-all">
                        <button id="episodeSearchClearBtn" type="button" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-white">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- View Mode Toggle --}}
                    <div class="flex items-center bg-black/50 p-1 rounded-xl border border-white/10">
                        <button type="button" 
                                id="btnViewCards"
                                onclick="setEpisodeViewMode('cards')"
                                class="px-2.5 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 bg-viewlix-red text-white transition-all"
                                title="Detailed Cards View">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            <span class="hidden sm:inline">Cards</span>
                        </button>
                        <button type="button" 
                                id="btnViewCompact"
                                onclick="setEpisodeViewMode('compact')"
                                class="px-2.5 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-1.5 bg-viewlix-gray text-gray-400 hover:text-white transition-all"
                                title="Compact Grid View">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                            <span class="hidden sm:inline">Grid</span>
                        </button>
                    </div>

                    {{-- Collapse / Expand Toggle Button --}}
                    <button type="button" 
                            id="btnToggleEpisodeCollapse"
                            onclick="toggleEpisodeListCollapse()"
                            class="flex items-center gap-1.5 px-3 py-1.5 bg-black/50 hover:bg-white/10 text-gray-300 hover:text-white rounded-xl text-xs font-bold border border-white/10 transition-all cursor-pointer shadow-sm"
                            title="Collapse / Expand Episode List">
                        <span id="episodeCollapseLabel">Collapse</span>
                        <svg id="episodeCollapseChevron" class="w-3.5 h-3.5 transform transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Optional Horizontal Season Pills (when multiple seasons exist) --}}
            @if(!empty($seasons) && count($seasons) > 1)
                <div class="mt-4 pt-3 border-t border-white/5 flex items-center gap-2 overflow-x-auto horizontal-scroll-hide">
                    @foreach($seasons as $s)
                        <a href="{{ str_replace('__SEASON__', $s['season_number'], $seasonUrlTemplate) }}" 
                           class="flex-shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all {{ $s['season_number'] == $currentSeasonNum ? 'bg-blue-600 text-white shadow-md shadow-blue-900/30' : 'bg-zinc-800/80 hover:bg-zinc-700 text-gray-300' }}">
                            {{ $s['name'] ?? ('Season ' . $s['season_number']) }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Collapsible Episode Content Container --}}
        <div id="episodesContentContainer" class="transition-all duration-300">
            {{-- Scrollable Episodes Panel (scrolls inside panel, not the entire page) --}}
            <div id="episodesScrollPanel" class="episodes-scroll-panel">
                {{-- VIEW A: DETAILED CARDS VIEW (Default) --}}
                <div id="episodesDetailedView" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 pb-2">
                    @foreach($episodes as $ep)
                        @php
                            $isCur = (int)($ep['episode_number'] ?? 0) === (int)$currentEpNum;
                            $epLink = $type === 'tv' 
                                ? route('tv-show.watch', ['slug' => $slugStr, 'season' => $currentSeasonNum, 'episode' => $ep['episode_number']])
                                : route('anime.watch', ['id' => $id, 'season' => $currentSeasonNum, 'episode' => $ep['episode_number']]);
                            $epStill = !empty($ep['still_path']) 
                                ? $tmdb->getImageUrl($ep['still_path'], 'w500') 
                                : (!empty($media['backdrop_path']) ? $tmdb->getImageUrl($media['backdrop_path'], 'w500') : null);
                            $epSearchMeta = "Episode {$ep['episode_number']} " . ($ep['name'] ?? '') . " " . ($ep['overview'] ?? '');
                        @endphp

                        <a href="{{ $epLink }}" 
                           data-search="{{ $epSearchMeta }}"
                           class="episode-card group flex flex-col bg-[#111116] rounded-2xl overflow-hidden border {{ $isCur ? 'episode-card-active border-blue-500 ring-2 ring-blue-500/50' : 'border-white/10 hover:border-blue-500/50 hover:bg-[#16161d]' }} shadow-md">
                            
                            {{-- Card Thumbnail (16:9) --}}
                            <div class="relative aspect-video w-full bg-zinc-950 overflow-hidden">
                                @if($epStill)
                                    <img src="{{ $epStill }}" 
                                         alt="{{ $ep['name'] ?? 'Episode '.$ep['episode_number'] }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                         loading="lazy" 
                                         decoding="async">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-zinc-900 text-zinc-600 font-black text-3xl">
                                        E{{ $ep['episode_number'] }}
                                    </div>
                                @endif

                                {{-- Gradient Overlay --}}
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>

                                {{-- Top-Left Badge: Episode Number Pill --}}
                                <div class="absolute top-2.5 left-2.5 flex items-center gap-1.5 px-2.5 py-1 bg-black/80 backdrop-blur-md border border-white/10 rounded-lg text-xs font-bold text-white shadow-md">
                                    <span>EP {{ $ep['episode_number'] }}</span>
                                </div>

                                {{-- Top-Right Badge: Active Now Playing Equalizer OR Rating --}}
                                @if($isCur)
                                    <div class="absolute top-2.5 right-2.5 flex items-center gap-1.5 px-2.5 py-1 bg-viewlix-red text-white text-xs font-extrabold uppercase tracking-wider rounded-lg shadow-lg shadow-blue-900/50">
                                        <span class="equalizer-bars text-white">
                                            <span class="equalizer-bar"></span>
                                            <span class="equalizer-bar"></span>
                                            <span class="equalizer-bar"></span>
                                            <span class="equalizer-bar"></span>
                                        </span>
                                        <span>Playing</span>
                                    </div>
                                @elseif(!empty($ep['vote_average']))
                                    <div class="absolute top-2.5 right-2.5 flex items-center gap-1 px-2 py-0.5 bg-black/75 backdrop-blur-md border border-white/10 rounded-lg text-[11px] font-bold text-amber-400 shadow-md">
                                        <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span>{{ number_format($ep['vote_average'], 1) }}</span>
                                    </div>
                                @endif

                                {{-- Bottom-Right: Duration / Runtime --}}
                                @if(!empty($ep['runtime']))
                                    <div class="absolute bottom-2.5 right-2.5 px-2 py-0.5 bg-black/85 backdrop-blur-md rounded text-[11px] font-medium text-gray-300">
                                        {{ $ep['runtime'] }}m
                                    </div>
                                @endif

                                {{-- Center Play Icon on Hover --}}
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200">
                                    <div class="w-11 h-11 rounded-full bg-viewlix-red/90 text-white flex items-center justify-center shadow-xl transform scale-90 group-hover:scale-100 transition-transform">
                                        <svg class="w-5 h-5 fill-current ml-0.5" viewBox="0 0 20 20">
                                            <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            {{-- Card Info --}}
                            <div class="p-3.5 flex-1 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between text-[11px] text-gray-400 font-medium mb-1">
                                        <span>Episode {{ $ep['episode_number'] }}</span>
                                        @if(!empty($ep['air_date']))
                                            <span>{{ \Carbon\Carbon::parse($ep['air_date'])->format('M d, Y') }}</span>
                                        @endif
                                    </div>
                                    <h3 class="text-sm font-bold text-white group-hover:text-blue-400 transition-colors truncate mb-1.5" title="{{ $ep['name'] ?? 'Episode '.$ep['episode_number'] }}">
                                        {{ $ep['name'] ?? 'Episode '.$ep['episode_number'] }}
                                    </h3>
                                    @if(!empty($ep['overview']))
                                        <p class="text-xs text-gray-400 line-clamp-2 leading-relaxed">
                                            {{ $ep['overview'] }}
                                        </p>
                                    @else
                                        <p class="text-xs text-gray-500 italic">
                                            No description available for this episode.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- VIEW B: COMPACT GRID VIEW (Quick Number Buttons) --}}
                <div id="episodesCompactView" class="hidden grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 lg:grid-cols-10 xl:grid-cols-12 gap-2.5 pb-2">
                    @foreach($episodes as $ep)
                        @php
                            $isCur = (int)($ep['episode_number'] ?? 0) === (int)$currentEpNum;
                            $epLink = $type === 'tv' 
                                ? route('tv-show.watch', ['slug' => $slugStr, 'season' => $currentSeasonNum, 'episode' => $ep['episode_number']])
                                : route('anime.watch', ['id' => $id, 'season' => $currentSeasonNum, 'episode' => $ep['episode_number']]);
                            $epSearchMeta = "Episode {$ep['episode_number']} " . ($ep['name'] ?? '');
                        @endphp
                        <a href="{{ $epLink }}" 
                           data-search="{{ $epSearchMeta }}"
                           class="episode-chip flex flex-col items-center justify-center p-3 rounded-xl border font-bold text-sm transition-all {{ $isCur ? 'bg-viewlix-red border-viewlix-red text-white shadow-lg shadow-blue-900/40 ring-2 ring-blue-400/50' : 'bg-[#111116] border-white/10 text-gray-300 hover:border-blue-500/50 hover:bg-zinc-800 hover:text-white' }}"
                           title="{{ $ep['name'] ?? 'Episode '.$ep['episode_number'] }}">
                            <span class="text-xs font-semibold opacity-70">EP</span>
                            <span class="text-base font-black">{{ $ep['episode_number'] }}</span>
                            @if($isCur)
                                <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping mt-1"></span>
                            @endif
                        </a>
                    @endforeach
                </div>

                {{-- Empty Search Notice --}}
                <div id="episodesEmptySearchNotice" class="hidden py-12 text-center bg-[#111116] rounded-2xl border border-white/10 my-2">
                    <svg class="w-12 h-12 mx-auto text-gray-600 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <h4 class="text-base font-bold text-white mb-1">No episodes found</h4>
                    <p class="text-xs text-gray-400">Try adjusting your search keywords or episode number.</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- EXTENDED CONTENT TABS: OVERVIEW, CAST & SIMILAR SHOWS --}}
    <div class="bg-[#111116] rounded-2xl border border-white/10 p-5 md:p-6 mb-12 shadow-xl">
        {{-- Tabs Navigation --}}
        <div class="flex items-center gap-2 border-b border-white/10 pb-4 mb-6 overflow-x-auto horizontal-scroll-hide">
            <button type="button" 
                    onclick="setWatchTab('overview')"
                    data-tab="overview"
                    class="watch-tab-btn px-4 py-2 text-sm font-bold rounded-xl border-b-2 border-viewlix-red text-white bg-white/5 transition-all">
                Overview & Details
            </button>
            @if(!empty($credits['cast']))
            <button type="button" 
                    onclick="setWatchTab('cast')"
                    data-tab="cast"
                    class="watch-tab-btn px-4 py-2 text-sm font-bold rounded-xl border-b-2 border-transparent text-gray-400 hover:text-gray-200 transition-all">
                Cast ({{ count($credits['cast']) }})
            </button>
            @endif
            @if(!empty($similar))
            <button type="button" 
                    onclick="setWatchTab('similar')"
                    data-tab="similar"
                    class="watch-tab-btn px-4 py-2 text-sm font-bold rounded-xl border-b-2 border-transparent text-gray-400 hover:text-gray-200 transition-all">
                More Like This
            </button>
            @endif
        </div>

        {{-- Tab 1: Overview Panel --}}
        <div id="tab-panel-overview" class="watch-tab-panel">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <h3 class="text-lg font-bold text-white mb-3">Storyline</h3>
                    <p class="text-sm md:text-base text-gray-300 leading-relaxed max-w-3xl">
                        {{ $media['overview'] ?? 'No overview available for this title.' }}
                    </p>
                </div>
                <div class="space-y-3 bg-black/40 p-4 rounded-xl border border-white/5 text-xs md:text-sm">
                    @if(!empty($media['genres']))
                    <div>
                        <span class="text-gray-500 font-medium block mb-1">Genres:</span>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($media['genres'] as $genre)
                                <span class="px-2 py-0.5 bg-zinc-800 rounded text-gray-300 font-semibold">{{ $genre['name'] }}</span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @if(!empty($media['first_air_date']))
                    <div class="flex justify-between border-t border-white/5 pt-2">
                        <span class="text-gray-500">First Air Date:</span>
                        <span class="text-white font-medium">{{ \Carbon\Carbon::parse($media['first_air_date'])->format('F d, Y') }}</span>
                    </div>
                    @endif
                    @if(!empty($media['status']))
                    <div class="flex justify-between border-t border-white/5 pt-2">
                        <span class="text-gray-500">Status:</span>
                        <span class="text-white font-medium">{{ $media['status'] }}</span>
                    </div>
                    @endif
                    @if(!empty($media['number_of_seasons']))
                    <div class="flex justify-between border-t border-white/5 pt-2">
                        <span class="text-gray-500">Total Seasons:</span>
                        <span class="text-white font-medium">{{ $media['number_of_seasons'] }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tab 2: Cast Panel --}}
        @if(!empty($credits['cast']))
        <div id="tab-panel-cast" class="watch-tab-panel hidden">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach(array_slice($credits['cast'], 0, 12) as $actor)
                    <div class="bg-black/30 p-3 rounded-xl border border-white/5 text-center flex flex-col items-center">
                        <div class="w-16 h-16 md:w-20 md:h-20 rounded-full bg-zinc-800 overflow-hidden mb-2.5 border border-white/10 shadow-md">
                            @if(!empty($actor['profile_path']))
                                <img src="{{ $tmdb->getImageUrl($actor['profile_path'], 'w185') }}" 
                                     alt="{{ $actor['name'] }}" 
                                     class="w-full h-full object-cover" 
                                     loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-500">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <h4 class="text-xs md:text-sm font-bold text-white truncate w-full">{{ $actor['name'] }}</h4>
                        <p class="text-[11px] text-gray-400 truncate w-full">{{ $actor['character'] ?? 'Actor' }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Tab 3: Similar TV Shows Panel --}}
        @if(!empty($similar))
        <div id="tab-panel-similar" class="watch-tab-panel hidden">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach(array_slice($similar, 0, 6) as $simItem)
                    @include('partials.media-card', ['item' => $simItem, 'type' => $type === 'anime' ? 'anime' : 'tv'])
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
@php
    $nextEpisodeUrl = null;
    if (!empty($nextEpisode)) {
        $nextEpisodeUrl = $type === 'tv' 
            ? route('tv-show.watch', ['slug' => $slugStr, 'season' => $currentSeasonNum, 'episode' => $nextEpisode['episode_number']])
            : ($type === 'anime' ? route('anime.watch', ['id' => $id, 'season' => $currentSeasonNum, 'episode' => $nextEpisode['episode_number']]) : null);
    }
@endphp
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
    releaseDate: @json($media['release_date'] ?? $media['first_air_date'] ?? null),
    nextEpisodeUrl: @json($nextEpisodeUrl),
    nextEpisodeName: @json($nextEpisode['name'] ?? null),
    nextEpisodeNumber: {{ $nextEpisode['episode_number'] ?? 'null' }}
};

let isFavorited = false;

// Check if favorited on load
@auth
document.addEventListener('DOMContentLoaded', async function() {
    try {
        const response = await fetch(`{{ route('favorites.check') }}?media_id=${mediaData.id}&media_type=${mediaData.type}`);
        const data = await response.json();
        window.isFavorited = data.favorited;
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

// Toggle favorite
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
        window.isFavorited = data.status === 'added';
        if (typeof updateFavoriteIcon === 'function') updateFavoriteIcon();
        showWatchToast(window.isFavorited ? 'Added to Favorites' : 'Removed from Favorites');
    } catch (error) {
        console.error('Error toggling favorite:', error);
    }
}

// Pass server-provided sources/embed to external script via globals
const providedSources = @json($sources ?? null, JSON_UNESCAPED_SLASHES);
const defaultEmbed = @json($embedUrl, JSON_UNESCAPED_SLASHES);
</script>

<script src="{{ asset('js/watch.js') }}"></script>
@endpush

@push('scripts')
<script>
// Adjust page wrapper and video container to fit available viewport on mobile
function adjustWatchLayout(){
    const wrapper = document.getElementById('page-wrapper');
    if(!wrapper) return;

    const header = document.querySelector('header') || document.querySelector('.site-header') || document.querySelector('.topbar');
    const bottomNav = document.querySelector('.mobile-nav') || document.querySelector('#mobile-bottom-nav') || document.querySelector('.bottom-nav') || document.querySelector('footer');

    const defaultHeaderHeight = window.innerWidth < 768 ? 56 : 80;
    const defaultBottomHeight = window.innerWidth < 768 ? 56 : 0;

    const headerH = header ? Math.ceil(header.getBoundingClientRect().height) : defaultHeaderHeight;
    const bottomH = (window.innerWidth < 768) ? (bottomNav ? Math.ceil(bottomNav.getBoundingClientRect().height) : defaultBottomHeight) : 0;

    const padding = 24;
    let available = window.innerHeight - headerH - bottomH - padding;
    const cap = Math.floor(window.innerHeight * 0.85);
    if(available > cap) available = cap;
    if(available < 300) available = 300;

    wrapper.style.minHeight = available + 'px';
    try {
        const extra = 20;
        wrapper.style.paddingBottom = `calc(${bottomH + extra}px + env(safe-area-inset-bottom))`;
    } catch (e) {
        wrapper.style.paddingBottom = (bottomH + 20) + 'px';
    }

    const videoContainer = wrapper.querySelector('.video-container');
    if(videoContainer){
        const width = Math.floor(videoContainer.getBoundingClientRect().width) || window.innerWidth;
        const desiredHeight = Math.min(Math.round(width * 9 / 16), Math.floor(available * 0.6));
        videoContainer.style.height = desiredHeight + 'px';
        videoContainer.style.paddingBottom = '0';
        videoContainer.style.maxHeight = Math.floor(available * 0.75) + 'px';

        const iframe = videoContainer.querySelector('#playerIframe');
        if(iframe){
            iframe.style.width = '100%';
            iframe.style.height = '100%';
        }
    }
}

document.addEventListener('DOMContentLoaded', function(){
    adjustWatchLayout();
    setTimeout(adjustWatchLayout, 200);
    setTimeout(adjustWatchLayout, 800);
});

window.addEventListener('resize', adjustWatchLayout);
window.addEventListener('orientationchange', function(){ setTimeout(adjustWatchLayout, 250); });
</script>
@endpush


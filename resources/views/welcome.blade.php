@extends('layouts.app')

@section('title', 'Welcome to Viewlix')

@section('content')
<div class="min-h-[calc(100vh-200px)]">
    {{-- Hero Section --}}
    <section class="relative py-12 lg:py-20">
        <div class="text-center max-w-3xl mx-auto">
            <h1 class="text-4xl lg:text-6xl font-bold text-white mb-4">
                Welcome to <span class="text-viewlix-red">Viewlix</span>
            </h1>
            <p class="text-lg text-gray-400 mb-8">
                Discover, search, and watch the best movies, TV shows, and anime. 
                Enjoy a modern, unique experience!
            </p>
            <div class="flex items-center justify-center gap-4">
                <a href="{{ route('movies') }}" class="px-6 py-3 bg-viewlix-red hover:bg-viewlix-red-dark text-white font-semibold rounded-xl transition-colors">
                    Browse Movies
                </a>
                <a href="{{ route('tv-shows') }}" class="px-6 py-3 bg-viewlix-gray hover:bg-viewlix-gray-light text-white font-semibold rounded-xl border border-viewlix-border transition-colors">
                    TV Shows
                </a>
            </div>
        </div>
    </section>
    
    {{-- Continue Watching Section (from localStorage) --}}
    @include('partials.continue-watching')
    
    {{-- Trending Movies --}}
    @if(isset($trendingMovies) && count($trendingMovies) > 0)
    <section class="py-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">🔥 Trending Movies</h2>
            <a href="{{ route('movies') }}" class="text-sm text-viewlix-red hover:text-viewlix-red-light transition-colors">View All</a>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach(array_slice($trendingMovies, 0, 6) as $movie)
            @include('partials.media-card', ['item' => $movie, 'type' => 'movie'])
            @endforeach
        </div>
    </section>
    @endif
    
    {{-- Top Rated Movies --}}
    @if(isset($topRatedMovies) && count($topRatedMovies) > 0)
    <section class="py-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">⭐ Top Rated</h2>
            <a href="{{ route('movies', ['filter' => 'top_rated']) }}" class="text-sm text-viewlix-red hover:text-viewlix-red-light transition-colors">View All</a>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach(array_slice($topRatedMovies, 0, 6) as $movie)
            @include('partials.media-card', ['item' => $movie, 'type' => 'movie'])
            @endforeach
        </div>
    </section>
    @endif
    
    {{-- Trending TV Shows --}}
    @if(isset($trendingTv) && count($trendingTv) > 0)
    <section class="py-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">� Trending TV Shows</h2>
            <a href="{{ route('tv-shows') }}" class="text-sm text-viewlix-red hover:text-viewlix-red-light transition-colors">View All</a>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach(array_slice($trendingTv, 0, 6) as $show)
            @include('partials.media-card', ['item' => $show, 'type' => 'tv'])
            @endforeach
        </div>
    </section>
    @endif
    
    {{-- Popular Anime --}}
    @if(isset($popularAnime) && count($popularAnime) > 0)
    <section class="py-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-white">🎌 Popular Anime</h2>
            <a href="{{ route('anime') }}" class="text-sm text-viewlix-red hover:text-viewlix-red-light transition-colors">View All</a>
        </div>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
            @foreach(array_slice($popularAnime, 0, 6) as $anime)
            @include('partials.media-card', ['item' => $anime, 'type' => 'anime'])
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection

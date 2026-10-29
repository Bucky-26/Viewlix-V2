@extends('layouts.app')

@section('title', 'Welcome to Viewlix - Watch Free Movies, TV Shows & Anime')

@section('description', 'Discover and stream thousands of trending movies, top-rated TV series, and popular anime in full HD on Viewlix. 100% free streaming with no subscriptions.')

@push('styles')
    {{-- Main custom CSS --}}
    <link rel="stylesheet" href="{{ url('css/main.css') }}?v={{ filemtime(public_path('css/main.css')) }}" />

    @php
        $pageUrl = url()->current();
        $pageTitle = 'Viewlix - Watch Free Movies, TV Shows & Anime in HD';
        $pageDescription = 'Discover, search, and stream the best movies, TV shows, and anime on Viewlix. Enjoy 4K quality, watchlist sync, and a modern cinema experience.';
        $ogImage = null;
        if(isset($trendingMovies) && count($trendingMovies) > 0 && isset($tmdb) && ($trendingMovies[0]['backdrop_path'] ?? null)) {
            $ogImage = $tmdb->getImageUrl($trendingMovies[0]['backdrop_path'], 'original', true);
        } else {
            $ogImage = asset('viewlix.png');
        }

        $jsonLd = json_encode([
            "@context" => "https://schema.org",
            "@type" => "WebSite",
            "name" => config('app.name', 'Viewlix'),
            "url" => url('/'),
            "description" => $pageDescription,
            "publisher" => [
                "@type" => "Organization",
                "name" => config('app.name', 'Viewlix'),
                "url" => url('/'),
                "logo" => asset('viewlix.png'),
            ]
        ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
    @endphp

    <link rel="canonical" href="{{ $pageUrl }}" />
    <meta name="robots" content="index, follow" />

    {{-- Open Graph --}}
    <meta property="og:locale" content="{{ str_replace('_','-',app()->getLocale()) }}" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ $pageTitle }}" />
    <meta property="og:description" content="{{ $pageDescription }}" />
    <meta property="og:url" content="{{ $pageUrl }}" />
    <meta property="og:image" content="{{ $ogImage }}" />

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $pageTitle }}" />
    <meta name="twitter:description" content="{{ $pageDescription }}" />
    <meta name="twitter:image" content="{{ $ogImage }}" />

    <style>
        /* Scoped styles for high-impact cinematic hero carousel */
        #hero-carousel { position: relative; }
        #hero-carousel .hero-carousel-body { position: relative; width: 100%; height: 100%; }
        #hero-carousel .hero-carousel-slide { 
            position: absolute; 
            inset: 0; 
            width: 100%; 
            height: 100%; 
            opacity: 0; 
            visibility: hidden;
            transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.8s ease; 
        }
        #hero-carousel .hero-carousel-slide.active { 
            opacity: 1; 
            visibility: visible;
            z-index: 10; 
        }
    </style>

    <script src="{{ asset('js/home.js') }}?v={{ filemtime(public_path('js/home.js')) }}"></script>

    {{-- JSON-LD Structured Data --}}
    <script type="application/ld+json">
        {!! $jsonLd !!}
    </script>
@endpush

@section('content')
<div class="space-y-10 sm:space-y-14 -mt-4 -mx-4 lg:-mt-6 lg:-mx-6">

    {{-- Cinematic Hero Carousel Section --}}
    @if((isset($trendingMovies) && count($trendingMovies) > 0) || (isset($trendingTv) && count($trendingTv) > 0))
    <section id="hero-carousel" class="relative w-full h-[70vh] sm:h-[75vh] lg:h-[82vh] max-h-[850px] min-h-[500px] overflow-hidden bg-black">
        <div class="hero-carousel-container relative w-full h-full">
            <div id="hero-carousel-body" class="hero-carousel-body">
                <!-- Skeleton Placeholder before JS hydration -->
                <div class="hero-carousel-slide active">
                    <div class="relative w-full h-full bg-zinc-950">
                        <div class="absolute inset-0 bg-gradient-to-r from-black via-black/60 to-transparent z-10"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-[#08080a] via-transparent to-transparent z-10"></div>
                        <div class="absolute bottom-16 left-6 sm:left-12 lg:left-16 z-20 max-w-2xl space-y-4">
                            <div class="h-6 w-28 bg-white/10 rounded-full animate-pulse"></div>
                            <div class="h-12 w-96 max-w-full bg-white/10 rounded-2xl animate-pulse"></div>
                            <div class="h-4 w-64 bg-white/10 rounded-lg animate-pulse"></div>
                            <div class="h-16 w-full bg-white/5 rounded-2xl animate-pulse"></div>
                            <div class="flex gap-4 pt-2">
                                <div class="h-12 w-36 bg-viewlix-red/40 rounded-xl animate-pulse"></div>
                                <div class="h-12 w-36 bg-white/10 rounded-xl animate-pulse"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Arrows -->
        <button type="button" 
                id="hero-prev" 
                class="absolute inset-y-0 start-0 z-20 inline-flex justify-center items-center w-12 sm:w-16 h-full text-white/70 hover:text-white hover:bg-gradient-to-r hover:from-black/50 hover:to-transparent focus:outline-none transition-all duration-200"
                aria-label="Previous Slide">
            <div class="w-10 h-10 rounded-full bg-black/40 backdrop-blur-md border border-white/10 flex items-center justify-center hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6"/>
                </svg>
            </div>
        </button>

        <button type="button" 
                id="hero-next" 
                class="absolute inset-y-0 end-0 z-20 inline-flex justify-center items-center w-12 sm:w-16 h-full text-white/70 hover:text-white hover:bg-gradient-to-l hover:from-black/50 hover:to-transparent focus:outline-none transition-all duration-200"
                aria-label="Next Slide">
            <div class="w-10 h-10 rounded-full bg-black/40 backdrop-blur-md border border-white/10 flex items-center justify-center hover:scale-110 transition-transform">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m9 18 6-6-6-6"/>
                </svg>
            </div>
        </button>

        <!-- Slide Indicators Container (Handled by JS) -->
        <div id="hero-dots" class="absolute bottom-4 sm:bottom-6 left-6 sm:left-12 lg:left-16 z-30 flex items-center gap-2.5"></div>
    </section>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        {{-- Streaming Providers Section --}}
        @include('partials.streaming-providers')

        {{-- Continue Watching Section --}}
        @include('partials.continue-watching')
        
        {{-- Trending Movies Shelf --}}
        <section class="space-y-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-2.5 h-6 bg-gradient-to-b from-viewlix-red to-blue-600 rounded-full shadow-md shadow-viewlix-red/30"></div>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                            <span>Trending Movies</span>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-viewlix-red/10 border border-viewlix-red/20 text-blue-400 font-semibold uppercase tracking-wider">Top Picks</span>
                        </h2>
                        <p class="text-xs text-gray-400 mt-0.5">Most watched blockbusters right now</p>
                    </div>
                </div>
                <a href="{{ route('movies') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-semibold text-gray-300 hover:text-white transition-all group">
                    <span>Explore All</span>
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div id="trending-movies-grid">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4.5">
                    @for($i = 0; $i < 6; $i++)
                        <div class="bg-[#111116] rounded-2xl overflow-hidden border border-white/5 animate-pulse">
                            <div class="aspect-[2/3] bg-zinc-900"></div>
                            <div class="p-3 space-y-2">
                                <div class="h-3 bg-white/10 rounded w-3/4"></div>
                                <div class="h-2.5 bg-white/5 rounded w-1/3"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </section>
        
        {{-- Top Rated Movies Shelf --}}
        <section class="space-y-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-2.5 h-6 bg-gradient-to-b from-amber-500 to-orange-500 rounded-full shadow-md shadow-amber-500/30"></div>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                            <span>Top Rated Movies</span>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-amber-500/10 border border-amber-500/20 text-amber-400 font-semibold uppercase tracking-wider">Critically Acclaimed</span>
                        </h2>
                        <p class="text-xs text-gray-400 mt-0.5">Highest rated films by audiences worldwide</p>
                    </div>
                </div>
                <a href="{{ route('movies', ['filter' => 'top_rated']) }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-semibold text-gray-300 hover:text-white transition-all group">
                    <span>Explore All</span>
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div id="top-rated-movies-grid">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4.5">
                    @for($i = 0; $i < 6; $i++)
                        <div class="bg-[#111116] rounded-2xl overflow-hidden border border-white/5 animate-pulse">
                            <div class="aspect-[2/3] bg-zinc-900"></div>
                            <div class="p-3 space-y-2">
                                <div class="h-3 bg-white/10 rounded w-3/4"></div>
                                <div class="h-2.5 bg-white/5 rounded w-1/3"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </section>
        
        {{-- Trending TV Shows Shelf --}}
        <section class="space-y-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-2.5 h-6 bg-gradient-to-b from-indigo-500 to-purple-600 rounded-full shadow-md shadow-indigo-500/30"></div>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                            <span>Binge-Worthy TV Shows</span>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 font-semibold uppercase tracking-wider">Series</span>
                        </h2>
                        <p class="text-xs text-gray-400 mt-0.5">Stream the latest seasons and trending series</p>
                    </div>
                </div>
                <a href="{{ route('tv-shows') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-semibold text-gray-300 hover:text-white transition-all group">
                    <span>Explore All</span>
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div id="trending-tv-grid">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4.5">
                    @for($i = 0; $i < 6; $i++)
                        <div class="bg-[#111116] rounded-2xl overflow-hidden border border-white/5 animate-pulse">
                            <div class="aspect-[2/3] bg-zinc-900"></div>
                            <div class="p-3 space-y-2">
                                <div class="h-3 bg-white/10 rounded w-3/4"></div>
                                <div class="h-2.5 bg-white/5 rounded w-1/3"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </section>
        
        {{-- Popular Anime Shelf --}}
        <section class="space-y-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-2.5 h-6 bg-gradient-to-b from-pink-500 to-rose-600 rounded-full shadow-md shadow-pink-500/30"></div>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                            <span>Popular Anime</span>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-pink-500/10 border border-pink-500/20 text-pink-400 font-semibold uppercase tracking-wider">Simulcasts</span>
                        </h2>
                        <p class="text-xs text-gray-400 mt-0.5">Top-rated anime series and movies with sub &amp; dub</p>
                    </div>
                </div>
                <a href="{{ route('anime') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-semibold text-gray-300 hover:text-white transition-all group">
                    <span>Explore All</span>
                    <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div id="popular-anime-grid">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4.5">
                    @for($i = 0; $i < 6; $i++)
                        <div class="bg-[#111116] rounded-2xl overflow-hidden border border-white/5 animate-pulse">
                            <div class="aspect-[2/3] bg-zinc-900"></div>
                            <div class="p-3 space-y-2">
                                <div class="h-3 bg-white/10 rounded w-3/4"></div>
                                <div class="h-2.5 bg-white/5 rounded w-1/3"></div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        </section>
    </div>

</div>
@endsection

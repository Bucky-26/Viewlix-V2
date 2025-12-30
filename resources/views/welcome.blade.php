    @extends('layouts.app')

        @section('title', 'Welcome to Viewlix')

        {{-- SEO: description, Open Graph, Twitter Card, canonical and JSON-LD. Pushed into the head via the `styles` stack. --}}
        @section('description', 'Discover, search, and watch the best movies, TV shows, and anime on Viewlix — curated trending picks, top rated titles, and a modern streaming experience.')

        @push('styles')
    <link rel="stylesheet" href="{{ url('css/main.css') }}" />
            @php
                $pageUrl = url()->current();
                $pageTitle = 'Welcome to Viewlix | ' . config('app.name', 'Viewlix');
                $pageDescription = trim(View::yieldContent('description', 'Discover, search, and watch the best movies, TV shows, and anime on Viewlix — curated trending picks, top rated titles, and a modern streaming experience.'));
                $ogImage = null;
                if(isset($trendingMovies) && count($trendingMovies) > 0 && isset($tmdb) && ($trendingMovies[0]['backdrop_path'] ?? null)) {
                    $ogImage = $tmdb->getImageUrl($trendingMovies[0]['backdrop_path'], 'original');
                } else {
                    $ogImage = asset('splash.png');
                }

                // Precompute JSON-LD to avoid Blade interpreting keys that start with @ (e.g. @context)
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

            {{-- JSON-LD Structured Data (WebSite + Publisher) --}}
            <script type="application/ld+json">
                {!! $jsonLd !!}
            </script>
        @endpush

    @section('content')
    <div class="lg:min-h-[calc(100vh-200px)]">

        
        {{-- Trending Now Carousel (Preline) --}}
        @if(isset($trendingMovies) && count($trendingMovies) > 0)
        <section class="py-6">
            <div class="max-w-7xl mx-auto px-4">
                <div class="relative">
                    <div data-hs-carousel='{
    "loadingClasses": "opacity-0",
    "dotsItemClasses": "hs-carousel-active:bg-blue-700 hs-carousel-active:border-blue-700 size-3 border border-gray-400 rounded-full cursor-pointer",
    "isAutoPlay": true
  }' class="relative">
                      <div class="hs-carousel relative overflow-hidden w-full min-h-96 rounded-xl bg-viewlix-darker">
                        <div class="hs-carousel-body absolute top-0 bottom-0 start-0 flex flex-nowrap transition-transform duration-700 opacity-0">
                            @foreach(array_slice($trendingMovies, 0, 8) as $movie)
                            <div class="hs-carousel-slide min-w-full flex-shrink-0 rounded-xl overflow-hidden relative">
                                <div role="link" tabindex="0" aria-label="Open details for {{ $movie['title'] ?? $movie['name'] }}" class="block relative group viewlix-clickable-card" data-url="{{ route('movie.show', ['slug' => $movie['id'].'-'.\Illuminate\Support\Str::slug($movie['title'] ?? $movie['name'])]) }}">
                                    @if(isset($tmdb) && ($movie['backdrop_path'] ?? null))
                                    <img src="{{ $tmdb->getImageUrl($movie['backdrop_path'], 'original') }}" alt="{{ $movie['title'] ?? $movie['name'] }}"   class="w-full h-[260px] sm:h-[360px] md:h-[480px] lg:h-[480px] object-cover">
                                    <div class="w-full h-[260px] sm:h-[360px] md:h-[480px] lg:h-[480px] bg-viewlix-darker"></div>
                                    @endif

                                    <!-- Move overlay content toward the top and increase sizing for better prominence -->
                                    <div class="absolute inset-0 bg-gradient-to-b from-black/90 via-transparent to-transparent flex items-start justify-center">
                                        <div class="max-w-3xl text-center px-4 sm:px-6 pt-8 sm:pt-12 md:pt-16 lg:pt-24 pb-6 md:pb-0">
                                            <span class="inline-block bg-white/10 text-white text-sm rounded-full px-3 py-1 mb-4">Trending Now</span>
                                            <h3 class="text-2xl sm:text-3xl md:text-6xl lg:text-7xl font-extrabold text-white tracking-tight mb-4">{{ $movie['title'] ?? $movie['name'] }}</h3>

                                            <div class="flex items-center justify-center gap-4 text-sm text-gray-300 mb-4">
                                                @php
                                                    $date = $movie['release_date'] ?? ($movie['first_air_date'] ?? null);
                                                @endphp
                                                @if($date)
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                    <span>{{ substr($date, 0, 4) }}</span>
                                                </div>
                                                @endif

                                                @if(isset($movie['vote_average']) && $movie['vote_average'])
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.96a1 1 0 00.95.69h4.17c.969 0 1.371 1.24.588 1.81l-3.375 2.455a1 1 0 00-.364 1.118l1.287 3.96c.3.922-.755 1.688-1.54 1.118L10 13.348l-3.375 2.455c-.785.57-1.84-.196-1.54-1.118l1.287-3.96a1 1 0 00-.364-1.118L2.633 9.387c-.783-.57-.381-1.81.588-1.81h4.17a1 1 0 00.95-.69l1.286-3.96z"></path></svg>
                                                    <span>{{ number_format($movie['vote_average'], 1) }}/10</span>
                                                </div>
                                                @endif

                                                @if(!empty($movie['genre_names'][0] ?? null))
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 11h18M3 15h18"></path></svg>
                                                    <span>{{ $movie['genre_names'][0] }}</span>
                                                </div>
                                                @endif
                                            </div>

                                            <p class="text-gray-300 hidden md:block mb-8 text-lg">{{ \Illuminate\Support\Str::limit($movie['overview'] ?? '', 220) }}</p>

                                            <div class="flex items-center justify-center gap-4 mt-2">
                                                <a href="{{ route('movie.show', ['slug' => $movie['id'].'-'.\Illuminate\Support\Str::slug($movie['title'] ?? $movie['name'])]) }}" onclick="event.stopPropagation();" onpointerdown="event.stopPropagation();" class="inline-flex items-center gap-2 bg-white text-black px-5 py-3 rounded-md font-medium">Details
                                                </a>

                                                <a href="{{ route('movie.show', ['slug' => $movie['id'].'-'.\Illuminate\Support\Str::slug($movie['title'] ?? $movie['name'])]) }}" onclick="event.stopPropagation();" onpointerdown="event.stopPropagation();" class="inline-flex items-center gap-2 bg-viewlix-red hover:bg-viewlix-red-dark text-white px-5 py-3 rounded-md font-medium">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M6.5 5.5v9l7-4.5-7-4.5z"></path></svg>
                                                    Watch
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                      </div>

                      <button type="button" class="hs-carousel-prev hs-carousel-disabled:opacity-50 hs-carousel-disabled:pointer-events-none absolute inset-y-0 start-0 inline-flex justify-center items-center w-11.5 h-full text-white hover:bg-black/20 focus:outline-hidden focus:bg-black/10 rounded-s-lg">
                        <span class="text-2xl" aria-hidden="true">
                          <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m15 18-6-6 6-6"></path>
                          </svg>
                        </span>
                        <span class="sr-only">Previous</span>
                      </button>

                      <button type="button" class="hs-carousel-next hs-carousel-disabled:opacity-50 hs-carousel-disabled:pointer-events-none absolute inset-y-0 end-0 inline-flex justify-center items-center w-11.5 h-full text-white hover:bg-black/20 focus:outline-hidden focus:bg-black/10 rounded-e-lg">
                        <span class="sr-only">Next</span>
                        <span class="text-2xl" aria-hidden="true">
                          <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6"></path>
                          </svg>
                        </span>
                      </button>

                      <div class="hs-carousel-pagination flex justify-center absolute bottom-3 start-0 end-0 flex gap-x-2"></div>
                    </div>
                </div>
            </div>
        </section>
        @endif

        {{-- Continue Watching Section (from localStorage) --}}
        <div class="max-w-6xl mx-auto px-4">
            @include('partials.continue-watching')
        </div>
        
        {{-- Trending Movies --}}
        @if(isset($trendingMovies) && count($trendingMovies) > 0)
        <section class="py-8">
            <div class="max-w-6xl mx-auto px-4">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-white">🔥 Trending Movies</h2>
                    <a href="{{ route('movies') }}" class="text-sm text-viewlix-red hover:text-viewlix-red-light transition-colors">View All</a>
                </div>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                    @foreach(array_slice($trendingMovies, 0, 6) as $movie)
                    @include('partials.media-card', ['item' => $movie, 'type' => 'movie'])
                    @endforeach
                </div>
            </div>
        </section>
        @endif
        
        {{-- Top Rated Movies --}}
        @if(isset($topRatedMovies) && count($topRatedMovies) > 0)
        <section class="py-8">
            <div class="max-w-6xl mx-auto px-4">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-white">⭐ Top Rated</h2>
                    <a href="{{ route('movies', ['filter' => 'top_rated']) }}" class="text-sm text-viewlix-red hover:text-viewlix-red-light transition-colors">View All</a>
                </div>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                    @foreach(array_slice($topRatedMovies, 0, 6) as $movie)
                    @include('partials.media-card', ['item' => $movie, 'type' => 'movie'])
                    @endforeach
                </div>
            </div>
        </section>
        @endif
        
        {{-- Trending TV Shows --}}
        @if(isset($trendingTv) && count($trendingTv) > 0)
        <section class="py-8">
            <div class="max-w-6xl mx-auto px-4">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-white">� Trending TV Shows</h2>
                    <a href="{{ route('tv-shows') }}" class="text-sm text-viewlix-red hover:text-viewlix-red-light transition-colors">View All</a>
                </div>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                    @foreach(array_slice($trendingTv, 0, 6) as $show)
                    @include('partials.media-card', ['item' => $show, 'type' => 'tv'])
                    @endforeach
                </div>
            </div>
        </section>
        @endif
        
        {{-- Popular Anime --}}
        @if(isset($popularAnime) && count($popularAnime) > 0)
        <section class="py-8">
            <div class="max-w-6xl mx-auto px-4">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-white">🎌 Popular Anime</h2>
                    <a href="{{ route('anime') }}" class="text-sm text-viewlix-red hover:text-viewlix-red-light transition-colors">View All</a>
                </div>
                
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                    @foreach(array_slice($popularAnime, 0, 6) as $anime)
                    @include('partials.media-card', ['item' => $anime, 'type' => 'anime'])
                    @endforeach
                </div>
            </div>
        </section>
        @endif
        </div>

        
        <script src="{{ asset('js/index.js') }}"></script>

        @endsection

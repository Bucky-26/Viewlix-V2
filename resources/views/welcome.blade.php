    @extends('layouts.app')

        @section('title', 'Welcome to Viewlix')

        {{-- SEO: description, Open Graph, Twitter Card, canonical and JSON-LD. Pushed into the head via the `styles` stack. --}}
        @section('description', 'Discover, search, and watch the best movies, TV shows, and anime on Viewlix — curated trending picks, top rated titles, and a modern streaming experience.')

        @push('styles')
    {{-- Append file modification time to force browsers to fetch the latest CSS during development --}}
    <link rel="stylesheet" href="{{ url('css/main.css') }}?v={{ filemtime(public_path('css/main.css')) }}" />

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
        
                @endif
        
                {{-- Carousel: trending movies + tv (logos, backdrop, description, actions) --}}
                @if((isset($trendingMovies) && count($trendingMovies) > 0) || (isset($trendingTv) && count($trendingTv) > 0))
                <section class="py-6">
                        <div class="max-w-6xl mx-auto px-4">
                                <div data-hs-carousel='{
                                        "loadingClasses": "opacity-0",
                                        "dotsItemClasses": "hs-carousel-active:bg-viewlix-red hs-carousel-active:border-viewlix-red size-3 border border-gray-400 rounded-full cursor-pointer",
                                        "isAutoPlay": true
                                    }' class="relative">
                                    <div class="hs-carousel relative overflow-hidden w-full carousel-container min-h-96 md:min-h-96 bg-gray-900 rounded-lg">
                                        <div class="hs-carousel-body absolute inset-0 flex flex-nowrap h-full transition-transform duration-700 opacity-0">
                                            @php
                                                $slides = [];
                                                foreach(array_slice($trendingMovies ?? [], 0, 6) as $m) {
                                                        $slides[] = [
                                                                'id' => $m['id'] ?? null,
                                                                'title' => $m['title'] ?? $m['name'] ?? 'Untitled',
                                                                'type' => 'movie',
                                                                'backdrop' => $m['backdrop_path'] ?? $m['poster_path'] ?? null,
                                                                'overview' => $m['overview'] ?? ''
                                                        ];
                                                }
                                                foreach(array_slice($trendingTv ?? [], 0, 6) as $t) {
                                                        $slides[] = [
                                                                'id' => $t['id'] ?? null,
                                                                'title' => $t['name'] ?? $t['title'] ?? 'Untitled',
                                                                'type' => 'tv',
                                                                'backdrop' => $t['backdrop_path'] ?? $t['poster_path'] ?? null,
                                                                'overview' => $t['overview'] ?? ''
                                                        ];
                                                }
                                            @endphp

                                            @foreach($slides as $s)
                                            <div class="hs-carousel-slide w-full flex-shrink-0 h-full">
                                                <div class="relative flex items-center justify-center h-full rounded-lg overflow-hidden bg-gray-800">
                            @php
                            $bgUrl = '';
                            if(!empty($s['backdrop'])) {
                                // Use TMDBService to get original backdrop then proxy via wsrv.nl for consistent sizing
                                $tmdbBg = $tmdb->getImageUrl($s['backdrop'], 'original');
                                if($tmdbBg) {
                                    // Force a consistent width/height and center-crop using wsrv.nl
                                    // h-96 (Tailwind) ~= 24rem -> typically 384px. Use 1600x384 with fit=cover for widescreen crop.
                                    $bgUrl = 'https://wsrv.nl/?url=' . rawurlencode($tmdbBg) . '&w=1600&h=384&fit=cover&output=webp&q=80';
                                }
                            }
                            @endphp

                            @if($bgUrl)
                            <img src="{{ $bgUrl }}" alt="{{ $s['title'] }}" class="absolute inset-0 w-full h-full object-cover object-center" />
                                                    @else
                                                    <div class="absolute inset-0 bg-gradient-to-r from-black via-gray-900 to-black"></div>
                                                    @endif

                                                    <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/30 to-transparent"></div>

                                                    <div class="relative z-10 max-w-4xl mx-auto px-4 w-full flex flex-col items-center justify-center text-center">
                                                        @php
                                                            $logoUrl = '';
                                                            if($s['type'] === 'movie') {
                                                                $logoUrl = $tmdb->getMovieLogoUrl((int)$s['id'], 'US', 'w500');
                                                            } else {
                                                                $logoUrl = $tmdb->getTvLogoUrl((int)$s['id'], 'US', 'w500');
                                                            }
                                                        @endphp

                                                        <span class="inline-flex items-center carousel-trending-badge bg-viewlix-red text-sm px-3 py-1 rounded-full uppercase">Trending Now</span>

                                                        <div class="carousel-logo mt-2 md:mt-4">
                                                            @if($logoUrl)
                                                                <img src="{{ $logoUrl }}" alt="{{ $s['title'] }} logo" class="mx-auto h-20 md:h-28 object-contain" />
                                                            @else
                                                                <h2 class="carousel-title text-2xl md:text-4xl font-extrabold text-white">{{ $s['title'] }}</h2>
                                                            @endif
                                                        </div>

                                                        {{-- metadata row (year, rating, genres) - TMDB id lookup not performed here; placeholders if not available --}}
                                                        <div class="carousel-meta mt-2 md:mt-4 flex items-center gap-4 text-sm text-gray-200">
                                                            {{-- If you want year/rating/genres, fetch details in controller and pass to view. --}}
                                                            <span class="px-2 py-1 bg-black/40 rounded">2025</span>
                                                            <span class="px-2 py-1 bg-black/40 rounded">⭐ 7.6/10</span>
                                                            <span class="px-2 py-1 bg-black/40 rounded">Drama</span>
                                                        </div>

                                                        @php
                                                            $overview = $s['overview'];
                                                            if(strlen($overview) > 80) {
                                                                $overview = substr($overview, 0, 80) . '...';
                                                            }
                                                        @endphp
                                                        <p class="carousel-description text-gray-200 max-w-3xl leading-relaxed">{{ $overview }}</p>

                                                        <div class="carousel-buttons mt-4 md:mt-8 flex items-center justify-center gap-4">
                                                            @if($s['type'] === 'movie')
                                                                <a href="{{ route('movie.show', ['slug' => $s['id'] . '-' . \Illuminate\Support\Str::slug($s['title'])]) }}" class="px-5 py-3 bg-white text-black rounded shadow">Details</a>
                                                                <a href="{{ route('movie.show', ['slug' => $s['id'] . '-' . \Illuminate\Support\Str::slug($s['title'])]) }}" class="px-5 py-3 bg-viewlix-red text-white rounded shadow">Watch</a>
                                                            @else
                                                                <a href="{{ route('tv-show.show', ['slug' => $s['id'] . '-' . \Illuminate\Support\Str::slug($s['title'])]) }}" class="px-5 py-3 bg-white text-black rounded shadow">Details</a>
                                                                <a href="{{ route('tv-show.show', ['slug' => $s['id'] . '-' . \Illuminate\Support\Str::slug($s['title'])]) }}" class="px-5 py-3 bg-viewlix-red text-white rounded shadow">Watch</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <button type="button" class="hs-carousel-prev hs-carousel-disabled:opacity-50 hs-carousel-disabled:pointer-events-none absolute inset-y-0 start-0 inline-flex justify-center items-center w-11.5 h-full text-white hover:bg-white/10 focus:outline-hidden focus:bg-white/10 rounded-s-lg">
                                        <span class="text-2xl" aria-hidden="true">
                                            <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="m15 18-6-6 6-6"></path>
                                            </svg>
                                        </span>
                                        <span class="sr-only">Previous</span>
                                    </button>
                                    <button type="button" class="hs-carousel-next hs-carousel-disabled:opacity-50 hs-carousel-disabled:pointer-events-none absolute inset-y-0 end-0 inline-flex justify-center items-center w-11.5 h-full text-white hover:bg-white/10 focus:outline-hidden focus:bg-white/10 rounded-e-lg">
                                        <span class="sr-only">Next</span>
                                        <span class="text-2xl" aria-hidden="true">
                                            <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="m9 18 6-6-6-6"></path>
                                            </svg>
                                        </span>
                                    </button>
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

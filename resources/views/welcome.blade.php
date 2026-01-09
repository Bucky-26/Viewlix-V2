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
                    $ogImage = $tmdb->getImageUrl($trendingMovies[0]['backdrop_path'], 'original', true);
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
    <script src="{{ asset('js/home.js') }}"></script>

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
                    {{-- Hero / Trending carousel: dedicated responsive section that fills the current screen height --}}
                    <section id="hero-carousel" class="h-screen md:h-[80vh] lg:h-[85vh]">
                        <div class="max-w-6xl mx-auto px-4 h-full">
                            <div data-hero-carousel='{"loadingClasses":"opacity-0","dotsItemClasses":"hero-active:bg-viewlix-red hero-active:border-viewlix-red size-3 border border-gray-400 rounded-full cursor-pointer","isAutoPlay":true}' class="relative h-full">
                                <div class="hero-carousel-container relative overflow-hidden w-full carousel-container h-full bg-gray-900 rounded-lg">
                                    <div id="hero-carousel-body" class="hero-carousel-body absolute inset-0 h-full">
                                        <!-- Server-side skeleton placeholder so UI doesn't look empty before JS runs -->
                                        <div class="hero-carousel-slide absolute inset-0 h-full">
                                            <div class="relative h-full rounded-lg overflow-hidden bg-gray-800">
                                                <div class="absolute inset-0 bg-gradient-to-br from-gray-800 to-gray-700 animate-pulse" aria-hidden="true"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button" id="hero-prev" class="hs-carousel-prev hs-carousel-disabled:opacity-50 hs-carousel-disabled:pointer-events-none absolute inset-y-0 start-0 inline-flex justify-center items-center w-11.5 h-full text-white hover:bg-white/10 focus:outline-hidden focus:bg-white/10 rounded-s-lg">
                                    <span class="text-2xl" aria-hidden="true">
                                        <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m15 18-6-6 6-6"></path>
                                        </svg>
                                    </span>
                                    <span class="sr-only">Previous</span>
                                </button>
                                <button type="button" id="hero-next" class="hs-carousel-next hs-carousel-disabled:opacity-50 hs-carousel-disabled:pointer-events-none absolute inset-y-0 end-0 inline-flex justify-center items-center w-11.5 h-full text-white hover:bg-white/10 focus:outline-hidden focus:bg-white/10 rounded-e-lg">
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

                @push('styles')
                <style>
                    /* Scoped styles to create a fade transition carousel independent from Preline */
                    #hero-carousel .hero-carousel-body { position: relative; }
                    /* 5s fade transition as requested */
                    #hero-carousel .hero-carousel-slide { position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; transition: opacity 5s ease-in-out; }
                    /* Active slide shown */
                    #hero-carousel .hero-carousel-slide.active { opacity: 1; z-index: 10; }
                    /* Ensure background image covers the slide */
                    #hero-carousel img.object-cover { width: 100%; height: 100%; object-fit: cover; }
                </style>
                @endpush

                <!-- Hero behavior is handled by external JS (public/js/home.js) -->

        {{-- Continue Watching Section (from localStorage) --}}
        <div class="max-w-6xl mx-auto px-4">
            @include('partials.continue-watching')
        </div>
        
        {{-- Trending Movies (rendered client-side) --}}
        <section class="py-8">
            <div class="max-w-6xl mx-auto px-4">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-white">🔥 Trending Movies</h2>
                    <a href="{{ route('movies') }}" class="text-sm text-viewlix-red hover:text-viewlix-red-light transition-colors">View All</a>
                </div>

                <div id="trending-movies-grid">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                        @for($i = 0; $i < 6; $i++)
                            <div class="group relative bg-viewlix-gray rounded-xl overflow-hidden border border-viewlix-border">
                                <div class="aspect-[2/3] bg-gradient-to-br from-gray-800 to-gray-700 animate-pulse" aria-hidden="true"></div>
                                <div class="md:hidden p-2">
                                    <div class="h-3 bg-gray-700 rounded w-3/4 mb-2 animate-pulse"></div>
                                    <div class="h-2 bg-gray-700 rounded w-1/4 animate-pulse"></div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </section>
        
        {{-- Top Rated Movies (rendered client-side) --}}
        <section class="py-8">
            <div class="max-w-6xl mx-auto px-4">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-white">⭐ Top Rated</h2>
                    <a href="{{ route('movies', ['filter' => 'top_rated']) }}" class="text-sm text-viewlix-red hover:text-viewlix-red-light transition-colors">View All</a>
                </div>

                <div id="top-rated-movies-grid">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                        @for($i = 0; $i < 6; $i++)
                            <div class="group relative bg-viewlix-gray rounded-xl overflow-hidden border border-viewlix-border">
                                <div class="aspect-[2/3] bg-gradient-to-br from-gray-800 to-gray-700 animate-pulse" aria-hidden="true"></div>
                                <div class="md:hidden p-2">
                                    <div class="h-3 bg-gray-700 rounded w-3/4 mb-2 animate-pulse"></div>
                                    <div class="h-2 bg-gray-700 rounded w-1/4 animate-pulse"></div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </section>
        
        {{-- Trending TV Shows (rendered client-side) --}}
        <section class="py-8">
            <div class="max-w-6xl mx-auto px-4">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-white">Trending TV Shows</h2>
                    <a href="{{ route('tv-shows') }}" class="text-sm text-viewlix-red hover:text-viewlix-red-light transition-colors">View All</a>
                </div>

                <div id="trending-tv-grid">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                        @for($i = 0; $i < 6; $i++)
                            <div class="group relative bg-viewlix-gray rounded-xl overflow-hidden border border-viewlix-border">
                                <div class="aspect-[2/3] bg-gradient-to-br from-gray-800 to-gray-700 animate-pulse" aria-hidden="true"></div>
                                <div class="md:hidden p-2">
                                    <div class="h-3 bg-gray-700 rounded w-3/4 mb-2 animate-pulse"></div>
                                    <div class="h-2 bg-gray-700 rounded w-1/4 animate-pulse"></div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </section>
        
        {{-- Popular Anime (rendered client-side) --}}
        <section class="py-8">
            <div class="max-w-6xl mx-auto px-4">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-white">🎌 Popular Anime</h2>
                    <a href="{{ route('anime') }}" class="text-sm text-viewlix-red hover:text-viewlix-red-light transition-colors">View All</a>
                </div>

                <div id="popular-anime-grid">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                        @for($i = 0; $i < 6; $i++)
                            <div class="group relative bg-viewlix-gray rounded-xl overflow-hidden border border-viewlix-border">
                                <div class="aspect-[2/3] bg-gradient-to-br from-gray-800 to-gray-700 animate-pulse" aria-hidden="true"></div>
                                <div class="md:hidden p-2">
                                    <div class="h-3 bg-gray-700 rounded w-3/4 mb-2 animate-pulse"></div>
                                    <div class="h-2 bg-gray-700 rounded w-1/4 animate-pulse"></div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>
        </section>
        </div>

        

        @endsection

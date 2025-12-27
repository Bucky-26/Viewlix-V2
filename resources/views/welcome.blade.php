    @extends('layouts.app')

    @section('title', 'Welcome to Viewlix')

    @section('content')
    <div class="lg:min-h-[calc(100vh-200px)]">

        
        {{-- Trending Now Carousel (Preline) --}}
        @if(isset($trendingMovies) && count($trendingMovies) > 0)
        <section class="py-6">
            <div class="max-w-7xl mx-auto px-4">
                <div class="relative">
                    <div class="hs-carousel max-w-6xl mx-auto" data-hs-carousel='{"isAutoPlay":true,"isInfiniteLoop":true,"slidesQty":1,"speed":6000}'>
                        <div class="hs-carousel-body flex gap-4">
                            @foreach(array_slice($trendingMovies, 0, 8) as $movie)
                            <div class="hs-carousel-slide min-w-full flex-shrink-0 rounded-xl overflow-hidden relative transition-opacity duration-700 ease-in-out will-change-transform">
                                <div role="link" tabindex="0" aria-label="Open details for {{ $movie['title'] ?? $movie['name'] }}" class="block relative group viewlix-clickable-card" data-url="{{ route('movie.show', $movie['id']) }}">
                                    @if(isset($tmdb) && ($movie['backdrop_path'] ?? null))
                                    {{-- Fixed-size backdrop: explicit heights per breakpoint and no hover-scaling so the visible size is consistent across devices --}}
                                    <img src="{{ $tmdb->getImageUrl($movie['backdrop_path'], 'original') }}" alt="{{ $movie['title'] ?? $movie['name'] }}" class="w-full h-[260px] sm:h-[360px] md:h-[520px] lg:h-[720px] object-cover transition-opacity duration-700" loading="lazy">
                                    @else
                                    <div class="w-full h-[260px] sm:h-[360px] md:h-[520px] lg:h-[720px] bg-viewlix-darker"></div>
                                    @endif

                                    {{-- Centered overlay similar to Preline hero carousel --}}
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent flex items-center justify-center">
                                        <div class="max-w-3xl text-center px-4 sm:px-6 py-6 sm:py-8 md:py-12">
                                            <span class="inline-block bg-white/10 text-white text-sm rounded-full px-3 py-1 mb-4">Trending Now</span>

                                            <h3 class="text-2xl sm:text-3xl md:text-5xl lg:text-6xl font-extrabold text-white tracking-tight mb-4">{{ $movie['title'] ?? $movie['name'] }}</h3>

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

                                            <p class="text-gray-300 hidden md:block mb-6">{{ \Illuminate\Support\Str::limit($movie['overview'] ?? '', 220) }}</p>

                                            <div class="flex items-center justify-center gap-4">
                                                <a href="{{ route('movie.show', $movie['id']) }}" onclick="event.stopPropagation();" onpointerdown="event.stopPropagation();" class="inline-flex items-center gap-2 bg-white text-black px-5 py-3 rounded-md font-medium">Details
                                                </a>

                                                <a href="{{ route('movie.show', $movie['id']) }}" onclick="event.stopPropagation();" onpointerdown="event.stopPropagation();" class="inline-flex items-center gap-2 bg-viewlix-red hover:bg-viewlix-red-dark text-white px-5 py-3 rounded-md font-medium">
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

                    {{-- Controls --}}
                    <button type="button" aria-label="Previous slide" title="Previous" class="hs-carousel-prev absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 sm:w-10 sm:h-10 bg-black/50 hover:bg-black/70 rounded-full flex items-center justify-center text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <button type="button" aria-label="Next slide" title="Next" class="hs-carousel-next absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 sm:w-10 sm:h-10 bg-black/50 hover:bg-black/70 rounded-full flex items-center justify-center text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>

                    <div class="hs-carousel-pagination absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-2"></div>
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

        {{-- Smooth carousel transition + swipe/pointer support --}}
        <style>
            /* Hero logo sizing: make the container a fixed height per breakpoint and force the image to fill 100% height
            This ensures the logo's height is exactly the container height (no gap) while preserving aspect ratio. */
            .viewlix-hero-logo {
                position: relative;
                height: auto; /* base — reduced for a smaller logo */
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
            }

            .viewlix-hero-logo-shadow {
                position: absolute;
                left: 50%;
                top: 50%;
                transform: translate(-50%, -50%);
                border-radius: 9999px;
                filter: blur(20px);
                opacity: 0.8;
                width: 10rem;
                height: 2.6rem;
                background: radial-gradient(ellipse at center, rgba(0,0,0,0.65) 0%, rgba(0,0,0,0.25) 40%, rgba(0,0,0,0) 100%);
                pointer-events: none;
            }

            /* Force the logo image to occupy full container height while keeping width auto (preserve aspect ratio). */
            .viewlix-hero-logo-img {
                display: block;
                height: 100%;
                width: auto;
                max-width: 50%; /* tighten width so logo doesn't span the hero */
            }

            /* Slightly larger on small screens and above */
            @media (min-width: 640px) {
                .viewlix-hero-logo { height: 40px; }
                .viewlix-hero-logo-shadow { width: 10rem; height: 2.6rem; }
            }

            @media (min-width: 768px) {
                .viewlix-hero-logo { height: 48px; }
                .viewlix-hero-logo-shadow { width: 12rem; height: 3rem; }
            }

            @media (min-width: 1024px) {
                /* Keep desktop logo modest so it doesn't dominate the hero */
                .viewlix-hero-logo { height: 56px; }
                .viewlix-hero-logo-shadow { width: 14rem; height: 3.4rem; }
            }

            /* Smooth transform for carousel movement */
            .hs-carousel-body {
                transition: transform 700ms cubic-bezier(.2,.8,.2,1) !important;
            }

            /* Smooth transitions for slides and images */
            .hs-carousel-slide {
                transition: transform 700ms cubic-bezier(.2,.8,.2,1), opacity 400ms ease !important;
                will-change: transform, opacity;
                box-sizing: border-box;
            }
            .hs-carousel-slide img {
                transition: transform 700ms cubic-bezier(.2,.8,.2,1) !important;
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            /* Prevent the page from jumping when slides change by forcing a fixed responsive height
                that matches the Tailwind utility heights used on the images (h-[260px], sm:h-[360px], md:h-[520px], lg:h-[720px]) */
            .hs-carousel {
                position: relative;
                overflow: hidden;
                /* DON'T change display; Preline assumes default block behavior. Previously setting
                    display:flex caused the carousel body to calculate widths incorrectly and show
                    adjacent slides. */
                display: block;
            }

            /* Base height (mobile) */
            .hs-carousel .hs-carousel-body {
                height: 260px;
            }

            /* Small screens */
            @media (min-width: 640px) {
                .hs-carousel .hs-carousel-body { height: 360px; }
            }

            /* Medium screens */
            @media (min-width: 768px) {
                .hs-carousel .hs-carousel-body { height: 520px; }
            }

            /* Large screens */
            @media (min-width: 1024px) {
                .hs-carousel .hs-carousel-body { height: 720px; }
            }

            /* Ensure slides fill the carousel height and occupy exactly 100% of the carousel width
            so only one slide is visible at a time. Don't use auto margins which previously caused
            partial slides to appear. */
            .hs-carousel .hs-carousel-body {
                width: 100%;
                height: 100%;
                display: flex; /* body should remain flex so slides line up horizontally for transforms */
                align-items: center;
            }

            .hs-carousel .hs-carousel-body > .hs-carousel-slide {
                /* Force each slide to be exactly the carousel width */
                flex: 0 0 100% !important;
                max-width: 100%;
                margin: 0 !important;
                height: 100%;
            }

            /* Keep carousel content within page padding */
            .hs-carousel .hs-carousel-body { max-width: calc(100% - 2rem); margin: 0 auto; }

            /* Make the whole slide feel clickable */
            .viewlix-clickable-card { cursor: pointer; }
            
            /* Mobile: make the hero carousel feel full-screen */
            @media (max-width: 639px) {
                /* Let the carousel occupy the full viewport height on phones */
                .hs-carousel {
                    height: 80vh;
                    overflow: hidden;
                }

                /* Carousel body and slides should fill the viewport height */
                .hs-carousel .hs-carousel-body {
                    height: 100vh !important;
                    width: 100%;
                    max-width: 100%;
                    display: flex;
                    align-items: center;
                    margin: 0 auto;
                }

                .hs-carousel .hs-carousel-body > .hs-carousel-slide {
                    height: 100vh !important;
                    flex: 0 0 100% !important;
                    max-width: 100% !important;
                }

                /* Force images to cover the viewport and preserve aspect ratio */
                .hs-carousel .hs-carousel-body img {
                    height: 100vh !important;
                    width: 100% !important;
                    object-fit: cover !important;
                }

                        /* Responsive hero logo sizing for mobile: keep the logo modest on very small phones
                            - reduce the minimum to avoid oversized logos on narrow screens
                            - prefer a slightly larger scale on mid-size phones, but cap the max */
                        .viewlix-hero-logo { height: clamp(28px, 9vw, 48px); }
                        /* Limit image width so it doesn't span the hero; ensure height scales naturally */
                        .viewlix-hero-logo-img { max-width: clamp(40%, 60%, 75%); height: auto; }
                        /* Shadow should scale with logo but remain subtle */
                        .viewlix-hero-logo-shadow { width: clamp(6.5rem, 14vw, 12rem); height: clamp(1.4rem, 3.2vw, 2.8rem); }
            }
        </style>

        <script>
            (function(){
                // Basic swipe and pointer support for hs-carousel
                document.querySelectorAll('.hs-carousel').forEach(function(carousel){
                    var startX = 0;
                    var startTime = 0;
                    var threshold = 50; // px needed to qualify as swipe
                    var allowedTime = 1000; // max time allowed to take for a swipe (ms)

                    // Touch events (mobile)
                    carousel.addEventListener('touchstart', function(e){
                        if(e.touches.length !== 1) return;
                        startX = e.touches[0].clientX;
                        startTime = Date.now();
                    }, {passive:true});

                    carousel.addEventListener('touchend', function(e){
                        var dx = e.changedTouches[0].clientX - startX;
                        var dt = Date.now() - startTime;
                        if(Math.abs(dx) >= threshold && dt <= allowedTime){
                            if(dx < 0){ // swipe left -> next
                                var next = carousel.querySelector('.hs-carousel-next');
                                if(next) next.click();
                            } else { // swipe right -> prev
                                var prev = carousel.querySelector('.hs-carousel-prev');
                                if(prev) prev.click();
                            }
                        }
                    }, {passive:true});

                    // Pointer events (desktop dragging / pen)
                    var pointerActive = false;
                    var pointerId = null;
                    carousel.addEventListener('pointerdown', function(e){
                        // only left button or primary pointer
                        if(e.isPrimary === false) return;
                        pointerActive = true;
                        pointerId = e.pointerId;
                        startX = e.clientX;
                        startTime = Date.now();
                        try{ carousel.setPointerCapture(pointerId); }catch(err){}
                    });

                    carousel.addEventListener('pointerup', function(e){
                        if(!pointerActive || e.pointerId !== pointerId) return;
                        pointerActive = false;
                        var dx = e.clientX - startX;
                        var dt = Date.now() - startTime;
                        if(Math.abs(dx) >= threshold && dt <= allowedTime){
                            if(dx < 0){ carousel.querySelector('.hs-carousel-next')?.click(); }
                            else { carousel.querySelector('.hs-carousel-prev')?.click(); }
                        }
                        try{ carousel.releasePointerCapture(pointerId); }catch(err){}
                    });

                    // If the carousel auto-plays, prevent accidental triggers when a user is scrolling vertically
                    // We use a small check on the touchmove to detect primarily-horizontal movement.
                    var moved = false;
                    var lastMoveX = 0;
                    carousel.addEventListener('touchmove', function(e){
                        if(e.touches.length !== 1) return;
                        var moveX = e.touches[0].clientX;
                        var moveY = e.touches[0].clientY;
                        var dx = Math.abs(moveX - startX);
                        // if horizontal movement exceeds small amount, prevent default to avoid page-snap on some devices
                        if(dx > 10){
                            moved = true;
                            lastMoveX = moveX;
                        }
                    }, {passive:true});
                });

                // Attach click handlers directly to each card — more reliable for mouse events and avoids
                // interference from other global handlers.
                document.querySelectorAll('.viewlix-clickable-card').forEach(function(card){
                    card.addEventListener('click', function(e){
                        // If the click originated from a real interactive element, let it proceed
                        if(e.target.closest && e.target.closest('a, button, input')) return;
                        var url = card.getAttribute('data-url');
                        if(url) window.location.href = url;
                    });
                });

                // keyboard support: Enter or Space activates the card when focused
                document.addEventListener('keydown', function(e){
                    var el = document.activeElement;
                    if(!el || !el.classList.contains('viewlix-clickable-card')) return;
                    if(e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        var url = el.getAttribute('data-url');
                        if(url) window.location.href = url;
                    }
                });
            })();
        </script>

        @endsection

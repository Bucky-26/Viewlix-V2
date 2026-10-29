{{-- Streaming Providers Panel --}}
<section class="relative pt-2" id="streaming-providers-section">
    {{-- Top Controls Bar: [List of Providers] [Movie | TV Show Toggle] --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 sm:gap-4 mb-4 sm:mb-5">
        
        {{-- [List of Providers] Horizontal Track --}}
        <div class="flex items-center gap-2 sm:gap-2.5 overflow-x-auto modern-scrollbar scroll-smooth pt-1 pb-2 flex-1 min-w-0" id="provider-tabs-track">
            
            {{-- Netflix (Default Active) --}}
            <button type="button" 
                    data-provider="netflix" 
                    data-provider-name="Netflix"
                    data-search-query="Netflix"
                    data-active-color="border-red-500 bg-red-950/30 text-white shadow-[0_0_20px_rgba(229,9,20,0.35)]"
                    class="provider-tab-btn active group relative flex-shrink-0 px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl border border-red-500/80 bg-red-950/30 text-white shadow-[0_0_20px_rgba(229,9,20,0.35)] backdrop-blur-xl transition-all duration-200 hover:scale-[1.02] flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500 shadow-[0_0_6px_#E50914] provider-indicator"></span>
                <span class="text-xs sm:text-sm font-black tracking-wider text-[#E50914] font-sans">NETFLIX</span>
            </button>

            {{-- Disney+ --}}
            <button type="button" 
                    data-provider="disney" 
                    data-provider-name="Disney+"
                    data-search-query="Disney"
                    data-active-color="border-blue-500 bg-blue-950/30 text-white shadow-[0_0_20px_rgba(59,130,246,0.35)]"
                    class="provider-tab-btn group relative flex-shrink-0 px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl border border-white/10 bg-[#0d0e17]/85 text-gray-400 hover:text-white hover:border-white/20 backdrop-blur-xl transition-all duration-200 hover:scale-[1.02] flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 shadow-[0_0_6px_#3B82F6] provider-indicator hidden"></span>
                <span class="font-serif italic text-xs sm:text-sm font-black tracking-tighter text-white">Disney<span class="text-blue-400 font-sans font-bold">+</span></span>
            </button>

            {{-- HBO Max --}}
            <button type="button" 
                    data-provider="hbo" 
                    data-provider-name="HBO Max"
                    data-search-query="HBO"
                    data-active-color="border-indigo-500 bg-indigo-950/30 text-white shadow-[0_0_20px_rgba(99,102,241,0.35)]"
                    class="provider-tab-btn group relative flex-shrink-0 px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl border border-white/10 bg-[#0d0e17]/85 text-gray-400 hover:text-white hover:border-white/20 backdrop-blur-xl transition-all duration-200 hover:scale-[1.02] flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 shadow-[0_0_6px_#6366F1] provider-indicator hidden"></span>
                <span class="text-xs sm:text-sm font-black tracking-tight text-white font-sans">HBO <span class="bg-gradient-to-r from-blue-400 to-indigo-400 bg-clip-text text-transparent">MAX</span></span>
            </button>

            {{-- Prime Video --}}
            <button type="button" 
                    data-provider="prime" 
                    data-provider-name="Prime Video"
                    data-search-query="Prime Video"
                    data-active-color="border-cyan-500 bg-cyan-950/30 text-white shadow-[0_0_20px_rgba(6,182,212,0.35)]"
                    class="provider-tab-btn group relative flex-shrink-0 px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl border border-white/10 bg-[#0d0e17]/85 text-gray-400 hover:text-white hover:border-white/20 backdrop-blur-xl transition-all duration-200 hover:scale-[1.02] flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 shadow-[0_0_6px_#22D3EE] provider-indicator hidden"></span>
                <span class="text-xs sm:text-sm font-bold tracking-tight text-white font-sans">prime <span class="text-cyan-400">video</span></span>
            </button>

            {{-- Apple TV+ --}}
            <button type="button" 
                    data-provider="apple" 
                    data-provider-name="Apple TV+"
                    data-search-query="Apple TV"
                    data-active-color="border-white/40 bg-white/10 text-white shadow-[0_0_20px_rgba(255,255,255,0.2)]"
                    class="provider-tab-btn group relative flex-shrink-0 px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl border border-white/10 bg-[#0d0e17]/85 text-gray-400 hover:text-white hover:border-white/20 backdrop-blur-xl transition-all duration-200 hover:scale-[1.02] flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-white shadow-[0_0_6px_#ffffff] provider-indicator hidden"></span>
                <span class="text-xs sm:text-sm font-bold tracking-tight text-white font-sans flex items-center gap-1">
                    <svg class="w-3 h-3.5 fill-current" viewBox="0 0 170 170"><path d="M150.37 130.25c-2.45 5.66-5.35 10.87-8.71 15.66-4.58 6.53-8.33 11.05-11.22 13.56-4.48 4.12-9.28 6.23-14.42 6.35-3.69 0-8.14-1.05-13.32-3.18-5.19-2.12-9.97-3.17-14.34-3.17-4.58 0-9.49 1.05-14.75 3.17-5.26 2.13-9.5 3.24-12.74 3.35-4.35.13-9.16-1.9-14.42-6.08-3.69-3.08-7.7-7.98-12.01-14.7-5.88-9.04-10.43-19.18-13.65-30.42-3.22-11.23-4.83-21.89-4.83-31.98 0-14.36 3.66-26.27 10.98-35.73 7.32-9.46 16.48-14.28 27.48-14.47 5.09 0 10.74 1.34 16.96 4.01 6.21 2.68 10.15 4.07 11.8 4.18 1.43 0 5.43-1.39 12.01-4.18 6.58-2.79 12.33-4.04 17.26-3.76 13.88.75 25.13 5.76 33.77 15.03-12.16 7.42-18.12 17.43-17.88 30.04.24 9.87 3.99 18.06 11.25 24.58 7.26 6.51 16.03 10.22 26.31 11.13-2.12 6.44-4.8 12.73-8.08 18.86zM119.22 33.58c0-7.14 2.65-13.9 7.95-20.27 5.3-6.38 11.78-10.42 19.44-12.14.33 1.09.5 2.18.5 3.27 0 7.02-2.78 13.82-8.35 20.4-5.56 6.59-12.14 10.56-19.74 11.91-.07-.79-.1-1.85-.1-3.17z"/></svg>
                    <span>tv+</span>
                </span>
            </button>

            {{-- Paramount+ --}}
            <button type="button" 
                    data-provider="paramount" 
                    data-provider-name="Paramount+"
                    data-search-query="Paramount"
                    data-active-color="border-blue-600 bg-blue-950/30 text-white shadow-[0_0_20px_rgba(37,99,235,0.35)]"
                    class="provider-tab-btn group relative flex-shrink-0 px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl border border-white/10 bg-[#0d0e17]/85 text-gray-400 hover:text-white hover:border-white/20 backdrop-blur-xl transition-all duration-200 hover:scale-[1.02] flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 shadow-[0_0_6px_#2563EB] provider-indicator hidden"></span>
                <span class="text-xs sm:text-sm font-black tracking-tight text-white font-sans">Paramount<span class="text-blue-500 font-bold">+</span></span>
            </button>

            {{-- Hulu --}}
            <button type="button" 
                    data-provider="hulu" 
                    data-provider-name="Hulu"
                    data-search-query="Hulu"
                    data-active-color="border-emerald-500 bg-emerald-950/30 text-white shadow-[0_0_20px_rgba(16,185,129,0.35)]"
                    class="provider-tab-btn group relative flex-shrink-0 px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl border border-white/10 bg-[#0d0e17]/85 text-gray-400 hover:text-white hover:border-white/20 backdrop-blur-xl transition-all duration-200 hover:scale-[1.02] flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_6px_#10B981] provider-indicator hidden"></span>
                <span class="text-xs sm:text-sm font-black tracking-wider text-[#1ce783] font-sans lowercase">hulu</span>
            </button>

            {{-- Crunchyroll --}}
            <button type="button" 
                    data-provider="crunchyroll" 
                    data-provider-name="Crunchyroll"
                    data-search-query="Crunchyroll"
                    data-active-color="border-orange-500 bg-orange-950/30 text-white shadow-[0_0_20px_rgba(249,115,22,0.35)]"
                    class="provider-tab-btn group relative flex-shrink-0 px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl border border-white/10 bg-[#0d0e17]/85 text-gray-400 hover:text-white hover:border-white/20 backdrop-blur-xl transition-all duration-200 hover:scale-[1.02] flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-orange-500 shadow-[0_0_6px_#F97316] provider-indicator hidden"></span>
                <span class="text-xs sm:text-sm font-black tracking-tight text-[#F47521] font-sans">Crunchyroll</span>
            </button>

            {{-- Peacock --}}
            <button type="button" 
                    data-provider="peacock" 
                    data-provider-name="Peacock"
                    data-search-query="Peacock"
                    data-active-color="border-teal-500 bg-teal-950/30 text-white shadow-[0_0_20px_rgba(20,184,166,0.35)]"
                    class="provider-tab-btn group relative flex-shrink-0 px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl border border-white/10 bg-[#0d0e17]/85 text-gray-400 hover:text-white hover:border-white/20 backdrop-blur-xl transition-all duration-200 hover:scale-[1.02] flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-teal-400 shadow-[0_0_6px_#2DD4BF] provider-indicator hidden"></span>
                <span class="text-xs sm:text-sm font-black tracking-wider text-white font-sans lowercase">peacock</span>
            </button>

            {{-- Vivamax --}}
            <button type="button" 
                    data-provider="vivamax" 
                    data-provider-name="Vivamax"
                    data-search-query="Vivamax"
                    data-active-color="border-amber-500 bg-amber-950/30 text-white shadow-[0_0_20px_rgba(245,158,11,0.35)]"
                    class="provider-tab-btn group relative flex-shrink-0 px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl border border-white/10 bg-[#0d0e17]/85 text-gray-400 hover:text-white hover:border-white/20 backdrop-blur-xl transition-all duration-200 hover:scale-[1.02] flex items-center gap-2">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 shadow-[0_0_6px_#F59E0B] provider-indicator hidden"></span>
                <span class="text-xs sm:text-sm font-black tracking-tight text-[#f39c12] font-sans">VIVAMAX</span>
            </button>
        </div>

        {{-- [Movie | TV Show] Toggle Switch --}}
        <div class="flex items-center self-start lg:self-center shrink-0 p-1 rounded-xl bg-[#0e0f18]/90 border border-white/10 shadow-inner backdrop-blur-md">
            <button type="button" 
                    id="provider-type-movie" 
                    data-type="movie"
                    class="provider-type-btn active flex items-center gap-1.5 px-3 sm:px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all text-white bg-gradient-to-r from-viewlix-red to-blue-600 shadow-md">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/>
                </svg>
                <span>Movies</span>
            </button>
            <button type="button" 
                    id="provider-type-tv" 
                    data-type="tv"
                    class="provider-type-btn flex items-center gap-1.5 px-3 sm:px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all text-gray-400 hover:text-white">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span>TV Shows</span>
            </button>
        </div>
    </div>

    {{-- Sub-header: Active Status & Direct Search Link --}}
    <div class="flex items-center justify-between mb-3.5 text-xs">
        <div class="flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
            <h3 id="provider-content-heading" class="font-extrabold text-white tracking-wide text-sm sm:text-base">
                Top Movies on <span class="text-white">Netflix</span>
            </h3>
            <span id="provider-results-badge" class="hidden sm:inline-block px-2 py-0.5 rounded-full bg-white/5 border border-white/10 text-gray-400 text-[10px] uppercase font-semibold">
                Popular Catalog
            </span>
        </div>

        <a id="provider-explore-link" 
           href="{{ route('search', ['q' => 'Netflix']) }}" 
           class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-white transition-colors group">
            <span>Explore All</span>
            <svg class="w-3 h-3 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>

    {{-- [LIST] Media Content Shelf --}}
    <div id="provider-media-grid" class="min-h-[260px]">
        {{-- Skeleton Loaders for Initial Load --}}
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

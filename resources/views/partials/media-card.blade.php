@php
    $title = $item['title'] ?? $item['name'] ?? 'Unknown';
    $date = $item['release_date'] ?? $item['first_air_date'] ?? '';
    $year = $date ? substr($date, 0, 4) : '';
    $rating = $item['vote_average'] ?? 0;
    $posterPath = $item['poster_path'] ?? null;
    $mediaType = $type === 'anime' ? 'anime' : ($type === 'tv' ? 'tv' : 'movie');
    
    // Determine route based on type (use slug: {id}-{slugified-title})
    $route = match($type) {
        'movie' => route('movie.show', ['slug' => $item['id'] . '-' . \Illuminate\Support\Str::slug($title)]),
        'tv' => route('tv-show.show', ['slug' => $item['id'] . '-' . \Illuminate\Support\Str::slug($title)]),
        'anime' => route('anime.show', $item['id']),
        default => '#'
    };
@endphp

<div class="group relative bg-[#111116] rounded-2xl overflow-hidden border border-white/5 hover:border-blue-500/50 hover:shadow-[0_12px_30px_rgba(0,0,0,0.8),0_0_20px_rgba(0,74,173,0.25)] transition-all duration-300 flex flex-col">
    <a href="{{ $route }}" class="block relative overflow-hidden aspect-[2/3] bg-zinc-900">
        @if($posterPath && isset($tmdb))
            {{-- Provide intrinsic image dimensions to avoid layout shifts (w342 -> 2:3 aspect => 342x513) --}}
            <img src="{{ $tmdb->getImageUrl($posterPath, 'w342', true) }}" 
                 alt="{{ $title }}" 
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                 width="342" height="513" decoding="async" loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center bg-zinc-900 text-gray-600">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
        @endif

        {{-- Gradient Scrim (for text readability) --}}
        <div class="absolute inset-0 bg-gradient-to-t from-[#0d0d12] via-transparent to-transparent opacity-80 group-hover:opacity-60 transition-opacity"></div>

        {{-- Top Left: Rating Badge --}}
        @if($rating > 0)
        <div class="absolute top-2.5 left-2.5 flex items-center gap-1 px-2 py-0.5 bg-black/75 backdrop-blur-md border border-white/10 rounded-lg text-[11px] shadow-md">
            <svg class="w-3 h-3 text-amber-400 fill-current" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
            </svg>
            <span class="text-white font-bold">{{ number_format($rating, 1) }}</span>
        </div>
        @endif

        {{-- Top Right: Quick Action Icons --}}
        <div class="absolute top-2.5 right-2.5 flex flex-col gap-1.5 opacity-0 group-hover:opacity-100 transition-all duration-200 translate-x-1 group-hover:translate-x-0">
            <button type="button" 
                    onclick="event.preventDefault(); event.stopPropagation(); openPreview({{ $item['id'] }}, '{{ $mediaType }}')"
                    class="w-7 h-7 rounded-lg bg-black/80 hover:bg-viewlix-red text-white flex items-center justify-center shadow-lg transition-colors"
                    title="Quick Preview"
                    aria-label="Quick Preview">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
            </button>
            
            <button type="button" 
                    onclick="event.preventDefault(); event.stopPropagation(); openShare('{{ addslashes($title) }}', 'Check out {{ addslashes($title) }} on Viewlix!', '{{ url()->to($route) }}')"
                    class="w-7 h-7 rounded-lg bg-black/80 hover:bg-viewlix-red text-white flex items-center justify-center shadow-lg transition-colors"
                    title="Share"
                    aria-label="Share">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                </svg>
            </button>
        </div>

        {{-- Center Play Button (On Hover) --}}
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
            <div class="w-11 h-11 rounded-full bg-viewlix-red/90 text-white shadow-xl shadow-viewlix-red/50 flex items-center justify-center transform scale-0 group-hover:scale-100 transition-transform duration-200">
                <svg class="w-5 h-5 ml-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M6.3 2.841A1.5 1.5 0 004 4.11v11.78a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                </svg>
            </div>
        </div>
    </a>

    {{-- Media Info (Bottom) --}}
    <div class="p-3 bg-[#101015]">
        <h3 class="text-xs sm:text-sm font-semibold text-white truncate group-hover:text-blue-400 transition-colors" title="{{ $title }}">
            {{ $title }}
        </h3>
        <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1">
            <span>{{ $year ?: 'N/A' }}</span>
            <span class="uppercase font-bold tracking-wider text-[10px] px-1.5 py-0.5 rounded bg-white/5 border border-white/5 text-gray-300">
                {{ $mediaType }}
            </span>
        </div>
    </div>
</div>

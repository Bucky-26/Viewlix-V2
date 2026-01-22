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

<div class="group relative bg-viewlix-gray rounded-xl overflow-hidden border border-viewlix-border hover:border-viewlix-red transition-colors">
    <a href="{{ $route }}">
        @if($posterPath && isset($tmdb))
            {{-- Provide intrinsic image dimensions to avoid layout shifts (w342 -> 2:3 aspect => 342x513) --}}
            <img src="{{ $tmdb->getImageUrl($posterPath, 'w342', true) }}" 
                 alt="{{ $title }}" 
                 class="aspect-[2/3] w-full object-contain md:object-cover"
                 width="342" height="513" decoding="async" loading="lazy">
        @else
            <div class="aspect-[2/3] bg-viewlix-darker flex items-center justify-center">
                <svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
        @endif
    </a>
    
    {{-- Rating Badge --}}
    @if($rating > 0)
    <div class="absolute top-2 left-2 flex items-center gap-1 px-2 py-1 bg-black/70 rounded-lg text-xs">
        <svg class="w-3 h-3 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
        </svg>
        <span class="text-white font-medium">{{ number_format($rating, 1) }}</span>
    </div>
    @endif
    
    {{-- Quick Actions (Mobile: visible, Desktop: on hover) --}}
    <div class="absolute top-2 right-2 flex flex-col gap-1 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
        {{-- Preview Button --}}
        <button type="button" 
                onclick="event.preventDefault(); event.stopPropagation(); openPreview({{ $item['id'] }}, '{{ $mediaType }}')"
                class="w-8 h-8 bg-black/70 hover:bg-viewlix-red rounded-lg flex items-center justify-center transition-colors"
                title="Quick Preview">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
        </button>
        
        {{-- Share Button --}}
        <button type="button" 
                onclick="event.preventDefault(); event.stopPropagation(); openShare('{{ addslashes($title) }}', 'Check out {{ addslashes($title) }} on Viewlix!', '{{ url()->to($route) }}')"
                class="w-8 h-8 bg-black/70 hover:bg-viewlix-red rounded-lg flex items-center justify-center transition-colors"
                title="Share">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
            </svg>
        </button>
    </div>
    
    {{-- Hover Overlay (Desktop only) --}}
    <div class="hidden md:block absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
        <div class="absolute bottom-0 left-0 right-0 p-3">
            <h3 class="text-sm font-medium text-white truncate">{{ $title }}</h3>
            <p class="text-xs text-gray-400">{{ $year }}</p>
        </div>
        
        {{-- Play Button --}}
        <div class="absolute inset-0 flex items-center justify-center">
            <a href="{{ $route }}" class="w-12 h-12 bg-viewlix-red/90 rounded-full flex items-center justify-center transform scale-0 group-hover:scale-100 transition-transform pointer-events-auto">
                <svg class="w-5 h-5 text-white ml-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M6.3 2.841A1.5 1.5 0 004 4.11v11.78a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                </svg>
            </a>
        </div>
    </div>
    
    {{-- Mobile: Title below image --}}
    <div class="md:hidden p-2">
        <h3 class="text-xs font-medium text-white truncate">{{ $title }}</h3>
        <p class="text-xs text-gray-500">{{ $year }}</p>
    </div>
</div>

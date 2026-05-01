<div class="fixed top-0 left-0 right-0 z-[9999] bg-gradient-to-r from-blue-600 to-blue-700 dark:from-blue-800 dark:to-blue-900 text-white px-4 py-2 flex items-center justify-between gap-4 flex-wrap shadow-lg" id="header-banner-{{ $banner->id }}" style="min-height: 64px; max-height: 100px;">
    <!-- Left Section: Icon + Text (Supports emoji and SVG) -->
    <div class="flex items-center gap-3 flex-1 min-w-0">
        @if($banner->icon)
            <div class="text-xl flex-shrink-0 w-6 h-6 md:w-7 md:h-7 flex items-center justify-center">
                {!! $banner->icon !!}
            </div>
        @endif
        <div class="min-w-0 flex-1">
            <p class="font-semibold text-sm md:text-base break-words">{{ $banner->title }}</p>
            @if($banner->description)
                <p class="text-xs md:text-sm opacity-90 break-words">{{ $banner->description }}</p>
            @endif
        </div>
    </div>

    <!-- Right Section: Hotlinks + Close Button -->
    <div class="flex items-center gap-2 flex-shrink-0">
        @if($banner->hotlinks && count($banner->hotlinks) > 0)
            @foreach($banner->hotlinks as $hotlink)
                <a 
                    href="{{ $hotlink['url'] ?? '#' }}"
                    target="{{ $hotlink['target'] ?? '_self' }}"
                    class="text-xs md:text-sm px-3 py-1 rounded bg-white/20 hover:bg-white/30 transition whitespace-nowrap"
                >
                    {{ $hotlink['text'] ?? 'Link' }}
                </a>
            @endforeach
        @endif
        
        <button 
            onclick="closeHeaderBanner({{ $banner->id }})"
            class="ml-2 text-xl hover:opacity-75 transition flex-shrink-0 cursor-pointer"
            aria-label="Close banner"
            title="Close this banner"
        >
            ✕
        </button>
    </div>
</div>

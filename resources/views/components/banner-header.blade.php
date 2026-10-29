<div class="fixed top-0 left-0 right-0 z-[10000] w-full bg-[#0c0d16]/90 backdrop-blur-xl border-b border-blue-500/30 text-white px-4 py-1.5 flex items-center justify-between gap-4 shadow-[0_4px_20px_rgba(0,0,0,0.5)] transition-all duration-300 min-h-[40px]" id="header-banner-{{ $banner->id }}">
    <!-- Left Section: Icon + Text (Supports emoji and SVG) -->
    <div class="flex items-center gap-3 flex-1 min-w-0">
        @if($banner->icon)
            <div class="text-xl flex-shrink-0 w-6 h-6 md:w-7 md:h-7 flex items-center justify-center">
                {!! $banner->icon !!}
            </div>
        @endif
        <div class="min-w-0 flex-1 flex items-center gap-2">
            <p class="font-semibold text-xs md:text-sm break-words">{{ $banner->title }}</p>
            @if($banner->description)
                <p class="text-[11px] md:text-xs opacity-80 break-words hidden sm:block">- {{ $banner->description }}</p>
            @endif
        </div>
    </div>

    <!-- Right Section: Hotlinks + Close Button -->
    <div class="flex items-center gap-2 flex-shrink-0">
        @php
            $hotlinks = $banner->hotlinks;
            if (is_string($hotlinks)) {
                $hotlinks = json_decode($hotlinks, true);
                if (is_string($hotlinks)) {
                    $hotlinks = json_decode($hotlinks, true);
                }
            }
        @endphp
        @if(!empty($hotlinks) && is_array($hotlinks))
            @foreach($hotlinks as $hotlink)
                <a 
                    href="{{ $hotlink['url'] ?? '#' }}"
                    target="{{ $hotlink['target'] ?? '_self' }}"
                    class="text-xs md:text-sm px-3 py-1 rounded-full bg-blue-600/20 hover:bg-blue-600/40 border border-blue-500/30 hover:border-blue-400 text-blue-200 hover:text-white transition-all shadow-sm whitespace-nowrap"
                >
                    {{ $hotlink['text'] ?? 'Link' }}
                </a>
            @endforeach
        @endif
        
        <button 
            onclick="closeHeaderBanner({{ $banner->id }})"
            class="ml-1 text-lg text-gray-400 hover:text-white hover:bg-white/10 w-7 h-7 flex items-center justify-center rounded-full transition-all flex-shrink-0 cursor-pointer"
            aria-label="Close banner"
            title="Close this banner"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
</div>

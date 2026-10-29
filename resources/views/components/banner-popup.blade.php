<div id="banner-popup-{{ $banner->id }}" class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-[9999] hidden transition-opacity duration-300" data-banner-id="{{ $banner->id }}">
    <div class="bg-[#121218] border border-white/10 rounded-3xl shadow-[0_0_50px_rgba(0,0,0,0.8)] max-w-md w-full mx-4 overflow-hidden transform transition-all duration-300 scale-95 opacity-0 relative" id="banner-popup-inner-{{ $banner->id }}">
        
        <!-- Glow effect behind modal header -->
        <div class="absolute top-0 left-0 right-0 h-32 bg-gradient-to-b from-blue-600/20 to-transparent pointer-events-none"></div>

        <!-- Header Banner (Supports emoji and SVG icons) -->
        <div class="px-6 pt-6 pb-4 flex flex-col items-center gap-3 relative z-10">
            @if($banner->icon)
                <div class="text-white text-3xl w-14 h-14 bg-white/5 border border-white/10 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-[0_0_15px_rgba(255,255,255,0.05)]">
                    {!! $banner->icon !!}
                </div>
            @endif
            <h2 class="text-white font-bold text-xl text-center">{{ $banner->title }}</h2>
        </div>

        <!-- Banner Content -->
        <div class="px-6 pb-2 relative z-10">
            @if($banner->image)
                <div class="mb-4 rounded-lg overflow-hidden">
                    <img src="{{ $banner->image }}" alt="{{ $banner->title }}" class="w-full h-auto object-cover">
                </div>
            @endif

            @if($banner->description)
                <p class="text-gray-400 text-sm leading-relaxed mb-6 text-center">
                    {{ $banner->description }}
                </p>
            @endif
        </div>

        <!-- Actions -->
        <div class="flex gap-3 px-6 pb-6 relative z-10">
            <button 
                onclick="closeBannerPopup({{ $banner->id }})"
                class="flex-1 px-4 py-2.5 text-gray-300 bg-white/5 border border-white/10 rounded-xl hover:bg-white/10 hover:text-white transition-all font-medium"
            >
                Close
            </button>
            @if($banner->button_url)
                <a 
                    href="{{ $banner->button_url }}"
                    onclick="closeBannerPopup({{ $banner->id }})"
                    class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-xl hover:from-blue-500 hover:to-blue-400 transition-all text-center font-medium shadow-[0_0_15px_rgba(37,99,235,0.4)]"
                >
                    {{ $banner->button_text }}
                </a>
            @else
                <button 
                    onclick="closeBannerPopup({{ $banner->id }})"
                    class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-500 text-white rounded-xl hover:from-blue-500 hover:to-blue-400 transition-all font-medium shadow-[0_0_15px_rgba(37,99,235,0.4)]"
                >
                    {{ $banner->button_text }}
                </button>
            @endif
        </div>
    </div>
</div>

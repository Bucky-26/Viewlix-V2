<div id="banner-popup-{{ $banner->id }}" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden" data-banner-id="{{ $banner->id }}">
    <div class="bg-white dark:bg-gray-900 rounded-lg shadow-lg max-w-md w-full mx-4 overflow-hidden">
        <!-- Header Banner (Supports emoji and SVG icons) -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 flex items-center gap-3">
            @if($banner->icon)
                <div class="text-white text-2xl w-8 h-8 flex items-center justify-center flex-shrink-0">
                    {!! $banner->icon !!}
                </div>
            @endif
            <h2 class="text-white font-bold text-lg flex-1">{{ $banner->title }}</h2>
        </div>

        <!-- Banner Content -->
        <div class="p-6">
            @if($banner->image)
                <div class="mb-4 rounded-lg overflow-hidden">
                    <img src="{{ $banner->image }}" alt="{{ $banner->title }}" class="w-full h-auto object-cover">
                </div>
            @endif

            @if($banner->description)
                <p class="text-gray-700 dark:text-gray-300 text-sm leading-relaxed mb-6">
                    {{ $banner->description }}
                </p>
            @endif
        </div>

        <!-- Actions -->
        <div class="flex gap-3 px-6 pb-6">
            <button 
                onclick="closeBannerPopup({{ $banner->id }})"
                class="flex-1 px-4 py-2 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition"
            >
                Close
            </button>
            @if($banner->button_url)
                <a 
                    href="{{ $banner->button_url }}"
                    onclick="closeBannerPopup({{ $banner->id }})"
                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-center font-medium"
                >
                    {{ $banner->button_text }}
                </a>
            @else
                <button 
                    onclick="closeBannerPopup({{ $banner->id }})"
                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
                >
                    {{ $banner->button_text }}
                </button>
            @endif
        </div>
    </div>
</div>

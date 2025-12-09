{{-- Footer --}}
<footer class="mt-auto py-6 px-4 lg:px-6 border-t border-viewlix-border">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <svg class="w-6 h-6 text-viewlix-red" viewBox="0 0 24 24" fill="currentColor">
                <path d="M4 4h4l4 12 4-12h4L12 22 4 4z"/>
            </svg>
            <span class="text-sm text-gray-400">
                &copy; {{ date('Y') }} <span class="text-white font-medium">Viewlix</span>. All rights reserved.
            </span>
        </div>
        
        <div class="flex items-center gap-4">
            <a href="#" class="text-sm text-gray-400 hover:text-white transition-colors">Privacy</a>
            <a href="#" class="text-sm text-gray-400 hover:text-white transition-colors">Terms</a>
            <a href="#" class="text-sm text-gray-400 hover:text-white transition-colors">Help</a>
        </div>
    </div>
</footer>

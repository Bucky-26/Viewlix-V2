    {{-- Footer --}}
    <footer class="mt-auto py-6 px-4 lg:px-6 border-t border-viewlix-border">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                {{-- Use the vielix.png image from the public folder. Make sure public/vielix.png exists. --}}
                <img src="{{ asset('viewlix.png') }}" alt="Viewlix" class="w-20 h-auto object-contain">
                <span class="text-sm text-gray-400">
                    &copy; {{ date('Y') }} <span class="text-white font-medium">Viewlix</span>. All rights reserved.
                </span>
            </div>
            
            <div class="flex items-center gap-4">
                <a href="/privacy" class="text-sm text-gray-400 hover:text-white transition-colors">Privacy</a>
                <a href="/terms" class="text-sm text-gray-400 hover:text-white transition-colors">Terms</a>
                <a href="/about" class="text-sm text-gray-400 hover:text-white transition-colors">About</a>
            </div>
        </div>
    </footer>

{{-- Modernized Streaming Platform Footer --}}
<footer class="mt-16 border-t border-white/5 bg-[#08080a]/90 backdrop-blur-xl py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8 lg:gap-12 pb-10 border-b border-white/5">
            
            {{-- Brand Column --}}
            <div class="md:col-span-5 space-y-4">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                    <img src="{{ asset('logo.png') }}" alt="Viewlix Icon" class="w-8 h-8 object-contain drop-shadow-[0_0_10px_rgba(0,74,173,0.4)]">
                    <img src="{{ asset('viewlix.png') }}" alt="Viewlix" class="h-6 w-auto object-contain">
                </a>
                
                <p class="text-xs text-gray-400 leading-relaxed max-w-sm">
                    Viewlix is your ultimate cinematic entertainment hub. Stream thousands of trending movies, television series, and simulcast anime in crystal-clear HD with zero subscriptions.
                </p>

                <div class="pt-1">
                    <p class="text-[11px] text-gray-500 leading-normal max-w-sm">
                        <strong class="text-gray-400">Disclaimer:</strong> Viewlix does not host any media files on its servers. All videos and streams are provided by non-affiliated third-party services.
                    </p>
                </div>
            </div>

            {{-- Explore Links --}}
            <div class="md:col-span-2 sm:col-span-4 space-y-3">
                <h4 class="text-xs font-bold text-white uppercase tracking-wider">Explore</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="{{ route('movies') }}" class="text-gray-400 hover:text-white transition-colors">Movies</a></li>
                    <li><a href="{{ route('tv-shows') }}" class="text-gray-400 hover:text-white transition-colors">TV Series</a></li>
                    <li><a href="{{ route('anime') }}" class="text-gray-400 hover:text-white transition-colors">Anime</a></li>
                    <li><a href="{{ route('filipino') }}" class="text-gray-400 hover:text-white transition-colors">Filipino Hub</a></li>
                    <li><a href="{{ route('movies', ['filter' => 'top_rated']) }}" class="text-gray-400 hover:text-white transition-colors">Top Rated</a></li>
                </ul>
            </div>

            {{-- Account Links --}}
            <div class="md:col-span-2 sm:col-span-4 space-y-3">
                <h4 class="text-xs font-bold text-white uppercase tracking-wider">My Space</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="{{ route('favorites') }}" class="text-gray-400 hover:text-white transition-colors">Favorites</a></li>
                    <li><a href="{{ route('watch-history') }}" class="text-gray-400 hover:text-white transition-colors">Watch History</a></li>
                    @auth
                    <li><a href="{{ route('profile.edit') }}" class="text-gray-400 hover:text-white transition-colors">Account Profile</a></li>
                    @else
                    <li><a href="{{ route('login') }}" class="text-gray-400 hover:text-white transition-colors">Sign In</a></li>
                    <li><a href="{{ route('register') }}" class="text-gray-400 hover:text-white transition-colors">Create Account</a></li>
                    @endauth
                </ul>
            </div>

            {{-- Legal & Info Links --}}
            <div class="md:col-span-3 sm:col-span-4 space-y-3">
                <h4 class="text-xs font-bold text-white uppercase tracking-wider">Platform</h4>
                <ul class="space-y-2 text-xs">
                    <li><a href="/privacy" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a></li>
                    <li><a href="/terms" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a></li>
                    <li><a href="{{ route('robots') }}" class="text-gray-400 hover:text-white transition-colors">Robots &amp; Sitemap</a></li>
                    <li><span class="text-gray-500 text-[11px]">Version: 2.0 Viewlix.site</span></li>
                </ul>
            </div>

        </div>

        {{-- Bottom Copyright Bar --}}
        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-gray-500">
            <p>&copy; {{ date('Y') }} <span class="text-gray-300 font-semibold">Viewlix.site</span>. All rights reserved.</p>
            <div class="flex items-center gap-4 text-[11px]">
                <span class="inline-flex items-center gap-1.5 text-emerald-400">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    Systems Operational
                </span>
                <span class="text-gray-600">•</span>
                <span>Viewlix IT Services</span>
            </div>
        </div>
    </div>
</footer>

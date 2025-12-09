{{-- Continue Watching Section --}}
{{-- Loads from Firebase for authenticated users, localStorage for guests --}}

<section class="py-8" id="continue-watching-section" style="display: none;">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-white">
            <svg class="w-6 h-6 inline-block mr-2 text-viewlix-red" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Continue Watching
        </h2>
        <button onclick="clearContinueWatching()" class="text-sm text-gray-400 hover:text-viewlix-red transition-colors">
            Clear All
        </button>
    </div>
    
    <div id="continue-watching-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
        {{-- Items will be injected via JavaScript --}}
    </div>
</section>

<script>
// Wait a bit for Firebase to initialize before loading
document.addEventListener('DOMContentLoaded', function() {
    // Delay to allow Firebase to authenticate
    setTimeout(loadContinueWatching, 500);
});

async function loadContinueWatching() {
    const section = document.getElementById('continue-watching-section');
    const grid = document.getElementById('continue-watching-grid');
    
    if (!section || !grid) return;
    
    try {
        let items = [];
        
        // Try to get from Firebase first (uses global function from app.js)
        if (typeof window.getContinueWatching === 'function') {
            items = await window.getContinueWatching(6);
        }
        
        // Fallback to localStorage if no items from Firebase
        if (items.length === 0) {
            const stored = localStorage.getItem('vidLinkProgress');
            if (stored) {
                const allProgress = JSON.parse(stored);
                items = Object.values(allProgress)
                    .filter(item => {
                        if (!item.progress) return false;
                        const percent = (item.progress.watched / item.progress.duration) * 100;
                        return item.progress.watched >= 30 && percent < 95;
                    })
                    .sort((a, b) => (b.last_updated || 0) - (a.last_updated || 0))
                    .slice(0, 6);
            }
        }
        
        if (items.length === 0) return;
        
        // Show section
        section.style.display = 'block';
        
        // Render items
        grid.innerHTML = items.map(item => renderContinueWatchingCard(item)).join('');
        
    } catch (e) {
        console.error('Error loading continue watching:', e);
    }
}

function renderContinueWatchingCard(item) {
    const posterUrl = item.poster_path 
        ? `https://image.tmdb.org/t/p/w342${item.poster_path}` 
        : '/images/no-poster.svg';
    
    const progress = item.progress ? Math.min(100, (item.progress.watched / item.progress.duration) * 100) : 0;
    
    // Determine the watch URL
    let watchUrl = '#';
    if (item.type === 'movie') {
        watchUrl = `/movie/${item.id}/watch`;
    } else if (item.type === 'tv') {
        const season = item.last_season_watched || '1';
        const episode = item.last_episode_watched || '1';
        watchUrl = `/tv-show/${item.id}/watch/${season}/${episode}`;
    } else if (item.type === 'anime') {
        const season = item.last_season_watched || '1';
        const episode = item.last_episode_watched || '1';
        watchUrl = `/anime/${item.id}/watch/${season}/${episode}`;
    }
    
    // Format time remaining
    const remaining = Math.floor(item.progress.duration - item.progress.watched);
    const remainingMin = Math.floor(remaining / 60);
    const remainingText = remainingMin > 0 ? `${remainingMin} min left` : 'Almost done';
    
    // Episode info for TV shows
    let episodeInfo = '';
    if (item.type !== 'movie' && item.last_season_watched && item.last_episode_watched) {
        episodeInfo = `<span class="text-gray-500">S${item.last_season_watched} E${item.last_episode_watched}</span>`;
    }
    
    return `
        <a href="${watchUrl}" class="group relative block bg-viewlix-gray rounded-xl overflow-hidden border border-viewlix-border hover:border-viewlix-red transition-all duration-300">
            {{-- Poster --}}
            <div class="relative aspect-[2/3] overflow-hidden">
                <img 
                    src="${posterUrl}" 
                    alt="${item.title}"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    loading="lazy"
                    onerror="this.src='/images/no-poster.svg'"
                >
                
                {{-- Overlay --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-transparent to-transparent"></div>
                
                {{-- Play Button --}}
                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                    <div class="w-14 h-14 rounded-full bg-viewlix-red/90 flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
                </div>
                
                {{-- Progress Bar --}}
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-700">
                    <div class="h-full bg-viewlix-red" style="width: ${progress}%"></div>
                </div>
                
                {{-- Remove Button --}}
                <button 
                    onclick="event.preventDefault(); event.stopPropagation(); removeContinueWatching(${item.id})"
                    class="absolute top-2 right-2 p-1.5 bg-black/60 hover:bg-viewlix-red rounded-full opacity-0 group-hover:opacity-100 transition-all"
                    title="Remove"
                >
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                {{-- Type Badge --}}
                <span class="absolute top-2 left-2 px-2 py-0.5 text-xs font-medium bg-black/60 text-white rounded-lg capitalize">
                    ${item.type}
                </span>
            </div>
            
            {{-- Info --}}
            <div class="p-3">
                <h3 class="font-medium text-white text-sm truncate mb-1">${item.title}</h3>
                <div class="flex items-center justify-between text-xs">
                    ${episodeInfo}
                    <span class="text-viewlix-red">${remainingText}</span>
                </div>
            </div>
        </a>
    `;
}

async function removeContinueWatching(mediaId) {
    try {
        // Use global Firebase function if available
        if (typeof window.removeWatchProgress === 'function') {
            await window.removeWatchProgress(mediaId);
        } else {
            // Fallback to localStorage only
            const stored = localStorage.getItem('vidLinkProgress');
            if (stored) {
                const allProgress = JSON.parse(stored);
                delete allProgress[String(mediaId)];
                localStorage.setItem('vidLinkProgress', JSON.stringify(allProgress));
            }
        }
        
        // Reload the section
        await loadContinueWatching();
        
    } catch (e) {
        console.error('Error removing item:', e);
    }
}

async function clearContinueWatching() {
    if (!confirm('Clear all continue watching history?')) return;
    
    try {
        // Use global Firebase function if available
        if (typeof window.clearAllWatchProgress === 'function') {
            await window.clearAllWatchProgress();
        } else {
            // Fallback to localStorage only
            localStorage.removeItem('vidLinkProgress');
        }
        
        // Hide section
        const section = document.getElementById('continue-watching-section');
        if (section) section.style.display = 'none';
        
    } catch (e) {
        console.error('Error clearing history:', e);
    }
}
</script>

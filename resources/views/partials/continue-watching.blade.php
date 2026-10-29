{{-- Continue Watching Section --}}
{{-- Loads from Firebase for authenticated users, localStorage for guests --}}

<section class="space-y-5" id="continue-watching-section" style="display: none;">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-2.5 h-6 bg-gradient-to-b from-blue-500 to-indigo-600 rounded-full shadow-md shadow-blue-500/30"></div>
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight flex items-center gap-2">
                    <span>Continue Watching</span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 font-semibold uppercase tracking-wider">Resume</span>
                </h2>
                <p class="text-xs text-gray-400 mt-0.5">Pick up right where you left off</p>
            </div>
        </div>
        <button onclick="clearContinueWatching()" class="text-xs font-semibold text-gray-400 hover:text-red-400 transition-colors px-3 py-1.5 rounded-xl hover:bg-white/5 border border-transparent hover:border-white/10">
            Clear All
        </button>
    </div>
    
    <div id="continue-watching-grid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4.5">
        {{-- Items injected via JavaScript --}}
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(loadContinueWatching, 500);
});

async function loadContinueWatching() {
    const section = document.getElementById('continue-watching-section');
    const grid = document.getElementById('continue-watching-grid');
    
    if (!section || !grid) return;
    
    try {
        let items = [];
        
        if (typeof window.getContinueWatching === 'function') {
            items = await window.getContinueWatching(6);
        }
        
        if (items.length === 0) {
            const stored = localStorage.getItem('vidLinkProgress');
            if (stored) {
                const allProgress = JSON.parse(stored);
                items = Object.values(allProgress)
                    .filter(item => {
                        if (!item.progress) return false;
                        const watched = Number(item.progress.watched) || 0;
                        const duration = Number(item.progress.duration) || 0;
                        if (duration <= 0) return false;
                        const percent = (watched / duration) * 100;
                        return percent >= 1 && percent < 95;
                    })
                    .sort((a, b) => (b.last_updated || 0) - (a.last_updated || 0))
                    .slice(0, 6);
            }
        }
        
        if (items.length === 0) return;
        
        section.style.display = 'block';
        grid.innerHTML = items.map(item => renderContinueWatchingCard(item)).join('');
        
    } catch (e) {
        console.error('Error loading continue watching:', e);
    }
}

function renderContinueWatchingCard(item) {
    const posterUrl = item.poster_path 
        ? `https://image.tmdb.org/t/p/w342${item.poster_path}` 
        : '/images/no-poster.svg';
    
    const progress = item.progress ? Math.min(100, Math.max(5, (item.progress.watched / item.progress.duration) * 100)) : 0;
    
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
    
    const remaining = Math.floor((item.progress ? item.progress.duration : 0) - (item.progress ? item.progress.watched : 0));
    const remainingMin = Math.floor(remaining / 60);
    const remainingText = remainingMin > 0 ? `${remainingMin}m left` : 'Almost done';
    
    let episodeInfo = '';
    if (item.type !== 'movie' && item.last_season_watched && item.last_episode_watched) {
        episodeInfo = `<span class="text-blue-400 font-semibold">S${item.last_season_watched} E${item.last_episode_watched}</span>`;
    }
    
    return `
        <a href="${watchUrl}" class="group relative block bg-[#111116] rounded-2xl overflow-hidden border border-white/5 hover:border-blue-500/50 hover:shadow-[0_12px_30px_rgba(0,0,0,0.8),0_0_20px_rgba(0,74,173,0.25)] transition-all duration-300 flex flex-col">
            <div class="relative aspect-[2/3] overflow-hidden bg-zinc-900">
                <img 
                    src="${posterUrl}" 
                    alt="${item.title}"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                    loading="lazy"
                    onerror="this.src='/images/no-poster.svg'"
                >
                
                <div class="absolute inset-0 bg-gradient-to-t from-[#0d0d12] via-transparent to-transparent opacity-80 group-hover:opacity-50 transition-opacity"></div>
                
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                    <div class="w-11 h-11 rounded-full bg-viewlix-red/90 text-white shadow-xl shadow-viewlix-red/50 flex items-center justify-center transform scale-0 group-hover:scale-100 transition-transform duration-200">
                        <svg class="w-5 h-5 ml-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6.3 2.841A1.5 1.5 0 004 4.11v11.78a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                        </svg>
                    </div>
                </div>
                
                <div class="absolute bottom-0 left-0 right-0 h-1.5 bg-black/60 backdrop-blur-xs">
                    <div class="h-full bg-gradient-to-r from-viewlix-red to-blue-500 rounded-r-full shadow-[0_0_8px_rgba(0,74,173,0.8)]" style="width: ${progress}%"></div>
                </div>
                
                <button 
                    onclick="event.preventDefault(); event.stopPropagation(); removeContinueWatching(${item.id})"
                    class="absolute top-2.5 right-2.5 p-1.5 bg-black/75 hover:bg-red-500/80 text-white rounded-lg opacity-0 group-hover:opacity-100 transition-all shadow-md"
                    title="Remove from history"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                
                <span class="absolute top-2.5 left-2.5 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider bg-black/75 backdrop-blur-md border border-white/10 text-white rounded-lg">
                    ${item.type}
                </span>
            </div>
            
            <div class="p-3 bg-[#101015]">
                <h3 class="font-semibold text-white text-xs sm:text-sm truncate group-hover:text-blue-400 transition-colors mb-1">${item.title}</h3>
                <div class="flex items-center justify-between text-[11px]">
                    ${episodeInfo}
                    <span class="text-gray-400">${remainingText}</span>
                </div>
            </div>
        </a>
    `;
}

async function removeContinueWatching(mediaId) {
    if (typeof window.removeWatchProgress === 'function') {
        await window.removeWatchProgress(mediaId);
    }
    
    try {
        const stored = localStorage.getItem('vidLinkProgress');
        if (stored) {
            const allProgress = JSON.parse(stored);
            delete allProgress[String(mediaId)];
            localStorage.setItem('vidLinkProgress', JSON.stringify(allProgress));
        }
    } catch (e) {
        console.error('Error removing local progress:', e);
    }
    
    loadContinueWatching();
}

async function clearContinueWatching() {
    if (!confirm('Are you sure you want to clear your continue watching list?')) return;
    
    if (typeof window.clearAllWatchProgress === 'function') {
        await window.clearAllWatchProgress();
    }
    
    localStorage.removeItem('vidLinkProgress');
    
    const section = document.getElementById('continue-watching-section');
    if (section) section.style.display = 'none';
}
</script>

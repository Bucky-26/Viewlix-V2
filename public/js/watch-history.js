// watch-history.js
// Provides global function watchHistoryPage() used by Alpine x-data

function watchHistoryPage() {
    return {
        items: [],
        activeType: 'all',
        loading: true,
        contentBaseUrl: 'https://content.viewlix.online',
        
        async init() {
            // Sign in to Firebase first (optional, provided by other scripts)
            if (window.signInToFirebase) {
                await window.signInToFirebase();
            }
            
            // Wait a bit for Firebase auth to complete
            await new Promise(resolve => setTimeout(resolve, 500));
            
            // Load watch history from Firebase or localStorage
            await this.loadHistory();
        },
        
        async loadHistory() {
            this.loading = true;
            
            try {
                if (window.getAllWatchHistory) {
                    this.items = await window.getAllWatchHistory();
                } else {
                    // Fallback to localStorage
                    this.items = this.getFromLocalStorage();
                }
            } catch (error) {
                console.error('Error loading history:', error);
                this.items = this.getFromLocalStorage();
            }
            
            this.loading = false;
        },
        
        getFromLocalStorage() {
            try {
                const stored = localStorage.getItem('vidLinkProgress');
                if (!stored) return [];
                
                const data = JSON.parse(stored);
                return Object.entries(data)
                    .map(([id, item]) => ({ id, ...item }))
                    .sort((a, b) => (b.last_updated || 0) - (a.last_updated || 0));
            } catch (e) {
                return [];
            }
        },
        
        get filteredItems() {
            if (this.activeType === 'all') {
                return this.items;
            }
            return this.items.filter(item => item.type === this.activeType);
        },
        
        getProgressPercent(item) {
            if (!item.progress) return 0;
            const watched = item.progress.watched || 0;
            const duration = item.progress.duration || 1;
            return Math.min(100, (watched / duration) * 100);
        },
        
        getPosterUrl(item) {
            if (item.poster_path) {
                return 'https://image.tmdb.org/t/p/w500' + item.poster_path;
            }
            return 'https://via.placeholder.com/500x750/1a1a2e/e94560?text=No+Image';
        },
        
        getEpisodeText(item) {
            if (item.season && item.episode) {
                return 'S' + item.season + ':E' + item.episode;
            }
            return Math.round(this.getProgressPercent(item)) + '% watched';
        },
        
        getDetailUrl(item) {
            const type = item.type || 'movie';
            const id = item.id || item.media_id;
            
            if (type === 'movie') return '/movie/' + id;
            if (type === 'tv') return '/tv-show/' + id;
            if (type === 'anime') return '/anime/' + id;
            return '/movie/' + id;
        },
        
        getWatchUrl(item) {
            const type = item.type || 'movie';
            const id = item.id || item.media_id;
            const progress = item.progress && Number(item.progress.watched);
            const query = new URLSearchParams({ autoplay: '1' });

            if (progress > 0) {
                query.set('startAt', String(Math.floor(progress)));
            }
            
            if (type === 'movie') {
                return '/movie/' + encodeURIComponent(id) + '/watch?' + query;
            }

            if (type === 'tv' || type === 'anime') {
                const season = encodeURIComponent(item.season || 1);
                const episode = encodeURIComponent(item.episode || 1);

                const path = type === 'tv' ? '/tv-show/' : '/anime/';
                return path + encodeURIComponent(id) + '/watch/' + season + '/' + episode + '?' + query;
            }

            return '/movie/' + encodeURIComponent(id) + '/watch?' + query;
        },
        
        formatTimeAgo(timestamp) {
            if (!timestamp) return '';
            
            const seconds = Math.floor((Date.now() - timestamp) / 1000);
            
            if (seconds < 60) return 'just now';
            if (seconds < 3600) return Math.floor(seconds / 60) + 'm ago';
            if (seconds < 86400) return Math.floor(seconds / 3600) + 'h ago';
            if (seconds < 604800) return Math.floor(seconds / 86400) + 'd ago';
            
            return new Date(timestamp).toLocaleDateString();
        },
        
        async removeItem(item) {
            if (!confirm('Remove from watch history?')) return;
            
            const mediaKey = this.getMediaKey(item);
            
            try {
                if (window.removeWatchProgress) {
                    await window.removeWatchProgress(mediaKey);
                }
                
                // Remove from local list
                this.items = this.items.filter(i => this.getMediaKey(i) !== mediaKey);
                
            } catch (error) {
                console.error('Error removing item:', error);
            }
        },
        
        async clearHistory() {
            const typeLabel = this.activeType === 'all' ? '' : ' for ' + this.activeType;
            if (!confirm('Clear all watch history' + typeLabel + '?')) return;
            
            try {
                if (this.activeType === 'all') {
                    if (window.clearAllWatchProgress) {
                        await window.clearAllWatchProgress();
                    }
                    this.items = [];
                } else {
                    // Remove only items of this type
                    const toRemove = this.items.filter(item => item.type === this.activeType);
                    
                    for (const item of toRemove) {
                        const mediaKey = this.getMediaKey(item);
                        if (window.removeWatchProgress) {
                            await window.removeWatchProgress(mediaKey);
                        }
                    }
                    
                    this.items = this.items.filter(item => item.type !== this.activeType);
                }
            } catch (error) {
                console.error('Error clearing history:', error);
            }
        },
        
        getMediaKey(item) {
            const type = item.type || 'movie';
            const id = item.id || item.media_id;
            
            if (type === 'movie') {
                return 'movie-' + id;
            } else {
                const season = item.season || 1;
                const episode = item.episode || 1;
                return type + '-' + id + '-s' + season + 'e' + episode;
            }
        }
    };
}

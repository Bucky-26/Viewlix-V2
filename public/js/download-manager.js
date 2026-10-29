/**
 * Download Manager for Viewlix
 * Handles downloading movies and TV shows with quality selection and PWA storage
 */

const DOWNLOAD_API_BASE = '/api/downloads';
const DOWNLOAD_REFERRER = 'https://dl.vidsrc.vip/';

/**
 * Open TV show download modal with season/episode selector
 * @param {number} mediaId - TMDB ID of the TV show
 * @param {string} mediaType - 'tv'
 * @param {string} mediaTitle - Title of the TV show
 * @param {number} totalSeasons - Total number of seasons
 */
async function openTVDownloadModal(mediaId, mediaType, mediaTitle, totalSeasons) {
    try {
        // Show loading state
        const modal = document.getElementById('download-modal');
        if (!modal) {
            await createDownloadModal();
        }
        
        const modalContent = document.getElementById('download-modal-content');
        
        // Create season/episode selector
        let html = `
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-white mb-4">Select Season & Episode</h3>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Season</label>
                    <select id="seasonSelect" onchange="updateEpisodeList()" class="w-full px-4 py-2 bg-viewlix-darker border border-viewlix-border text-white rounded-lg focus:outline-none focus:border-viewlix-red">
        `;
        
        for (let s = 1; s <= totalSeasons; s++) {
            html += `<option value="${s}">Season ${s}</option>`;
        }
        
        html += `
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Episode</label>
                    <select id="episodeSelect" class="w-full px-4 py-2 bg-viewlix-darker border border-viewlix-border text-white rounded-lg focus:outline-none focus:border-viewlix-red">
                        <option value="1">Episode 1</option>
                    </select>
                </div>
                
                <button 
                    onclick="fetchTVDownloads(${mediaId}, '${mediaType}', '${mediaTitle}')"
                    class="w-full px-4 py-2 bg-viewlix-red hover:bg-viewlix-red-dark text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    Find Downloads
                </button>
            </div>
        `;
        
        modalContent.innerHTML = html;
        openOverlay('#download-modal', true);
        
    } catch (error) {
        console.error('Error opening TV download modal:', error);
    }
}

/**
 * Update episode list based on selected season
 */
async function updateEpisodeList() {
    const seasonSelect = document.getElementById('seasonSelect');
    const episodeSelect = document.getElementById('episodeSelect');
    const season = seasonSelect.value;
    
    // For now, assume max 13 episodes per season (can be improved)
    let html = '';
    for (let e = 1; e <= 13; e++) {
        html += `<option value="${e}">Episode ${e}</option>`;
    }
    
    episodeSelect.innerHTML = html;
}

/**
 * Fetch TV show downloads for selected season/episode
 */
async function fetchTVDownloads(mediaId, mediaType, mediaTitle) {
    try {
        const seasonSelect = document.getElementById('seasonSelect');
        const episodeSelect = document.getElementById('episodeSelect');
        const season = seasonSelect.value;
        const episode = episodeSelect.value;
        
        // Show loading state
        const modalContent = document.getElementById('download-modal-content');
        modalContent.innerHTML = '<div class="text-center py-8"><p class="text-gray-400">Loading downloads...</p><div class="mt-4 animate-spin w-8 h-8 border-4 border-viewlix-red border-t-transparent rounded-full mx-auto"></div></div>';
        
        // Fetch downloads from API
        const url = `${DOWNLOAD_API_BASE}/tv/${mediaId}?season=${season}&episode=${episode}`;
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`Download API error: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (!data.ok) {
            throw new Error('Download service returned an error');
        }
        
        // Display downloads
        displayDownloadOptions(data.downloads, mediaId, mediaType, mediaTitle, season, episode);
        
    } catch (error) {
        console.error('Error fetching TV downloads:', error);
        const modalContent = document.getElementById('download-modal-content');
        if (modalContent) {
            modalContent.innerHTML = `
                <div class="text-center py-8">
                    <p class="text-red-400 mb-4">Failed to load downloads: ${error.message}</p>
                    <button onclick="closeOverlay('#download-modal', true)" class="px-4 py-2 bg-viewlix-red text-white rounded-lg hover:bg-viewlix-red-dark">Close</button>
                </div>
            `;
        }
    }
}

/**
 * Open download modal with quality selection
 * @param {number} mediaId - TMDB ID of the media
 * @param {string} mediaType - 'movie' or 'tv'
 * @param {string} mediaTitle - Title of the media
 * @param {number} season - Season number (for TV shows)
 * @param {number} episode - Episode number (for TV shows)
 */
async function openDownloadModal(mediaId, mediaType, mediaTitle, season = 1, episode = 1) {
    try {
        // Show loading state
        const modal = document.getElementById('download-modal');
        if (!modal) {
            await createDownloadModal();
        }
        
        const modalContent = document.getElementById('download-modal-content');
        modalContent.innerHTML = '<div class="text-center py-8"><p class="text-gray-400">Loading downloads...</p><div class="mt-4 animate-spin w-8 h-8 border-4 border-viewlix-red border-t-transparent rounded-full mx-auto"></div></div>';
        
        openOverlay('#download-modal', true);
        
        // Fetch downloads from API
        let url;
        if (mediaType === 'movie') {
            url = `${DOWNLOAD_API_BASE}/movie/${mediaId}`;
        } else {
            url = `${DOWNLOAD_API_BASE}/tv/${mediaId}?season=${season}&episode=${episode}`;
        }
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`Download API error: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (!data.ok) {
            throw new Error('Download service returned an error');
        }
        
        // Display downloads
        displayDownloadOptions(data.downloads, mediaId, mediaType, mediaTitle, season, episode);
        
    } catch (error) {
        console.error('Error opening download modal:', error);
        const modalContent = document.getElementById('download-modal-content');
        if (modalContent) {
            modalContent.innerHTML = `
                <div class="text-center py-8">
                    <p class="text-red-400 mb-4">Failed to load downloads: ${error.message}</p>
                    <button onclick="closeOverlay('#download-modal', true)" class="px-4 py-2 bg-viewlix-red text-white rounded-lg hover:bg-viewlix-red-dark">Close</button>
                </div>
            `;
        }
    }
}

/**
 * Display download options in the modal
 */
function displayDownloadOptions(downloads, mediaId, mediaType, mediaTitle, season, episode) {
    const modalContent = document.getElementById('download-modal-content');
    
    if (!downloads || downloads.length === 0) {
        modalContent.innerHTML = `
            <div class="text-center py-8">
                <p class="text-gray-400">No downloads available for this content.</p>
                <button onclick="closeOverlay('#download-modal', true)" class="mt-4 px-4 py-2 bg-viewlix-red text-white rounded-lg hover:bg-viewlix-red-dark">Close</button>
            </div>
        `;
        return;
    }
    
    // Sort downloads by quality (highest first)
    const sortedDownloads = [...downloads].sort((a, b) => {
        const getQualityValue = (q) => {
            const match = q.match(/(\d+)p/);
            return match ? parseInt(match[1]) : 0;
        };
        return getQualityValue(b.quality) - getQualityValue(a.quality);
    });
    
    let html = `
        <div class="space-y-3">
            <h3 class="text-lg font-bold text-white mb-4">Available Downloads</h3>
    `;
    
    sortedDownloads.forEach((download, index) => {
        const isHighest = index === 0;
        // Properly encode the URL and filename for the onclick handler
        const encodedUrl = encodeURIComponent(download.url);
        const encodedFilename = encodeURIComponent(download.filename);
        
        html += `
            <div class="bg-viewlix-darker rounded-lg p-4 border border-viewlix-border hover:border-viewlix-red transition-colors">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <p class="text-white font-semibold">${download.quality}</p>
                        <p class="text-gray-400 text-sm">${download.size}</p>
                    </div>
                    ${isHighest ? '<span class="px-2 py-1 bg-viewlix-red text-white text-xs font-bold rounded">BEST</span>' : ''}
                </div>
                <button 
                    data-download-url="${download.url}"
                    data-download-filename="${download.filename}"
                    data-media-type="${mediaType}"
                    data-media-title="${mediaTitle}"
                    class="download-button w-full mt-2 px-4 py-2 bg-viewlix-red hover:bg-viewlix-red-dark text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download
                </button>
            </div>
        `;
    });
    
    html += `
        </div>
    `;
    
    modalContent.innerHTML = html;
    
    // Add event listeners to download buttons
    document.querySelectorAll('.download-button').forEach(button => {
        button.addEventListener('click', function() {
            const url = this.getAttribute('data-download-url');
            const filename = this.getAttribute('data-download-filename');
            const mediaType = this.getAttribute('data-media-type');
            const mediaTitle = this.getAttribute('data-media-title');
            startDownload(url, filename, mediaType, mediaTitle);
        });
    });
}

/**
 * Start the download process
 */
async function startDownload(downloadUrl, filename, mediaType, mediaTitle) {
    try {
        console.log('Starting download:', { downloadUrl, filename, mediaType, mediaTitle });
        
        // Show loading notification
        showNotification('Download started', 'Your browser is downloading the file...');
        
        // Download with referrer header using fetch
        downloadWithReferrer(downloadUrl, filename);
        
        // Close modal after a short delay
        setTimeout(() => {
            closeOverlay('#download-modal', true);
        }, 500);
        
    } catch (error) {
        console.error('Download error:', error);
        showNotification('Download failed', error.message, 'error');
    }
}

/**
 * Download file with referrer header
 */
function downloadWithReferrer(downloadUrl, filename) {
    try {
        let finalUrl = downloadUrl;
        if (!downloadUrl.includes('localhost:4001') && !downloadUrl.includes('content.viewlix.online')) {
            finalUrl = "/download/proxy?url=" + encodeURIComponent(downloadUrl) + "&filename=" + encodeURIComponent(filename || "");
        }
        
        const link = document.createElement("a");
        link.href = finalUrl;
        link.download = filename || "download";
        link.target = "_blank";
        document.body.appendChild(link);
        link.click();
        setTimeout(() => document.body.removeChild(link), 100);
        
        console.log("Download triggered successfully");
    } catch (error) {
        console.error('Fetch download error:', error);
        // Try server-side proxy as a reliable fallback (server will set Referer)
        try {
            showNotification('Retrying via proxy', 'Attempting download via server proxy...');
            const proxyUrl = `/download/proxy?url=${encodeURIComponent(downloadUrl)}&filename=${encodeURIComponent(filename || '')}`;
            const proxyResp = await fetch(proxyUrl, { method: 'GET', credentials: 'same-origin' });

            if (proxyResp.ok) {
                // Instead of pulling the whole file into JS (which buffers in memory),
                // create a hidden iframe that navigates to the proxy URL. When the
                // proxy responds with a Content-Disposition: attachment header the
                // browser will stream and save the file using its native download
                // manager. This avoids extra copy and starts the download earlier.
                showNotification('Downloading via proxy', 'Starting server-proxied download...');
                const proxyUrlDirect = `/download/proxy?url=${encodeURIComponent(downloadUrl)}&filename=${encodeURIComponent(filename || '')}`;
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = proxyUrlDirect;
                // Append and let browser handle streaming to file without navigating
                document.body.appendChild(iframe);
                // Remove iframe after some time to clean up
                setTimeout(() => {
                    try { if (iframe && iframe.parentNode) iframe.parentNode.removeChild(iframe); } catch (e) {}
                }, 60 * 1000);
                return;
            } else {
                console.error('Proxy responded with status', proxyResp.status);
            }
        } catch (proxyError) {
            console.error('Proxy fetch error:', proxyError);
        }

        // Final fallback: show copy-link UI (no redirect)
        downloadFallback(downloadUrl, filename);
    }
}

/**
 * Fallback download method
 */
function downloadFallback(downloadUrl, filename) {
    console.log('Using fallback download method');
    // Do NOT navigate or redirect the user.
    // Instead show a small UI allowing the user to copy the direct link or close.
    showDownloadErrorUI(downloadUrl, `The download couldn't be fetched automatically. You can copy the direct link to download it manually.`);
}

/**
 * Show a small UI (non-blocking) that lets the user copy the direct link.
 * This purposely does NOT redirect or open any new tab.
 */
function showDownloadErrorUI(url, message) {
    // If an existing UI exists, update it
    let container = document.getElementById('download-error-ui');
    if (!container) {
        container = document.createElement('div');
        container.id = 'download-error-ui';
        container.style.position = 'fixed';
        container.style.bottom = '20px';
        container.style.left = '20px';
        container.style.zIndex = 99999;
        container.style.background = 'rgba(18,18,18,0.95)';
        container.style.color = '#fff';
        container.style.padding = '12px 14px';
        container.style.borderRadius = '10px';
        container.style.boxShadow = '0 6px 24px rgba(0,0,0,0.6)';
        container.style.maxWidth = '90%';
        container.style.fontSize = '14px';

        const text = document.createElement('div');
        text.id = 'download-error-msg';
        text.style.marginBottom = '8px';
        container.appendChild(text);

        const actions = document.createElement('div');
        actions.style.display = 'flex';
        actions.style.gap = '8px';

        const copyBtn = document.createElement('button');
        copyBtn.id = 'download-copy-link';
        copyBtn.textContent = 'Copy link';
        copyBtn.style.padding = '6px 10px';
        copyBtn.style.border = 'none';
        copyBtn.style.background = '#ef4444';
        copyBtn.style.color = 'white';
        copyBtn.style.borderRadius = '6px';
        copyBtn.style.cursor = 'pointer';

        const closeBtn = document.createElement('button');
        closeBtn.id = 'download-error-close';
        closeBtn.textContent = 'Close';
        closeBtn.style.padding = '6px 10px';
        closeBtn.style.border = '1px solid rgba(255,255,255,0.08)';
        closeBtn.style.background = 'transparent';
        closeBtn.style.color = '#fff';
        closeBtn.style.borderRadius = '6px';
        closeBtn.style.cursor = 'pointer';

        actions.appendChild(copyBtn);
        actions.appendChild(closeBtn);
        container.appendChild(actions);

        document.body.appendChild(container);

        copyBtn.addEventListener('click', () => {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(() => {
                    copyBtn.textContent = 'Copied';
                    setTimeout(() => copyBtn.textContent = 'Copy link', 2000);
                }).catch(() => {
                    window.prompt('Copy this link', url);
                });
            } else {
                window.prompt('Copy this link', url);
            }
        });

        closeBtn.addEventListener('click', () => {
            if (container && container.parentNode) container.parentNode.removeChild(container);
        });
    }

    // Update message in container
    const msg = document.getElementById('download-error-msg');
    if (msg) msg.textContent = message || url;
}

/**
 * PWA-based download with file storage
 */
async function downloadWithPWA(downloadUrl, filename, mediaType, mediaTitle) {
    console.log('PWA download initiated');
    // Uses the same fetch-based method with referrer
    await downloadWithReferrer(downloadUrl, filename);
}

/**
 * Show notification
 */
function showNotification(title, message, type = 'success') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed bottom-4 right-4 px-6 py-4 rounded-lg text-white font-semibold shadow-lg z-50 animate-slide-in ${
        type === 'error' ? 'bg-red-600' : 'bg-viewlix-red'
    }`;
    notification.innerHTML = `
        <div>${title}</div>
        <div class="text-sm opacity-90">${message}</div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transition = 'opacity 0.3s ease-out';
        setTimeout(() => document.body.removeChild(notification), 300);
    }, 3000);
}

/**
 * Create download modal HTML (called once on page load)
 */
async function createDownloadModal() {
    const modalHTML = `
        <div id="download-modal" class="fixed inset-0 z-40 hidden">
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
            
            <!-- Modal Container -->
            <div class="absolute inset-0 flex items-center justify-center p-4">
                <div class="bg-viewlix-dark rounded-2xl w-full max-w-md max-h-[80vh] overflow-y-auto border border-viewlix-border shadow-2xl">
                    <!-- Header -->
                    <div class="sticky top-0 bg-viewlix-darker border-b border-viewlix-border p-6 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-white">Download</h2>
                        <button 
                            onclick="closeOverlay('#download-modal', true)"
                            class="text-gray-400 hover:text-white transition-colors"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Content -->
                    <div id="download-modal-content" class="p-6">
                        <!-- Content loaded dynamically -->
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Check if modal exists, if not create it
    if (!document.getElementById('download-modal')) {
        const container = document.createElement('div');
        container.innerHTML = modalHTML;
        document.body.appendChild(container.firstElementChild);
    }
}

/**
 * Initialize download modal on page load
 */
document.addEventListener('DOMContentLoaded', async () => {
    await createDownloadModal();
    
    // Add animation styles if not already present
    if (!document.getElementById('download-modal-styles')) {
        const style = document.createElement('style');
        style.id = 'download-modal-styles';
        style.textContent = `
            @keyframes slide-in {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            .animate-slide-in {
                animation: slide-in 0.3s ease-out;
            }
        `;
        document.head.appendChild(style);
    }
});

/**
 * Simple triggerDownload API compatible with the sample you provided.
 * Keeps user on page and delegates to startDownload (which uses fetch+blob).
 */
function triggerDownload(btn, url) {
    // Toggle active classes
    document.querySelectorAll('.download-button,.subtitle-button').forEach(b => b.classList.remove('active'));
    if (btn && btn.classList) btn.classList.add('active');

    // Determine a sane filename from the url
    const filename = getFilenameFromUrl(url);

    // Delegate to startDownload (non-blocking)
    try {
        startDownload(url, filename, null, null);
    } catch (e) {
        console.error('triggerDownload error', e);
    }
}

/**
 * Subtitle toggler matching your sample
 */
function toggleSubtitles() {
    const el = document.getElementById('subtitle-links');
    const icon = document.getElementById('subtitle-icon');
    if (!el || !icon) return;
    el.classList.toggle('open');
    icon.classList.toggle('fa-chevron-down');
    icon.classList.toggle('fa-chevron-up');
}

// Expose API on window for inline handlers
window.triggerDownload = triggerDownload;
window.toggleSubtitles = toggleSubtitles;

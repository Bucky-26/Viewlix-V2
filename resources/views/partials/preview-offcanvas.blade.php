{{-- Preview Offcanvas - Shows quick preview of media --}}
<div id="preview-offcanvas" class="hidden translate-y-full fixed bottom-0 inset-x-0 transition-all duration-300 transform h-auto max-h-[90vh] w-full z-[90] bg-viewlix-darker border-t border-viewlix-border rounded-t-2xl" role="dialog" tabindex="-1" aria-labelledby="preview-offcanvas-label">
    <div class="flex justify-center pt-3 pb-2">
        <span class="w-12 h-1.5 bg-viewlix-gray rounded-full cursor-pointer" onclick="closeOverlay('#preview-offcanvas')"></span>
    </div>
    
    <div class="p-4 overflow-y-auto" id="preview-content">
        {{-- Content will be loaded dynamically via JavaScript --}}
        <div class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-viewlix-red"></div>
        </div>
    </div>
</div>

{{-- Share Offcanvas --}}
<div id="share-offcanvas" class="hidden translate-y-full fixed bottom-0 inset-x-0 transition-all duration-300 transform h-auto w-full z-[90] bg-viewlix-darker border-t border-viewlix-border rounded-t-2xl" role="dialog" tabindex="-1" aria-labelledby="share-offcanvas-label">
    <div class="flex justify-center pt-3 pb-2">
        <span class="w-12 h-1.5 bg-viewlix-gray rounded-full cursor-pointer" onclick="closeOverlay('#share-offcanvas')"></span>
    </div>
    
    <div class="p-4">
        <h3 id="share-offcanvas-label" class="text-lg font-bold text-white text-center mb-4">Share</h3>
        
        <div id="share-content" class="mb-4">
            {{-- Share info will be populated dynamically --}}
        </div>
        
        {{-- Share Options --}}
        <div class="grid grid-cols-4 gap-4 mb-6">
            {{-- Copy Link --}}
            <button type="button" onclick="copyShareLink()" class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-viewlix-gray transition-colors">
                <div class="w-12 h-12 rounded-full bg-gray-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                    </svg>
                </div>
                <span class="text-xs text-gray-400">Copy</span>
            </button>
            
            {{-- WhatsApp --}}
            <a href="#" id="share-whatsapp" target="_blank" class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-viewlix-gray transition-colors">
                <div class="w-12 h-12 rounded-full bg-green-500 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                </div>
                <span class="text-xs text-gray-400">WhatsApp</span>
            </a>
            
            {{-- Twitter/X --}}
            <a href="#" id="share-twitter" target="_blank" class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-viewlix-gray transition-colors">
                <div class="w-12 h-12 rounded-full bg-black flex items-center justify-center border border-viewlix-border">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </div>
                <span class="text-xs text-gray-400">X</span>
            </a>
            
            {{-- Facebook --}}
            <a href="#" id="share-facebook" target="_blank" class="flex flex-col items-center gap-2 p-3 rounded-xl hover:bg-viewlix-gray transition-colors">
                <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </div>
                <span class="text-xs text-gray-400">Facebook</span>
            </a>
        </div>
        
        {{-- URL Display --}}
        <div class="flex items-center gap-2 p-3 bg-viewlix-gray rounded-xl">
            <input type="text" id="share-url" readonly class="flex-1 bg-transparent text-sm text-gray-300 border-0 focus:ring-0 p-0" value="">
            <button type="button" onclick="copyShareLink()" class="text-viewlix-red hover:text-viewlix-red-light text-sm font-medium">
                Copy
            </button>
        </div>
        
        {{-- Copy Success Message --}}
        <div id="copy-success" class="hidden mt-3 p-2 bg-green-500/20 text-green-400 text-sm text-center rounded-lg">
            Link copied to clipboard!
        </div>
    </div>
</div>

@push('scripts')
<script>
// Share functionality
let currentShareData = {
    title: '',
    text: '',
    url: ''
};

function openShare(title, text, url) {
    currentShareData = { title, text, url };
    
    // Update share content
    document.getElementById('share-url').value = url;
    
    // Update share links
    const encodedUrl = encodeURIComponent(url);
    const encodedText = encodeURIComponent(text);
    const encodedTitle = encodeURIComponent(title);
    
    document.getElementById('share-whatsapp').href = `https://wa.me/?text=${encodedText}%20${encodedUrl}`;
    document.getElementById('share-twitter').href = `https://twitter.com/intent/tweet?text=${encodedText}&url=${encodedUrl}`;
    document.getElementById('share-facebook').href = `https://www.facebook.com/sharer/sharer.php?u=${encodedUrl}`;
    
    // Try native share API first on mobile
    if (navigator.share && /Android|iPhone|iPad|iPod/i.test(navigator.userAgent)) {
        navigator.share({
            title: title,
            text: text,
            url: url
        }).catch(() => {
            // If native share fails, show offcanvas
            openOverlay('#share-offcanvas');
        });
    } else {
        // Show share offcanvas on desktop
        openOverlay('#share-offcanvas');
    }
}

function copyShareLink() {
    const url = document.getElementById('share-url').value || window.location.href;
    
    navigator.clipboard.writeText(url).then(() => {
        const successEl = document.getElementById('copy-success');
        successEl.classList.remove('hidden');
        setTimeout(() => {
            successEl.classList.add('hidden');
        }, 2000);
    }).catch(() => {
        // Fallback for older browsers
        const input = document.getElementById('share-url');
        input.select();
        document.execCommand('copy');
    });
}

// Preview functionality
function openPreview(mediaId, mediaType) {
    const content = document.getElementById('preview-content');
    content.innerHTML = `
        <div class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-viewlix-red"></div>
        </div>
    `;
    
    openOverlay('#preview-offcanvas');
    
    // Fetch preview data
    fetch(`/api/preview/${mediaType}/${mediaId}`)
        .then(response => response.json())
        .then(data => {
            renderPreview(data, mediaType);
        })
        .catch(error => {
            content.innerHTML = `
                <div class="text-center py-8">
                    <p class="text-gray-400">Failed to load preview</p>
                </div>
            `;
        });
}

function renderPreview(data, mediaType) {
    const content = document.getElementById('preview-content');
    const title = data.title || data.name;
    const releaseDate = data.release_date || data.first_air_date;
    const year = releaseDate ? new Date(releaseDate).getFullYear() : '';
    const rating = data.vote_average ? data.vote_average.toFixed(1) : 'N/A';
    const posterUrl = data.poster_path ? `https://image.tmdb.org/t/p/w300${data.poster_path}` : '/images/placeholder.jpg';
    const backdropUrl = data.backdrop_path ? `https://image.tmdb.org/t/p/w780${data.backdrop_path}` : '';
    
    let detailUrl = '';
    let watchUrl = '';
    
    switch(mediaType) {
        case 'movie':
            detailUrl = `/movie/${data.id}`;
            watchUrl = `/movie/${data.id}/watch`;
            break;
        case 'tv':
            detailUrl = `/tv-show/${data.id}`;
            watchUrl = `/tv-show/${data.id}/watch`;
            break;
        case 'anime':
            detailUrl = `/anime/${data.id}`;
            watchUrl = `/anime/${data.id}/watch`;
            break;
    }
    
    content.innerHTML = `
        <div class="relative">
            ${backdropUrl ? `
            <div class="absolute inset-0 -mx-4 -mt-4 h-40 overflow-hidden rounded-t-2xl">
                <img src="${backdropUrl}" alt="" class="w-full h-full object-cover opacity-30">
                <div class="absolute inset-0 bg-gradient-to-t from-viewlix-darker to-transparent"></div>
            </div>
            ` : ''}
            
            <div class="relative flex gap-4 ${backdropUrl ? 'pt-20' : ''}">
                <img src="${posterUrl}" alt="${title}" class="w-24 h-36 object-cover rounded-xl shadow-lg flex-shrink-0">
                
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-bold text-white line-clamp-2">${title}</h3>
                    <div class="flex items-center gap-2 mt-1 text-sm text-gray-400">
                        ${year ? `<span>${year}</span>` : ''}
                        ${rating !== 'N/A' ? `
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            ${rating}
                        </span>
                        ` : ''}
                    </div>
                </div>
            </div>
            
            ${data.overview ? `
            <p class="mt-4 text-sm text-gray-400 line-clamp-3">${data.overview}</p>
            ` : ''}
            
            <div class="flex gap-3 mt-6">
                <a href="${watchUrl}" class="flex-1 flex items-center justify-center gap-2 py-3 bg-viewlix-red hover:bg-viewlix-red-dark text-white font-semibold rounded-xl transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/>
                    </svg>
                    Watch Now
                </a>
                <a href="${detailUrl}" class="flex items-center justify-center w-12 h-12 bg-viewlix-gray hover:bg-viewlix-gray-light rounded-xl transition-colors">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </a>
                <button type="button" onclick="openShare('${title.replace(/'/g, "\\'")}', 'Check out ${title.replace(/'/g, "\\'")} on Viewlix!', '${window.location.origin}${detailUrl}')" class="flex items-center justify-center w-12 h-12 bg-viewlix-gray hover:bg-viewlix-gray-light rounded-xl transition-colors">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                </button>
            </div>
        </div>
    `;
}
</script>
@endpush

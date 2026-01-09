// External homepage JS — fetch data from JSON APIs and render the hero carousel
(function(){
    const IMAGE_BASE = 'https://image.tmdb.org/t/p';
    const DEFAULT_IMG = '/splash.png';

    function slugify(text){
        return String(text).toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/(^-|-$)/g,'');
    }

    function getImage(pathOrUrl, size='w500'){
        if(!pathOrUrl) return DEFAULT_IMG;
        // If this already looks like a full URL (wsrv.nl or https), return as-is
        if(typeof pathOrUrl === 'string' && (pathOrUrl.startsWith('http://') || pathOrUrl.startsWith('https://'))){
            return pathOrUrl;
        }
        // Otherwise treat as TMDB file path
        return `${IMAGE_BASE}/${size}${pathOrUrl}`;
    }

    async function fetchJson(url){
        try{
            const r = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if(!r.ok) return null;
            return await r.json();
        }catch(e){
            console.error('fetch error', url, e);
            return null;
        }
    }

    function createSlide(item, type){
        const slide = document.createElement('div');
        slide.className = 'hero-carousel-slide absolute inset-0 h-full opacity-0';

        const inner = document.createElement('div');
        inner.className = 'relative flex items-center justify-center h-full rounded-lg overflow-hidden bg-gray-800';

    const img = document.createElement('img');
    img.alt = item.title || item.name || '';
    img.className = 'absolute inset-0 w-full h-full object-cover object-center';
    // Prefer proxied backdrop_url/backdrop_url, fall back to TMDB paths
    img.src = getImage(item.backdrop_url || item.backdrop_path || item.poster_url || item.poster_path, 'original');

        inner.appendChild(img);
        inner.insertAdjacentHTML('beforeend', '<div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/30 to-transparent"></div>');

        const content = document.createElement('div');
        content.className = 'relative z-10 max-w-4xl mx-auto px-4 w-full flex flex-col items-center justify-center text-center';

        const badge = document.createElement('span'); badge.className = 'inline-flex items-center carousel-trending-badge bg-viewlix-red text-sm px-3 py-1 rounded-full uppercase'; badge.textContent = 'Trending Now';
        content.appendChild(badge);

        const titleDiv = document.createElement('div'); titleDiv.className='carousel-logo mt-2 md:mt-4';
        // Prefer logo image when available (provided by API as proxied URL)
        if(item.logo_url){
            const logoImg = document.createElement('img');
            logoImg.src = item.logo_url;
            logoImg.alt = item.title || item.name || '';
            logoImg.className = 'mx-auto h-12 object-contain';
            titleDiv.appendChild(logoImg);
        } else {
            const titleText = document.createElement('h2'); titleText.className='carousel-title text-2xl md:text-4xl font-extrabold text-white'; titleText.textContent = item.title || item.name || 'Untitled';
            titleDiv.appendChild(titleText);
        }
        content.appendChild(titleDiv);

        const desc = document.createElement('p'); desc.className='carousel-description text-gray-200 max-w-3xl leading-relaxed mt-3';
        const overview = item.overview || item.description || '';
        desc.textContent = overview.length > 160 ? overview.slice(0,160) + '...' : overview;
        content.appendChild(desc);

        const btnWrap = document.createElement('div'); btnWrap.className='carousel-buttons mt-4 md:mt-8 flex items-center justify-center gap-4';
        let detailsHref = '#';
        if(type === 'movie') detailsHref = `/movie/${item.id}-${slugify(item.title || item.name || '')}`;
        else if(type === 'tv') detailsHref = `/tv-show/${item.id}-${slugify(item.title || item.name || '')}`;
        else if(type === 'anime') detailsHref = `/anime/${item.id}`;

        const details = document.createElement('a'); details.href = detailsHref; details.className='px-5 py-3 bg-white text-black rounded shadow'; details.textContent='Details';
        const watch = document.createElement('a'); watch.href = detailsHref; watch.className='px-5 py-3 bg-viewlix-red text-white rounded shadow'; watch.textContent='Watch';
        btnWrap.appendChild(details); btnWrap.appendChild(watch);
        content.appendChild(btnWrap);

        inner.appendChild(content);
        slide.appendChild(inner);
        return slide;
    }

    function createMediaCard(item, type){
        const title = item.title || item.name || 'Unknown';
        const date = item.release_date || item.first_air_date || '';
        const year = date ? String(date).slice(0,4) : '';
        const rating = item.vote_average || 0;
    const posterPath = item.poster_path || null;
        const mediaType = type === 'anime' ? 'anime' : (type === 'tv' ? 'tv' : 'movie');

        const root = document.createElement('div');
        root.className = 'group relative bg-viewlix-gray rounded-xl overflow-hidden border border-viewlix-border hover:border-viewlix-red transition-colors';

        const route = (function(){
            if(type === 'movie') return `/movie/${item.id}-${slugify(title)}`;
            if(type === 'tv') return `/tv-show/${item.id}-${slugify(title)}`;
            return `/anime/${item.id}`;
        })();

        const a = document.createElement('a'); a.href = route;

        const posterUrl = item.poster_url || posterPath;
        if(posterUrl){
            const img = document.createElement('img');
            img.src = getImage(posterUrl, 'w342');
            img.alt = title;
            img.className = 'aspect-[2/3] w-full object-cover';
            img.loading = 'lazy';
            a.appendChild(img);
        } else {
            const placeholder = document.createElement('div'); placeholder.className = 'aspect-[2/3] bg-viewlix-darker flex items-center justify-center';
            placeholder.innerHTML = '<svg class="w-12 h-12 text-gray-600" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>';
            a.appendChild(placeholder);
        }

        root.appendChild(a);

        // Rating badge
        if(rating > 0){
            const badge = document.createElement('div'); badge.className = 'absolute top-2 left-2 flex items-center gap-1 px-2 py-1 bg-black/70 rounded-lg text-xs';
            badge.innerHTML = '<svg class="w-3 h-3 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>' + `<span class="text-white font-medium">${Number(rating).toFixed(1)}</span>`;
            root.appendChild(badge);
        }

        // Quick actions
        const actions = document.createElement('div'); actions.className = 'absolute top-2 right-2 flex flex-col gap-1 md:opacity-0 md:group-hover:opacity-100 transition-opacity';
        const previewBtn = document.createElement('button'); previewBtn.type='button'; previewBtn.className='w-8 h-8 bg-black/70 hover:bg-viewlix-red rounded-lg flex items-center justify-center transition-colors'; previewBtn.title='Quick Preview';
        previewBtn.innerHTML = '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>';
        previewBtn.addEventListener('click', (e)=>{ e.preventDefault(); e.stopPropagation(); if(window.openPreview) window.openPreview(item.id, mediaType); });
        actions.appendChild(previewBtn);

        const shareBtn = document.createElement('button'); shareBtn.type='button'; shareBtn.className='w-8 h-8 bg-black/70 hover:bg-viewlix-red rounded-lg flex items-center justify-center transition-colors'; shareBtn.title='Share';
        shareBtn.innerHTML = '<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>';
        shareBtn.addEventListener('click', (e)=>{ e.preventDefault(); e.stopPropagation(); if(window.openShare) window.openShare(title.replace(/'/g, "\\'"), `Check out ${title} on Viewlix!`, window.location.origin + route); });
        actions.appendChild(shareBtn);
        root.appendChild(actions);

        // Hover overlay
        const hoverOverlay = document.createElement('div'); hoverOverlay.className='hidden md:block absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none';
        hoverOverlay.innerHTML = `<div class="absolute bottom-0 left-0 right-0 p-3"><h3 class="text-sm font-medium text-white truncate">${title}</h3><p class="text-xs text-gray-400">${year}</p></div><div class="absolute inset-0 flex items-center justify-center"><a href="${route}" class="w-12 h-12 bg-viewlix-red/90 rounded-full flex items-center justify-center transform scale-0 group-hover:scale-100 transition-transform pointer-events-auto"><svg class="w-5 h-5 text-white ml-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11v11.78a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/></svg></a></div>`;
        root.appendChild(hoverOverlay);

        // Mobile title
        const mobileTitle = document.createElement('div'); mobileTitle.className='md:hidden p-2';
        mobileTitle.innerHTML = `<h3 class="text-xs font-medium text-white truncate">${title}</h3><p class="text-xs text-gray-500">${year}</p>`;
        root.appendChild(mobileTitle);

        return root;
    }

    // Skeleton / loading card to show while data is fetched
    function createSkeletonCard(){
        const root = document.createElement('div');
        root.className = 'group relative bg-viewlix-gray rounded-xl overflow-hidden border border-viewlix-border';

        const placeholder = document.createElement('div');
        placeholder.className = 'aspect-[2/3] bg-gradient-to-br from-gray-800 to-gray-700 animate-pulse';
        root.appendChild(placeholder);

        const mobileTitle = document.createElement('div'); mobileTitle.className='md:hidden p-2';
        mobileTitle.innerHTML = `<div class="h-3 bg-gray-700 rounded w-3/4 mb-2 animate-pulse"></div><div class="h-2 bg-gray-700 rounded w-1/4 animate-pulse"></div>`;
        root.appendChild(mobileTitle);

        return root;
    }

    function showSkeletons(containerId, count = 6){
        const container = document.getElementById(containerId);
        if(!container) return;
        container.innerHTML = '';
        const grid = document.createElement('div'); grid.className='grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4';
        for(let i=0;i<count;i++){
            grid.appendChild(createSkeletonCard());
        }
        container.appendChild(grid);
    }

    function renderGrid(containerId, items, type){
        const container = document.getElementById(containerId);
        if(!container) return;
        container.innerHTML = '';
        const grid = document.createElement('div'); grid.className='grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4';
        items.slice(0,6).forEach(item => {
            const card = createMediaCard(item, type);
            grid.appendChild(card);
        });
        container.appendChild(grid);
    }

    function initHeroBehavior(){
        const carousel = document.getElementById('hero-carousel');
        if(!carousel) return;
        const slides = Array.from(carousel.querySelectorAll('.hero-carousel-slide'));
        if(!slides.length) return;
        let current = 0;

        function show(idx){
            slides.forEach((s,i)=>{
                if(i===idx){ s.classList.add('active'); s.setAttribute('aria-hidden','false'); }
                else { s.classList.remove('active'); s.setAttribute('aria-hidden','true'); }
            });
        }
        show(0);
        let timer = setInterval(()=>{ current = (current+1)%slides.length; show(current); }, 5000);

        const prev = document.getElementById('hero-prev');
        const next = document.getElementById('hero-next');
        if(prev) prev.addEventListener('click', (e)=>{ e.preventDefault(); current = (current-1+slides.length)%slides.length; show(current); clearInterval(timer); timer = setInterval(()=>{ current = (current+1)%slides.length; show(current); },5000); });
        if(next) next.addEventListener('click', (e)=>{ e.preventDefault(); current = (current+1)%slides.length; show(current); clearInterval(timer); timer = setInterval(()=>{ current = (current+1)%slides.length; show(current); },5000); });
    }

    // Adjust the hero carousel height so it fits the remaining viewport
    // after accounting for the page header and mobile bottom nav.
    function adjustHeroHeight(){
        const hero = document.getElementById('hero-carousel');
        if(!hero) return;

        // Try to find header and bottom nav elements by common selectors.
        const header = document.querySelector('header') || document.querySelector('.site-header') || document.querySelector('.topbar');
        const bottomNav = document.querySelector('.mobile-nav') || document.querySelector('#mobile-bottom-nav') || document.querySelector('.bottom-nav') || document.querySelector('footer');

        // Fallback sizes (px) if elements not found
        const defaultHeaderHeight = window.innerWidth < 768 ? 56 : 80;
        const defaultBottomNavHeight = window.innerWidth < 768 ? 56 : 0;

        const headerHeight = header ? Math.ceil(header.getBoundingClientRect().height) : defaultHeaderHeight;
        // Only treat bottomNav as present on narrow screens
        let bottomNavHeight = 0;
        if(window.innerWidth < 768){
            bottomNavHeight = bottomNav ? Math.ceil(bottomNav.getBoundingClientRect().height) : defaultBottomNavHeight;
        }

        // Small padding to avoid touching edges
        const extraPadding = 24;

        let available = window.innerHeight - headerHeight - bottomNavHeight - extraPadding;
        // Cap the hero at 85vh to avoid it becoming too large on tall viewports
        const cap = Math.floor(window.innerHeight * 0.85);
        if(available > cap) available = cap;
        if(available < 240) available = 240; // minimum reasonable hero height

        hero.style.height = available + 'px';
    }

    // Recalculate hero height on resize or orientation change
    window.addEventListener('resize', () => {
        adjustHeroHeight();
    });
    window.addEventListener('orientationchange', () => {
        setTimeout(adjustHeroHeight, 250);
    });

    async function init(){
        // Show skeletons while data is fetched so the page doesn't look empty
    showSkeletons('trending-movies-grid', 6);
    showSkeletons('top-rated-movies-grid', 6);
    showSkeletons('trending-tv-grid', 6);
    showSkeletons('popular-anime-grid', 6);

        const heroPlaceholder = document.getElementById('hero-carousel-body');
        if(heroPlaceholder){
            heroPlaceholder.innerHTML = '<div class="hero-carousel-slide absolute inset-0 h-full"><div class="relative h-full rounded-lg overflow-hidden bg-gray-800"><div class="absolute inset-0 bg-gradient-to-br from-gray-800 to-gray-700 animate-pulse"></div></div></div>';
        }

        // Choose image sizes based on viewport width (smaller on mobile)
        const isMobile = window.innerWidth < 768;
        const posterSize = isMobile ? 'w342' : 'w500';
        const backdropSize = isMobile ? 'w780' : 'w1280';

        // Fetch minimal payloads for grids. Only request logos for the movie list used in the hero (include_logos=true)
        const [moviesRes, tvRes, animeRes, topRatedRes] = await Promise.all([
            fetchJson(`/api/movies?filter=popular&page=1&minimal=1&include_logos=1&poster_size=${posterSize}&backdrop_size=${backdropSize}`),
            fetchJson(`/api/tv-shows?filter=popular&page=1&minimal=1&poster_size=${posterSize}&backdrop_size=${backdropSize}`),
            fetchJson(`/api/anime?filter=popular&page=1&minimal=1&poster_size=${posterSize}&backdrop_size=${backdropSize}`),
            fetchJson(`/api/movies?filter=top_rated&page=1&minimal=1&poster_size=${posterSize}&backdrop_size=${backdropSize}`),
        ]);

        const movies = moviesRes?.results || [];
        const tvs = tvRes?.results || [];
        const anime = animeRes?.results || [];
        const topRated = topRatedRes?.results || [];

        // Build hero slides from movies + tv (replace placeholder)
        const slidesContainer = document.getElementById('hero-carousel-body');
        if(slidesContainer){
            slidesContainer.innerHTML = '';
            const slides = [];
            movies.slice(0,6).forEach(m=>slides.push({item:m,type:'movie'}));
            tvs.slice(0,6).forEach(t=>slides.push({item:t,type:'tv'}));
            slides.slice(0,6).forEach(s=>{
                const slideEl = createSlide(s.item, s.type);
                slidesContainer.appendChild(slideEl);
            });
        }

        // Render grids (replace skeletons)
        renderGrid('trending-movies-grid', movies, 'movie');
        renderGrid('top-rated-movies-grid', topRated, 'movie');
        renderGrid('trending-tv-grid', tvs, 'tv');
        renderGrid('popular-anime-grid', anime, 'anime');

        initHeroBehavior();
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();

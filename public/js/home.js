// External homepage JS — Modernized Cinematic Hero Carousel & Media Shelves
(function () {
    const IMAGE_BASE = 'https://image.tmdb.org/t/p';
    const DEFAULT_IMG = '/splash.png';

    function slugify(text) {
        return String(text).toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)/g, '');
    }

    function getImage(pathOrUrl, size = 'w500') {
        if (!pathOrUrl) return DEFAULT_IMG;
        if (typeof pathOrUrl === 'string' && (pathOrUrl.startsWith('http://') || pathOrUrl.startsWith('https://'))) {
            return pathOrUrl;
        }
        return `${IMAGE_BASE}/${size}${pathOrUrl}`;
    }

    async function fetchJson(url) {
        try {
            const r = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!r.ok) return null;
            return await r.json();
        } catch (e) {
            console.error('fetch error', url, e);
            return null;
        }
    }

    // Modernized Left-Aligned Cinematic Hero Slide
    function createSlide(item, type, backdropSize = 'w1280') {
        const slide = document.createElement('div');
        slide.className = 'hero-carousel-slide absolute inset-0 h-full w-full';

        const inner = document.createElement('div');
        inner.className = 'relative w-full h-full bg-black overflow-hidden select-none';

        // Backdrop Image
        const img = document.createElement('img');
        const title = item.title || item.name || 'Untitled';
        img.alt = title;
        img.className = 'absolute inset-0 w-full h-full object-cover object-center scale-100 transition-transform duration-1000 ease-out';
        const backdropSource = item.backdrop_url || item.backdrop_path || item.poster_url || item.poster_path;
        img.src = getImage(backdropSource, backdropSize || 'w1280');
        img.decoding = 'async';

        const match = (backdropSize || '').match(/^w(\d+)$/);
        if (match) {
            const w = parseInt(match[1], 10);
            const h = Math.round(w * 9 / 21);
            img.setAttribute('width', String(w));
            img.setAttribute('height', String(h));
        }
        inner.appendChild(img);

        // Sweeping Multi-directional Vignettes for high contrast and readability
        const scrim = document.createElement('div');
        scrim.className = 'absolute inset-0 bg-gradient-to-r from-[#08080a] via-[#08080a]/80 via-40% to-transparent z-10';
        inner.appendChild(scrim);

        const bottomScrim = document.createElement('div');
        bottomScrim.className = 'absolute inset-0 bg-gradient-to-t from-[#08080a] via-[#08080a]/40 to-transparent z-10';
        inner.appendChild(bottomScrim);

        const topScrim = document.createElement('div');
        topScrim.className = 'absolute inset-0 bg-gradient-to-b from-black/40 via-transparent to-transparent z-10';
        inner.appendChild(topScrim);

        // Content Container (Left-Aligned Hero Content)
        const content = document.createElement('div');
        content.className = 'relative z-20 h-full flex flex-col justify-end max-w-3xl px-6 sm:px-12 lg:px-16 pb-12 sm:pb-16';

        // Badge


        // Title or Logo
        const titleDiv = document.createElement('div');
        titleDiv.className = 'mb-3';
        if (item.logo_url) {
            const logoImg = document.createElement('img');
            logoImg.src = item.logo_url;
            logoImg.alt = title;
            logoImg.className = 'max-h-16 sm:max-h-24 w-auto max-w-full object-contain drop-shadow-[0_4px_16px_rgba(0,0,0,0.9)]';
            titleDiv.appendChild(logoImg);
        } else {
            const titleText = document.createElement('h2');
            titleText.className = 'text-3xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-none drop-shadow-md';
            titleText.textContent = title;
            titleDiv.appendChild(titleText);
        }
        content.appendChild(titleDiv);

        // Meta Info Bar (Rating, Year, Quality)
        const meta = document.createElement('div');
        meta.className = 'flex items-center flex-wrap gap-2.5 sm:gap-3 text-xs sm:text-sm font-medium text-gray-300 mb-3.5';

        const rating = item.vote_average ? Number(item.vote_average).toFixed(1) : null;
        if (rating && rating > 0) {
            meta.innerHTML += `<div class="flex items-center gap-1 px-2 py-0.5 rounded-lg bg-amber-500/20 border border-amber-500/30 text-amber-400 font-bold"><svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg><span>${rating}</span></div>`;
        }

        const date = item.release_date || item.first_air_date || '';
        const year = date ? String(date).slice(0, 4) : null;
        if (year) {
            meta.innerHTML += `<span class="px-2 py-0.5 rounded-lg bg-white/5 border border-white/10 text-gray-300 font-semibold">${year}</span>`;
        }

        meta.innerHTML += `<span class="px-2 py-0.5 rounded-lg bg-white/5 border border-white/10 text-blue-400 font-bold uppercase tracking-wider text-[11px]">4K Ultra HD</span>`;
        meta.innerHTML += `<span class="px-2 py-0.5 rounded-lg bg-white/5 border border-white/10 text-gray-400 font-medium uppercase tracking-wider text-[11px]">Free Stream</span>`;
        content.appendChild(meta);

        // Overview / Synopsis
        const desc = document.createElement('p');
        desc.className = 'text-gray-300 text-xs sm:text-sm leading-relaxed max-w-2xl line-clamp-2 sm:line-clamp-3 mb-6 drop-shadow';
        desc.textContent = item.overview || item.description || 'Watch now in ultra-high-definition with crystal clear sound on Viewlix.';
        content.appendChild(desc);

        // CTA Action Buttons
        let detailsHref = '#';
        if (type === 'movie') detailsHref = `/movie/${item.id}-${slugify(title)}`;
        else if (type === 'tv') detailsHref = `/tv-show/${item.id}-${slugify(title)}`;
        else if (type === 'anime') detailsHref = `/anime/${item.id}`;

        const btnWrap = document.createElement('div');
        btnWrap.className = 'flex items-center gap-3 sm:gap-4 flex-wrap';

        // Watch Now Button
        const watch = document.createElement('a');
        watch.href = detailsHref;
        watch.className = 'h-11 px-6 sm:px-7 rounded-xl bg-gradient-to-r from-viewlix-red via-blue-600 to-indigo-600 hover:from-blue-600 hover:to-viewlix-red text-white font-bold text-xs sm:text-sm shadow-xl shadow-viewlix-red/30 hover:shadow-viewlix-red/50 hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2.5 shrink-0 whitespace-nowrap';
        watch.innerHTML = `<svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11v11.78a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/></svg><span>Watch Now</span>`;
        btnWrap.appendChild(watch);

        // Details Button
        const details = document.createElement('a');
        details.href = detailsHref;
        details.className = 'h-11 px-5 sm:px-6 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/15 text-white font-semibold text-xs sm:text-sm hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2 shrink-0 whitespace-nowrap';
        details.innerHTML = `<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><span>Details</span>`;
        btnWrap.appendChild(details);

        // Quick Preview Button
        const preview = document.createElement('button');
        preview.type = 'button';
        preview.className = 'w-11 h-11 shrink-0 rounded-xl bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/15 text-white flex items-center justify-center hover:scale-105 transition-transform';
        preview.title = 'Quick Preview';
        preview.innerHTML = `<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`;
        preview.addEventListener('click', (e) => {
            e.preventDefault();
            if (window.openPreview) window.openPreview(item.id, type);
        });
        btnWrap.appendChild(preview);

        content.appendChild(btnWrap);
        inner.appendChild(content);
        slide.appendChild(inner);
        return slide;
    }

    // Modern Media Card Generator
    function createMediaCard(item, type, posterSize = 'w342') {
        const title = item.title || item.name || 'Unknown';
        const date = item.release_date || item.first_air_date || '';
        const year = date ? String(date).slice(0, 4) : '';
        const rating = item.vote_average || 0;
        const posterPath = item.poster_path || null;
        const mediaType = type === 'anime' ? 'anime' : (type === 'tv' ? 'tv' : 'movie');

        const root = document.createElement('div');
        root.className = 'group relative bg-[#111116] rounded-2xl overflow-hidden border border-white/5 hover:border-blue-500/50 hover:shadow-[0_12px_30px_rgba(0,0,0,0.8),0_0_20px_rgba(0,74,173,0.25)] transition-all duration-300 flex flex-col';

        const route = (function () {
            if (type === 'movie') return `/movie/${item.id}-${slugify(title)}`;
            if (type === 'tv') return `/tv-show/${item.id}-${slugify(title)}`;
            return `/anime/${item.id}`;
        })();

        // Clickable Poster Area
        const a = document.createElement('a');
        a.href = route;
        a.className = 'block relative overflow-hidden aspect-[2/3] bg-zinc-900';

        const posterUrl = item.poster_url || posterPath;
        if (posterUrl) {
            const img = document.createElement('img');
            img.src = getImage(posterUrl, posterSize || 'w342');
            img.alt = title;
            img.className = 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out';
            img.loading = 'lazy';
            img.decoding = 'async';
            const pMatch = (posterSize || '').match(/^w(\d+)$/);
            if (pMatch) {
                const w = parseInt(pMatch[1], 10);
                const h = Math.round(w * 3 / 2);
                img.setAttribute('width', String(w));
                img.setAttribute('height', String(h));
            }
            a.appendChild(img);
        } else {
            const placeholder = document.createElement('div');
            placeholder.className = 'w-full h-full flex items-center justify-center bg-zinc-900 text-gray-600';
            placeholder.innerHTML = '<svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>';
            a.appendChild(placeholder);
        }

        // Bottom Gradient Scrim
        const scrim = document.createElement('div');
        scrim.className = 'absolute inset-0 bg-gradient-to-t from-[#0d0d12] via-transparent to-transparent opacity-80 group-hover:opacity-60 transition-opacity';
        a.appendChild(scrim);

        // Rating Badge (Top Left)
        if (rating > 0) {
            const badge = document.createElement('div');
            badge.className = 'absolute top-2.5 left-2.5 flex items-center gap-1 px-2 py-0.5 bg-black/75 backdrop-blur-md border border-white/10 rounded-lg text-[11px] shadow-md';
            badge.innerHTML = `<svg class="w-3 h-3 text-amber-400 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg><span class="text-white font-bold">${Number(rating).toFixed(1)}</span>`;
            a.appendChild(badge);
        }

        // Quick Actions (Top Right, on hover)
        const actions = document.createElement('div');
        actions.className = 'absolute top-2.5 right-2.5 flex flex-col gap-1.5 opacity-0 group-hover:opacity-100 transition-all duration-200 translate-x-1 group-hover:translate-x-0';

        const previewBtn = document.createElement('button');
        previewBtn.type = 'button';
        previewBtn.className = 'w-7 h-7 rounded-lg bg-black/80 hover:bg-viewlix-red text-white flex items-center justify-center shadow-lg transition-colors';
        previewBtn.title = 'Quick Preview';
        previewBtn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>';
        previewBtn.addEventListener('click', (e) => { e.preventDefault(); e.stopPropagation(); if (window.openPreview) window.openPreview(item.id, mediaType); });
        actions.appendChild(previewBtn);

        const shareBtn = document.createElement('button');
        shareBtn.type = 'button';
        shareBtn.className = 'w-7 h-7 rounded-lg bg-black/80 hover:bg-viewlix-red text-white flex items-center justify-center shadow-lg transition-colors';
        shareBtn.title = 'Share';
        shareBtn.innerHTML = '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>';
        shareBtn.addEventListener('click', (e) => { e.preventDefault(); e.stopPropagation(); if (window.openShare) window.openShare(title.replace(/'/g, "\\'"), `Check out ${title} on Viewlix!`, window.location.origin + route); });
        actions.appendChild(shareBtn);

        a.appendChild(actions);

        // Center Play Button (On Hover)
        const playCenter = document.createElement('div');
        playCenter.className = 'absolute inset-0 flex items-center justify-center pointer-events-none';
        playCenter.innerHTML = '<div class="w-11 h-11 rounded-full bg-viewlix-red/90 text-white shadow-xl shadow-viewlix-red/50 flex items-center justify-center transform scale-0 group-hover:scale-100 transition-transform duration-200"><svg class="w-5 h-5 ml-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11v11.78a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/></svg></div>';
        a.appendChild(playCenter);

        root.appendChild(a);

        // Bottom Info Bar
        const info = document.createElement('div');
        info.className = 'p-3 bg-[#101015] flex flex-col justify-between flex-1';
        info.innerHTML = `
            <h3 class="text-xs sm:text-sm font-semibold text-white truncate group-hover:text-blue-400 transition-colors" title="${title}">
                ${title}
            </h3>
            <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1">
                <span>${year || 'N/A'}</span>
                <span class="uppercase font-bold tracking-wider text-[10px] px-1.5 py-0.5 rounded bg-white/5 border border-white/5 text-gray-300">
                    ${mediaType}
                </span>
            </div>
        `;
        root.appendChild(info);

        return root;
    }

    // Modernized Skeleton Loading Card
    function createSkeletonCard() {
        const root = document.createElement('div');
        root.className = 'bg-[#111116] rounded-2xl overflow-hidden border border-white/5 animate-pulse';

        const placeholder = document.createElement('div');
        placeholder.className = 'aspect-[2/3] bg-zinc-900';
        root.appendChild(placeholder);

        const info = document.createElement('div');
        info.className = 'p-3 space-y-2';
        info.innerHTML = `
            <div class="h-3 bg-white/10 rounded w-3/4"></div>
            <div class="h-2.5 bg-white/5 rounded w-1/3"></div>
        `;
        root.appendChild(info);

        return root;
    }

    function showSkeletons(containerId, count = 6) {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.innerHTML = '';
        const grid = document.createElement('div');
        grid.className = 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4.5';
        for (let i = 0; i < count; i++) {
            grid.appendChild(createSkeletonCard());
        }
        container.appendChild(grid);
    }

    function renderGrid(containerId, items, type, posterSize = 'w342') {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.innerHTML = '';
        const grid = document.createElement('div');
        grid.className = 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4.5';
        items.slice(0, 6).forEach(item => {
            const card = createMediaCard(item, type, posterSize);
            grid.appendChild(card);
        });
        container.appendChild(grid);
    }

    function initHeroBehavior() {
        const carousel = document.getElementById('hero-carousel');
        if (!carousel) return;
        const slides = Array.from(carousel.querySelectorAll('.hero-carousel-slide'));
        if (!slides.length) return;
        let current = 0;

        const dotsContainer = document.getElementById('hero-dots');
        if (dotsContainer) {
            dotsContainer.innerHTML = '';
            slides.forEach((_, i) => {
                const dot = document.createElement('button');
                dot.type = 'button';
                dot.className = `h-2 rounded-full transition-all duration-300 ${i === 0 ? 'w-8 bg-blue-500' : 'w-2 bg-white/30 hover:bg-white/60'}`;
                dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
                dot.addEventListener('click', (e) => {
                    e.preventDefault();
                    show(i);
                    resetTimer();
                });
                dotsContainer.appendChild(dot);
            });
        }

        function updateDots(idx) {
            if (!dotsContainer) return;
            const dots = Array.from(dotsContainer.querySelectorAll('button'));
            dots.forEach((d, i) => {
                if (i === idx) {
                    d.className = 'h-2 w-8 rounded-full bg-blue-500 transition-all duration-300';
                } else {
                    d.className = 'h-2 w-2 rounded-full bg-white/30 hover:bg-white/60 transition-all duration-300';
                }
            });
        }

        function show(idx) {
            current = idx;
            slides.forEach((s, i) => {
                if (i === idx) {
                    s.classList.add('active');
                    s.setAttribute('aria-hidden', 'false');
                } else {
                    s.classList.remove('active');
                    s.setAttribute('aria-hidden', 'true');
                }
            });
            updateDots(idx);
        }

        show(0);
        let timer = setInterval(() => {
            current = (current + 1) % slides.length;
            show(current);
        }, 6000);

        function resetTimer() {
            clearInterval(timer);
            timer = setInterval(() => {
                current = (current + 1) % slides.length;
                show(current);
            }, 6000);
        }

        const prev = document.getElementById('hero-prev');
        const next = document.getElementById('hero-next');
        if (prev) {
            prev.addEventListener('click', (e) => {
                e.preventDefault();
                current = (current - 1 + slides.length) % slides.length;
                show(current);
                resetTimer();
            });
        }
        if (next) {
            next.addEventListener('click', (e) => {
                e.preventDefault();
                current = (current + 1) % slides.length;
                show(current);
                resetTimer();
            });
        }
    }

    // Interactive Streaming Providers Panel Manager
    function initStreamingProviders(posterSize = 'w342', backdropSize = 'w780') {
        const section = document.getElementById('streaming-providers-section');
        if (!section) return;

        let currentProvider = 'netflix';
        let currentType = 'movie';
        let currentProviderName = 'Netflix';
        let currentSearchQuery = 'Netflix';

        const providerCache = {}; // Cache key: `${provider}_${type}` => array of items

        const tabs = Array.from(section.querySelectorAll('.provider-tab-btn'));
        const typeMovieBtn = document.getElementById('provider-type-movie');
        const typeTvBtn = document.getElementById('provider-type-tv');
        const headingEl = document.getElementById('provider-content-heading');
        const exploreLink = document.getElementById('provider-explore-link');
        const gridContainer = document.getElementById('provider-media-grid');

        function updateUI() {
            // 1. Update Tabs visual states
            tabs.forEach(tab => {
                const isMatch = tab.dataset.provider === currentProvider;
                const indicator = tab.querySelector('.provider-indicator');
                const activeClasses = (tab.dataset.activeColor || 'border-white/40 bg-white/10 text-white').split(' ');

                if (isMatch) {
                    tab.classList.add('active');
                    activeClasses.forEach(c => tab.classList.add(c));
                    tab.classList.remove('border-white/10', 'bg-[#0d0e17]/85', 'text-gray-400');
                    if (indicator) indicator.classList.remove('hidden');
                } else {
                    tab.classList.remove('active');
                    activeClasses.forEach(c => tab.classList.remove(c));
                    tab.classList.add('border-white/10', 'bg-[#0d0e17]/85', 'text-gray-400');
                    if (indicator) indicator.classList.add('hidden');
                }
            });

            // 2. Update Type Toggle buttons
            if (currentType === 'movie') {
                typeMovieBtn?.classList.add('active', 'text-white', 'bg-gradient-to-r', 'from-viewlix-red', 'to-blue-600', 'shadow-md');
                typeMovieBtn?.classList.remove('text-gray-400');
                typeTvBtn?.classList.remove('active', 'text-white', 'bg-gradient-to-r', 'from-viewlix-red', 'to-blue-600', 'shadow-md');
                typeTvBtn?.classList.add('text-gray-400');
            } else {
                typeTvBtn?.classList.add('active', 'text-white', 'bg-gradient-to-r', 'from-viewlix-red', 'to-blue-600', 'shadow-md');
                typeTvBtn?.classList.remove('text-gray-400');
                typeMovieBtn?.classList.remove('active', 'text-white', 'bg-gradient-to-r', 'from-viewlix-red', 'to-blue-600', 'shadow-md');
                typeMovieBtn?.classList.add('text-gray-400');
            }

            // 3. Update Heading & Explore Link
            const typeLabel = currentType === 'movie' ? 'Movies' : 'TV Shows';
            if (headingEl) {
                headingEl.innerHTML = `Top ${typeLabel} on <span class="text-white">${currentProviderName}</span>`;
            }
            if (exploreLink) {
                if (currentProvider === 'vivamax') {
                    exploreLink.href = '/filipino';
                } else if (currentProvider === 'crunchyroll') {
                    exploreLink.href = '/anime';
                } else {
                    exploreLink.href = `/search?q=${encodeURIComponent(currentSearchQuery)}&filter=${currentType}`;
                }
            }
        }

        async function loadContent() {
            const cacheKey = `${currentProvider}_${currentType}`;

            if (providerCache[cacheKey]) {
                renderGrid('provider-media-grid', providerCache[cacheKey], currentType, posterSize);
                return;
            }

            // Show skeletons while fetching
            showSkeletons('provider-media-grid', 6);

            const data = await fetchJson(`/api/streaming-providers?provider=${encodeURIComponent(currentProvider)}&type=${encodeURIComponent(currentType)}&poster_size=${posterSize}&backdrop_size=${backdropSize}`);
            const results = data?.results || [];

            providerCache[cacheKey] = results;

            // Ensure user hasn't switched away while request was in flight
            if (`${currentProvider}_${currentType}` === cacheKey) {
                if (results.length > 0) {
                    renderGrid('provider-media-grid', results, currentType, posterSize);
                } else {
                    if (gridContainer) {
                        gridContainer.innerHTML = `<div class="p-8 rounded-2xl bg-[#0e0f18]/60 border border-white/5 text-center text-gray-400 text-sm">No ${currentType === 'movie' ? 'movies' : 'TV shows'} currently available for ${currentProviderName}.</div>`;
                    }
                }
            }
        }

        // Attach click listeners to provider tabs
        tabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                const provider = tab.dataset.provider;
                if (!provider || provider === currentProvider) return;

                currentProvider = provider;
                currentProviderName = tab.dataset.providerName || provider;
                currentSearchQuery = tab.dataset.searchQuery || currentProviderName;

                updateUI();
                loadContent();

                // Scroll tab smoothly into view if partially hidden
                tab.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
            });
        });

        // Attach click listeners to Type toggle buttons
        typeMovieBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentType === 'movie') return;
            currentType = 'movie';
            updateUI();
            loadContent();
        });

        typeTvBtn?.addEventListener('click', (e) => {
            e.preventDefault();
            if (currentType === 'tv') return;
            currentType = 'tv';
            updateUI();
            loadContent();
        });

        // Initial load
        updateUI();
        loadContent();
    }

    async function init() {
        showSkeletons('trending-movies-grid', 6);
        showSkeletons('top-rated-movies-grid', 6);
        showSkeletons('trending-tv-grid', 6);
        showSkeletons('popular-anime-grid', 6);

        const isMobile = window.innerWidth < 768;
        const posterSize = isMobile ? 'w342' : 'w500';
        const backdropSize = isMobile ? 'w780' : 'w1280';

        // Initialize Streaming Providers Panel
        initStreamingProviders(posterSize, backdropSize);

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

        const slidesContainer = document.getElementById('hero-carousel-body');
        if (slidesContainer && (movies.length || tvs.length)) {
            slidesContainer.innerHTML = '';
            const slides = [];
            movies.slice(0, 4).forEach(m => slides.push({ item: m, type: 'movie' }));
            tvs.slice(0, 3).forEach(t => slides.push({ item: t, type: 'tv' }));

            slides.forEach(s => {
                const slideEl = createSlide(s.item, s.type, backdropSize);
                slidesContainer.appendChild(slideEl);
            });

            initHeroBehavior();
        }

        renderGrid('trending-movies-grid', movies, 'movie', posterSize);
        renderGrid('top-rated-movies-grid', topRated, 'movie', posterSize);
        renderGrid('trending-tv-grid', tvs, 'tv', posterSize);
        renderGrid('popular-anime-grid', anime, 'anime', posterSize);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();

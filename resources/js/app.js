import './bootstrap';

import Alpine from 'alpinejs';

// Preline UI - Import all components
import { HSDropdown, HSOverlay, HSCarousel } from 'preline/preline';
import './overlays';
import './chat-support';

// Firebase imports - Realtime Database + Messaging
import { initializeApp } from 'firebase/app';
import { getAuth, signInWithCustomToken, onAuthStateChanged, signOut } from 'firebase/auth';
import { getDatabase, ref, set, get, remove, query, orderByChild, limitToLast, onValue, update, push as firebasePush } from 'firebase/database';
import { getMessaging, getToken, onMessage } from 'firebase/messaging';
import { getStorage, ref as storageRef, uploadBytes, getDownloadURL } from 'firebase/storage';

window.Alpine = Alpine;
Alpine.start();

// Initialize Preline components after DOM is ready (also handle case
// where script is loaded after DOMContentLoaded by invoking immediately
// when document is already ready).
function initPreline() {
    try {
        // Auto-initialize all Preline components
        HSDropdown.autoInit();
        HSOverlay.autoInit();
        // Initialize carousel so elements with data-hs-carousel are activated
        try { HSCarousel && HSCarousel.autoInit && HSCarousel.autoInit(); } catch(e) { console.warn('HSCarousel init failed', e); }
    } catch (e) {
        console.warn('Preline init failed', e);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPreline);
} else {
    // DOM already ready
    initPreline();
}

// Robust fallback for desktop notifications offcanvas
// Ensures the bell always toggles the right-side offcanvas even if Preline
// behaves unexpectedly (backdrop shown but panel stays hidden). This init
// runs immediately if the document is already ready.
function initDesktopNotifFallback() {
    try {
        const toggle = document.getElementById('desktop-notif-button');
        const offcanvas = document.getElementById('notifications-desktop-offcanvas');
        const backdrop = document.getElementById('overlay-backdrop');

        if (!toggle || !offcanvas) return;

    // Use capture phase so this handler runs before Preline's handlers
    // (prevents double-toggle races where Preline opens/closes the panel)
    toggle.addEventListener('click', (e) => {
            // Prevent other click handlers (Preline's built-in toggle) from
            // running after this handler which can cause the overlay to
            // immediately close (double-toggle). Stop immediate propagation
            // and prevent default right away so we control the open/close flow.
            try {
                e.preventDefault();
                e.stopImmediatePropagation();
                e.stopPropagation();
            } catch (ep) {
                // ignore if event methods are not available for some reason
            }

            // Diagnostic logging to help debug why offcanvas isn't visible
            try {
                console.debug('[notif] desktop bell clicked', {
                    HSOverlay_type: typeof HSOverlay,
                    has_hs_collection: !!window.$hsOverlayCollection,
                    offcanvas_id: offcanvas && offcanvas.id,
                    offcanvas_classes: offcanvas && offcanvas.className,
                    backdrop_classes: backdrop && backdrop.className,
                });
            } catch (dbgErr) {
                console.debug('[notif] debug error', dbgErr);
            }

            // Prefer Preline if it's fully functional. If HSOverlay is present
            // and an instance exists for the element, try using it to open the overlay.
            try {
                if (window.$hsOverlayCollection) {
                    const inst = window.$hsOverlayCollection.find(c => c.element.el === offcanvas);
                    if (inst) {
                        console.debug('[notif] Found Preline instance for offcanvas, attempting to open via instance');
                        try {
                            if (typeof inst.element.open === 'function') {
                                inst.element.open();
                            } else if (typeof HSOverlay.getInstance === 'function') {
                                const got = HSOverlay.getInstance(offcanvas, true);
                                if (got && typeof got.open === 'function') got.open();
                            }
                        } catch (openErr) {
                            console.warn('[notif] Preline instance open() call failed', openErr);
                        }
                        // Defer to Preline after attempting open. We already
                        // stopped propagation so Preline's own click handler
                        // won't run and cause a double-toggle.
                        return;
                    }
                }
            } catch (err) {
                // fallthrough to manual toggle
                console.debug('[notif] Preline check failed', err);
            }


            // Remove any Preline-created backdrop for this overlay (id is `${id}-backdrop`)
            const prelineBackdrop = document.getElementById(offcanvas.id + '-backdrop');
            if (prelineBackdrop) prelineBackdrop.remove();

            const isHidden = offcanvas.classList.contains('hidden');

            if (isHidden) {
                // show our central backdrop if available
                if (backdrop) {
                    backdrop.classList.remove('hidden');
                    backdrop.classList.remove('pointer-events-none');
                    backdrop.classList.add('pointer-events-auto');
                }

                offcanvas.classList.remove('hidden');
                // ensure it's positioned above the backdrop
                offcanvas.style.zIndex = '9999';
                requestAnimationFrame(() => {
                    offcanvas.classList.remove('translate-x-full');
                    offcanvas.classList.add('translate-x-0');
                });

                // Prevent global/document click handlers (which some libraries
                // use to auto-close overlays) from seeing the *same* click that
                // triggered opening. Add a short-lived capturing listener that
                // swallows click events immediately after opening.
                const _swallowClick = (evt) => {
                    try {
                        evt.stopImmediatePropagation();
                        evt.stopPropagation();
                    } catch (_) {}
                    // remove immediately so we don't block further interactions
                    document.removeEventListener('click', _swallowClick, true);
                };
                document.addEventListener('click', _swallowClick, true);
                // safety: remove after 250ms in case something went wrong
                setTimeout(() => document.removeEventListener('click', _swallowClick, true), 250);

                // Resilience: sometimes other code will still close the panel
                // immediately (race). If that happens, reopen after a short
                // delay so the user's click results in an open panel.
                setTimeout(() => {
                    try {
                        if (offcanvas.classList.contains('hidden')) {
                            if (backdrop) {
                                backdrop.classList.remove('hidden');
                                backdrop.classList.remove('pointer-events-none');
                                backdrop.classList.add('pointer-events-auto');
                            }
                            offcanvas.classList.remove('hidden');
                            offcanvas.style.zIndex = '9999';
                            requestAnimationFrame(() => {
                                offcanvas.classList.remove('translate-x-full');
                                offcanvas.classList.add('translate-x-0');
                            });
                        }
                    } catch (reopenErr) {
                        console.debug('[notif] reopen attempt failed', reopenErr);
                    }
                }, 120);
            } else {
                offcanvas.classList.remove('translate-x-0');
                offcanvas.classList.add('translate-x-full');
                setTimeout(() => {
                    offcanvas.classList.add('hidden');
                    // reset inline z-index
                    offcanvas.style.zIndex = '';
                }, 300);

                if (backdrop) {
                    backdrop.classList.add('hidden');
                    backdrop.classList.remove('pointer-events-auto');
                    backdrop.classList.add('pointer-events-none');
                }
            }
    }, true);

        // Close when clicking the global backdrop
        if (backdrop) {
            backdrop.addEventListener('click', () => {
                if (!offcanvas.classList.contains('hidden')) {
                    offcanvas.classList.remove('translate-x-0');
                    offcanvas.classList.add('translate-x-full');
                    setTimeout(() => offcanvas.classList.add('hidden'), 300);
                    backdrop.classList.add('hidden');
                    backdrop.classList.remove('pointer-events-auto');
                    backdrop.classList.add('pointer-events-none');
                }
            });
        }
    } catch (e) {
        console.warn('Desktop notif fallback init failed', e);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDesktopNotifFallback);
} else {
    initDesktopNotifFallback();
}

// Also reinitialize on Livewire/Turbo navigation if used
document.addEventListener('livewire:navigated', () => {
    HSDropdown.autoInit();
    HSOverlay.autoInit();
    try { HSCarousel && HSCarousel.autoInit && HSCarousel.autoInit(); } catch(e) { console.warn('HSCarousel reinit failed', e); }
});

// Search autocomplete / prediction
function initSearchAutocomplete() {
    try {
        const input = document.getElementById('global-search-input');
        const container = document.getElementById('search-suggestions');
        if (!input || !container) return;

        const list = container.querySelector('ul');
        let suggestions = [];
        let selectedIndex = -1;
        let controller = null;

        function hide() {
            container.classList.add('hidden');
            selectedIndex = -1;
            suggestions = [];
            list.innerHTML = '';
        }

        function show() {
            container.classList.remove('hidden');
        }

        function render(items) {
            suggestions = items || [];
            selectedIndex = -1;
            if (!suggestions.length) {
                hide();
                return;
            }
            list.innerHTML = suggestions.map((s, i) => {
                const img = s.image ? `<img src="${s.image}" alt="" class="w-10 h-14 rounded-sm object-cover flex-shrink-0 me-3">` : `<div class="w-10 h-14 rounded-sm bg-viewlix-gray flex-shrink-0 me-3"></div>`;
                const type = s.type ? `<span class="text-xs text-gray-400 ms-1">${s.type}</span>` : '';
                return `
                    <li data-index="${i}" role="option" class="px-3 py-2 cursor-pointer hover:bg-viewlix-gray flex items-center gap-3">
                        ${img}
                        <div class="min-w-0">
                            <div class="text-sm font-medium text-white truncate">${s.title}</div>
                            <div class="text-xs text-gray-400 truncate">${type}</div>
                        </div>
                    </li>
                `;
            }).join('');

            // attach handlers
            Array.from(list.querySelectorAll('li')).forEach(li => {
                li.addEventListener('mousedown', (ev) => {
                    // prevent input blur before click
                    ev.preventDefault();
                });
                li.addEventListener('click', (ev) => {
                    const idx = parseInt(li.getAttribute('data-index'));
                    const item = suggestions[idx];
                    if (item && item.link) {
                        window.location.href = item.link;
                    } else if (item) {
                        // fallback: submit form with query
                        input.value = item.title;
                        input.form && input.form.submit();
                    }
                });
            });

            show();
        }

        function setActive(idx) {
            const items = Array.from(list.querySelectorAll('li'));
            items.forEach((el, i) => {
                el.classList.toggle('bg-viewlix-gray', i === idx);
            });
            selectedIndex = idx;
        }

        function debounce(fn, wait = 250) {
            let t;
            return function(...args) {
                clearTimeout(t);
                t = setTimeout(() => fn.apply(this, args), wait);
            };
        }

        const fetchSuggestions = debounce(async (q) => {
            if (controller) controller.abort();
            controller = new AbortController();

            try {
                const resp = await fetch(`/search/suggest?q=${encodeURIComponent(q)}`, { signal: controller.signal });
                if (!resp.ok) return hide();
                const data = await resp.json();
                render(data.suggestions || []);
            } catch (err) {
                if (err.name === 'AbortError') return;
                console.debug('Search suggest error', err);
                hide();
            }
        }, 180);

        input.addEventListener('input', (e) => {
            const q = input.value.trim();
            if (q.length < 2) {
                hide();
                return;
            }
            fetchSuggestions(q);
        });

        input.addEventListener('keydown', (e) => {
            const items = Array.from(list.querySelectorAll('li'));
            if (!items.length) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                const next = (selectedIndex + 1) % items.length;
                setActive(next);
                items[next].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                const next = (selectedIndex - 1 + items.length) % items.length;
                setActive(next);
                items[next].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'Enter') {
                if (selectedIndex >= 0 && suggestions[selectedIndex]) {
                    e.preventDefault();
                    const item = suggestions[selectedIndex];
                    if (item.link) window.location.href = item.link;
                    else { input.value = item.title; input.form && input.form.submit(); }
                }
            } else if (e.key === 'Escape') {
                hide();
            }
        });

        // Hide when clicking outside
        document.addEventListener('click', (ev) => {
            if (!container.contains(ev.target) && ev.target !== input) {
                hide();
            }
        });

    } catch (e) {
        console.warn('Search autocomplete init failed', e);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSearchAutocomplete);
} else {
    initSearchAutocomplete();
}

// Reinit on Livewire/Turbo navigation
document.addEventListener('livewire:navigated', initSearchAutocomplete);

// ============================================
// Firebase Configuration & Initialization
// ============================================

let firebaseApp = null;
let firebaseAuth = null;
let firebaseDb = null;
let firebaseMessaging = null;
let currentFirebaseUser = null;
let notificationUnsubscribe = null;

/**
 * Initialize Firebase with config from window.firebaseConfig (set in Blade)
 */
window.initFirebase = function(config) {
    if (firebaseApp) return; // Already initialized
    
    try {
        firebaseApp = initializeApp(config);
        firebaseAuth = getAuth(firebaseApp);
        firebaseDb = getDatabase(firebaseApp);

        // Expose globally for submodules like chat-support and admin support console
        window.firebaseApp = firebaseApp;
        window.firebaseAuth = firebaseAuth;
        window.firebaseDb = firebaseDb;
        window.FirebaseDbOps = { ref, set, get, remove, query, orderByChild, limitToLast, onValue, update, push: firebasePush };
        window.dispatchEvent(new CustomEvent('firebase:ready', { detail: { app: firebaseApp, db: firebaseDb, ops: window.FirebaseDbOps } }));
        
        // Initialize Firebase Cloud Messaging
        try {
            firebaseMessaging = getMessaging(firebaseApp);
            console.log('🔥 Firebase Messaging initialized');
        } catch (e) {
            console.warn('Firebase Messaging not available:', e.message);
        }
        
        // Listen for auth state changes
        onAuthStateChanged(firebaseAuth, (user) => {
            currentFirebaseUser = user;
            if (user) {
                console.log('🔥 Firebase authenticated:', user.uid);
                // Start listening for notifications when authenticated
                startNotificationListener();
                // Request FCM token
                requestFcmPermission();
            } else {
                console.log('🔥 Firebase signed out');
                // Stop listening for notifications when signed out
                stopNotificationListener();
            }
        });
        
        // If user is authenticated in Laravel session, auto sign-in to Firebase
        if (window.isUserLoggedIn && !currentFirebaseUser) {
            setTimeout(() => {
                if (typeof window.signInToFirebase === 'function') {
                    window.signInToFirebase().catch(e => console.debug('auto signInToFirebase error', e));
                }
            }, 100);
        }

        console.log('🔥 Firebase initialized (Realtime Database)');
    } catch (error) {
        console.error('Firebase init error:', error);
    }
};

/**
 * Ensure the frontend has the Firebase config available. Prefer embedded
 * `window.firebaseConfig` (set in Blade); otherwise fetch from the JSON
 * endpoint `/api/app-config`. This allows a decoupled frontend to initialize
 * SDKs without server-side embedding.
 */
async function ensureFirebaseConfigFromApi() {
    try {
        // If Blade already injected config, prefer it (keeps backward compat)
        if (window.firebaseConfig) {
            try { window.initFirebase(window.firebaseConfig); } catch (e) { console.debug('initFirebase failed with embedded config', e); }
            if (window.firebaseVapidKey) {
                // already set
            }
            return;
        }

        const resp = await fetch('/api/app-config', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' });
        if (!resp.ok) return;
        const data = await resp.json();
        const fb = data?.firebase;
        if (!fb) return;

        window.firebaseVapidKey = fb.vapidKey || null;
        if (fb.config) {
            // Map keys to the SDK expected names (camelCase is already used)
            window.firebaseConfig = {
                apiKey: fb.config.apiKey,
                authDomain: fb.config.authDomain,
                databaseURL: fb.config.databaseURL,
                projectId: fb.config.projectId,
                storageBucket: fb.config.storageBucket,
                messagingSenderId: fb.config.messagingSenderId,
                appId: fb.config.appId,
                measurementId: fb.config.measurementId,
            };

            try { window.initFirebase(window.firebaseConfig); } catch (e) { console.debug('initFirebase failed after API fetch', e); }
        }
    } catch (e) {
        console.debug('Failed to fetch app config', e);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ensureFirebaseConfigFromApi);
} else {
    ensureFirebaseConfigFromApi();
}

let signInPromise = null;

/**
 * Sign in to Firebase using custom token from Laravel backend
 */
window.signInToFirebase = async function() {
    if (!window.isUserLoggedIn) {
        return false;
    }

    if (!firebaseAuth) {
        console.error('Firebase not initialized');
        return false;
    }
    
    // Prevent multiple concurrent sign-in requests
    if (signInPromise) {
        return signInPromise;
    }
    
    signInPromise = (async () => {
        try {
            // Use web route (not API) for session-based auth
            const response = await fetch('/api/firebase-token', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                // send cookies for sanctum/session authentication
                credentials: 'include'
            });
            
            if (!response.ok) {
                console.error('Failed to get Firebase token');
                return false;
            }
            
            const data = await response.json();
            if (!data || !data.token) {
                console.warn('No Firebase token returned from backend', data?.debug ?? data);
                return false;
            }

            await signInWithCustomToken(firebaseAuth, data.token);
            return true;
            
        } catch (error) {
            console.error('Firebase sign in failed:', error);
            return false;
        } finally {
            signInPromise = null;
        }
    })();
    
    return signInPromise;
};

/**
 * Sign out from Firebase
 */
window.signOutFirebase = async function() {
    if (firebaseAuth) {
        await signOut(firebaseAuth);
    }
};

/**
 * Check if user is authenticated with Firebase
 */
window.isFirebaseAuthenticated = function() {
    return currentFirebaseUser !== null;
};

// ============================================
// Watch Progress Functions (Firebase Only)
// ============================================

/**
 * Save watch progress to Firebase Realtime Database (dual client & server pipeline)
 */
window.saveWatchProgress = async function(mediaId, progressData) {
    const mediaKey = String(mediaId);

    // 1. Instantly update localStorage for offline/immediate response
    try {
        const localProgress = JSON.parse(localStorage.getItem('vidLinkProgress') || '{}');
        localProgress[mediaKey] = {
            ...progressData,
            last_updated: Date.now()
        };
        localStorage.setItem('vidLinkProgress', JSON.stringify(localProgress));
    } catch (error) {
        console.error('Local watch progress save error:', error);
    }

    // 2. Client-side Realtime Database direct write
    if (firebaseDb && currentFirebaseUser) {
        try {
            const progressRef = ref(firebaseDb, `users/${currentFirebaseUser.uid}/watchProgress/${mediaId}`);
            await set(progressRef, {
                ...progressData,
                last_updated: Date.now()
            });
        } catch (error) {
            console.error('Client RTDB save error:', error);
        }
    } else if (window.isUserLoggedIn && typeof window.signInToFirebase === 'function') {
        window.signInToFirebase().catch(() => {});
    }

    // 3. Server-side Realtime Database write via Laravel Admin SDK (guaranteed persistence)
    try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        const sanctumToken = localStorage.getItem('auth_token') || localStorage.getItem('token') || sessionStorage.getItem('auth_token') || '';
        const headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        };
        if (csrf) headers['X-CSRF-TOKEN'] = csrf;
        if (sanctumToken) headers['Authorization'] = `Bearer ${sanctumToken}`;

        fetch('/api/watch-progress', {
            method: 'POST',
            headers: headers,
            credentials: 'include',
            body: JSON.stringify({
                id: mediaId,
                type: progressData.type || 'movie',
                title: progressData.title || 'Unknown',
                poster_path: progressData.poster_path || null,
                backdrop_path: progressData.backdrop_path || null,
                progress: progressData.progress,
                last_season_watched: progressData.last_season_watched || null,
                last_episode_watched: progressData.last_episode_watched || null,
                show_progress: progressData.show_progress || null,
                last_updated: Date.now()
            })
        }).catch(err => console.debug('Backend RTDB sync error:', err));
    } catch (err) {
        console.debug('Failed to trigger backend watch progress sync:', err);
    }
};

/**
 * Get watch progress (Client RTDB + Backend RTDB fallback + localStorage sync)
 */
window.getWatchProgress = async function(mediaId) {
    const mediaKey = String(mediaId);
    let localData = null;
    try {
        const localProgress = JSON.parse(localStorage.getItem('vidLinkProgress') || '{}');
        localData = localProgress[mediaKey] || null;
    } catch (e) {}

    // 1. Try client-side Firebase RTDB if authenticated
    if (firebaseDb && currentFirebaseUser) {
        try {
            const progressRef = ref(firebaseDb, `users/${currentFirebaseUser.uid}/watchProgress/${mediaId}`);
            const snapshot = await get(progressRef);
            
            if (snapshot.exists()) {
                const remoteData = snapshot.val();
                if (localData && (localData.last_updated || 0) > (remoteData.last_updated || 0)) {
                    return localData;
                }
                try {
                    const localProgress = JSON.parse(localStorage.getItem('vidLinkProgress') || '{}');
                    localProgress[mediaKey] = remoteData;
                    localStorage.setItem('vidLinkProgress', JSON.stringify(localProgress));
                } catch (e) {}
                return remoteData;
            }
        } catch (error) {
            console.error('Firebase get error:', error);
        }
    }

    // 2. Query server API directly (reads from RTDB via Admin SDK) - only if authenticated
    if (window.isUserLoggedIn) {
        try {
            const resp = await fetch(`/api/watch-progress/${encodeURIComponent(mediaId)}`, {
                headers: { 'Accept': 'application/json' },
                credentials: 'include'
            });
            if (resp.ok) {
                const json = await resp.json();
                if (json && json.data) {
                    const remoteData = json.data;
                    if (localData && (localData.last_updated || 0) > (remoteData.last_updated || 0)) {
                        return localData;
                    }
                    try {
                        const localProgress = JSON.parse(localStorage.getItem('vidLinkProgress') || '{}');
                        localProgress[mediaKey] = remoteData;
                        localStorage.setItem('vidLinkProgress', JSON.stringify(localProgress));
                    } catch (e) {}
                    return remoteData;
                }
            }
        } catch (e) {
            console.debug('API watch progress fetch error:', e);
        }
    }

    return localData;
};

/**
 * Get all continue watching items (RTDB + API + localStorage fallback)
 */
window.getContinueWatching = async function(maxItems = 10) {
    // 1. Try client-side RTDB
    if (firebaseDb && currentFirebaseUser) {
        try {
            const progressRef = ref(firebaseDb, `users/${currentFirebaseUser.uid}/watchProgress`);
            const snapshot = await get(progressRef);
            
            if (snapshot.exists()) {
                const data = snapshot.val();
                return Object.entries(data)
                    .map(([id, item]) => ({ id, ...item }))
                    .filter(item => {
                        if (!item.progress) return false;
                        const watched = Number(item.progress.watched) || 0;
                        const duration = Number(item.progress.duration) || 0;
                        if (duration <= 0) return false;
                        const percent = (watched / duration) * 100;
                        return percent >= 1 && percent < 95;
                    })
                    .sort((a, b) => (b.last_updated || 0) - (a.last_updated || 0))
                    .slice(0, maxItems);
            }
        } catch (error) {
            console.error('Firebase continue watching query error:', error);
        }
    }

    // 2. Query backend API - only if authenticated
    if (window.isUserLoggedIn) {
        try {
            const resp = await fetch('/api/watch-progress/all', {
                headers: { 'Accept': 'application/json' },
                credentials: 'include'
            });
            if (resp.ok) {
                const json = await resp.json();
                if (json && json.data && typeof json.data === 'object') {
                    return Object.entries(json.data)
                        .map(([id, item]) => ({ id, ...item }))
                        .filter(item => {
                            if (!item.progress) return false;
                            const watched = Number(item.progress.watched) || 0;
                            const duration = Number(item.progress.duration) || 0;
                            if (duration <= 0) return false;
                            const percent = (watched / duration) * 100;
                            return percent >= 1 && percent < 95;
                        })
                        .sort((a, b) => (b.last_updated || 0) - (a.last_updated || 0))
                        .slice(0, maxItems);
                }
            }
        } catch (e) {}
    }

    // 3. Fallback to localStorage
    try {
        const stored = localStorage.getItem('vidLinkProgress');
        if (stored) {
            const data = JSON.parse(stored);
            return Object.entries(data)
                .map(([id, item]) => ({ id, ...item }))
                .filter(item => {
                    if (!item.progress) return false;
                    const watched = Number(item.progress.watched) || 0;
                    const duration = Number(item.progress.duration) || 0;
                    if (duration <= 0) return false;
                    const percent = (watched / duration) * 100;
                    return percent >= 1 && percent < 95;
                })
                .sort((a, b) => (b.last_updated || 0) - (a.last_updated || 0))
                .slice(0, maxItems);
        }
    } catch (e) {}

    return [];
};

/**
 * Remove item from watch progress (RTDB + Backend API + localStorage)
 */
window.removeWatchProgress = async function(mediaId) {
    // 1. Remove from localStorage
    try {
        const allProgress = JSON.parse(localStorage.getItem('vidLinkProgress') || '{}');
        delete allProgress[String(mediaId)];
        localStorage.setItem('vidLinkProgress', JSON.stringify(allProgress));
    } catch (e) {}

    // 2. Remove from client RTDB
    if (firebaseDb && currentFirebaseUser) {
        try {
            const progressRef = ref(firebaseDb, `users/${currentFirebaseUser.uid}/watchProgress/${mediaId}`);
            await remove(progressRef);
        } catch (error) {
            console.error('Firebase delete error:', error);
        }
    }

    // 3. Remove via Backend API - only if authenticated
    if (window.isUserLoggedIn) {
        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            fetch(`/api/watch-progress/${encodeURIComponent(mediaId)}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf || ''
                },
                credentials: 'include'
            }).catch(() => {});
        } catch (e) {}
    }
};

/**
 * Clear all watch progress (RTDB + Backend API + localStorage)
 */
window.clearAllWatchProgress = async function() {
    // 1. Clear localStorage
    try {
        localStorage.removeItem('vidLinkProgress');
    } catch (e) {}

    // 2. Clear client RTDB
    if (firebaseDb && currentFirebaseUser) {
        try {
            const progressRef = ref(firebaseDb, `users/${currentFirebaseUser.uid}/watchProgress`);
            await remove(progressRef);
        } catch (error) {
            console.error('Firebase clear error:', error);
        }
    }

    // 3. Clear via Backend API - only if authenticated
    if (window.isUserLoggedIn) {
        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            fetch('/api/watch-progress', {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf || ''
                },
                credentials: 'include'
            }).catch(() => {});
        } catch (e) {}
    }
};

/**
 * Get ALL watch history items (RTDB + Backend API + localStorage)
 */
window.getAllWatchHistory = async function() {
    // 1. Try client RTDB
    if (firebaseDb && currentFirebaseUser) {
        try {
            const progressRef = ref(firebaseDb, `users/${currentFirebaseUser.uid}/watchProgress`);
            const snapshot = await get(progressRef);
            
            if (snapshot.exists()) {
                const data = snapshot.val();
                return Object.entries(data)
                    .map(([id, item]) => ({ id, ...item }))
                    .sort((a, b) => (b.last_updated || 0) - (a.last_updated || 0));
            }
        } catch (error) {
            console.error('Firebase get all error:', error);
        }
    }

    // 2. Query backend API - only if authenticated
    if (window.isUserLoggedIn) {
        try {
            const resp = await fetch('/api/watch-progress/all', {
                headers: { 'Accept': 'application/json' },
                credentials: 'include'
            });
            if (resp.ok) {
                const json = await resp.json();
                if (json && json.data && typeof json.data === 'object') {
                    return Object.entries(json.data)
                        .map(([id, item]) => ({ id, ...item }))
                        .sort((a, b) => (b.last_updated || 0) - (a.last_updated || 0));
                }
            }
        } catch (e) {
            console.debug('Failed to fetch watch history from API:', e);
        }
    }

    // 3. Fallback to localStorage
    try {
        const stored = localStorage.getItem('vidLinkProgress');
        if (stored) {
            const data = JSON.parse(stored);
            return Object.entries(data)
                .map(([id, item]) => ({ id, ...item }))
                .sort((a, b) => (b.last_updated || 0) - (a.last_updated || 0));
        }
    } catch (e) {}

    return [];
};

// ============================================
// Notification Functions (Firebase)
// ============================================

/**
 * Start listening for notifications in real-time
 */
function startNotificationListener() {
    if (!firebaseDb || !currentFirebaseUser || notificationUnsubscribe) return;
    
    const notifPath = `notifications/${currentFirebaseUser.uid}`;
    console.log('🔔 Listening to path:', notifPath);
    const notifRef = ref(firebaseDb, notifPath);
    
    notificationUnsubscribe = onValue(notifRef, (snapshot) => {
        console.log('🔔 Notification snapshot received:', snapshot.exists(), snapshot.val());
        if (snapshot.exists()) {
            const notifications = [];
            snapshot.forEach((child) => {
                notifications.push({
                    id: child.key,
                    ...child.val()
                });
            });
            
            // Sort by timestamp descending
            notifications.sort((a, b) => (b.timestamp || 0) - (a.timestamp || 0));
            
            console.log('🔔 Parsed notifications:', notifications);
            
            // Update the notification store
            updateNotificationStore(notifications);
        } else {
            console.log('🔔 No notifications found');
            updateNotificationStore([]);
        }
    }, (error) => {
        console.error('🔔 Notification listener error:', error);
    });
    
    console.log('🔔 Notification listener started');
}

/**
 * Stop listening for notifications
 */
function stopNotificationListener() {
    if (notificationUnsubscribe) {
        notificationUnsubscribe();
        notificationUnsubscribe = null;
        console.log('🔔 Notification listener stopped');
    }
}

/**
 * Update notification store (Alpine reactive)
 */
function updateNotificationStore(notifications) {
    // Dispatch custom event for Alpine components
    window.dispatchEvent(new CustomEvent('notifications-updated', {
        detail: { notifications }
    }));
    
    // Store globally for easy access
    window.userNotifications = notifications;
    window.unreadNotificationCount = notifications.filter(n => !n.read).length;
    
    // Update badge if element exists
    const badge = document.getElementById('notification-badge');
    if (badge) {
        const unread = window.unreadNotificationCount;
        badge.textContent = unread > 99 ? '99+' : unread;
        badge.classList.toggle('hidden', unread === 0);
    }
}

/**
 * Get all notifications for current user
 */
window.getNotifications = async function() {
    if (!firebaseDb || !currentFirebaseUser) {
        return [];
    }
    
    try {
        const notifRef = ref(firebaseDb, `notifications/${currentFirebaseUser.uid}`);
        const snapshot = await get(notifRef);
        
        if (snapshot.exists()) {
            const notifications = [];
            snapshot.forEach((child) => {
                notifications.push({
                    id: child.key,
                    ...child.val()
                });
            });
            return notifications.sort((a, b) => (b.timestamp || 0) - (a.timestamp || 0));
        }
    } catch (error) {
        console.error('Error getting notifications:', error);
    }
    
    return [];
};

/**
 * Mark notification as read
 */
window.markNotificationRead = async function(notificationId) {
    if (!firebaseDb || !currentFirebaseUser) return;
    
    try {
        const notifRef = ref(firebaseDb, `notifications/${currentFirebaseUser.uid}/${notificationId}`);
        await update(notifRef, { read: true });
    } catch (error) {
        console.error('Error marking notification read:', error);
    }
};

/**
 * Mark all notifications as read
 */
window.markAllNotificationsRead = async function() {
    if (!firebaseDb || !currentFirebaseUser) return;
    
    try {
        const notifications = window.userNotifications || [];
        const updates = {};
        
        notifications.forEach(n => {
            if (!n.read) {
                updates[`notifications/${currentFirebaseUser.uid}/${n.id}/read`] = true;
            }
        });
        
        if (Object.keys(updates).length > 0) {
            await update(ref(firebaseDb), updates);
        }
    } catch (error) {
        console.error('Error marking all notifications read:', error);
    }
};

/**
 * Delete a notification
 */
window.deleteNotification = async function(notificationId) {
    if (!firebaseDb || !currentFirebaseUser) return;
    
    try {
        const notifRef = ref(firebaseDb, `notifications/${currentFirebaseUser.uid}/${notificationId}`);
        await remove(notifRef);
    } catch (error) {
        console.error('Error deleting notification:', error);
    }
};

/**
 * Clear all notifications
 */
window.clearAllNotifications = async function() {
    if (!firebaseDb || !currentFirebaseUser) return;
    
    try {
        const notifRef = ref(firebaseDb, `notifications/${currentFirebaseUser.uid}`);
        await remove(notifRef);
    } catch (error) {
        console.error('Error clearing notifications:', error);
    }
};

// ============================================
// FCM Token Functions
// ============================================

/**
 * Check if running as installed PWA
 */
function isInstalledPWA() {
    return window.matchMedia('(display-mode: standalone)').matches 
        || window.navigator.standalone === true;
}

/**
 * Check if the current browser supports web push notifications
 */
function isWebPushSupported() {
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    
    // iOS Safari browser (not PWA) does not support web push
    if (isIOS && !isInstalledPWA()) {
        console.log('🔔 iOS Safari detected - add to home screen for push notifications');
        return false;
    }
    
    // iOS PWA on iOS 16.4+ supports push notifications
    if (isIOS && isInstalledPWA()) {
        console.log('🔔 iOS PWA detected - checking push support');
        // Check if push is actually supported (iOS 16.4+)
        if (!('PushManager' in window)) {
            console.log('🔔 iOS version does not support web push (requires iOS 16.4+)');
            return false;
        }
    }
    
    // Check for required APIs
    if (!('serviceWorker' in navigator)) {
        console.log('🔔 Service Workers not supported');
        return false;
    }
    
    if (!('PushManager' in window)) {
        console.log('🔔 Push API not supported');
        return false;
    }
    
    if (!('Notification' in window)) {
        console.log('🔔 Notifications API not supported');
        return false;
    }
    
    return true;
}

/**
 * Show prompt to install PWA for iOS users
 */
function showIOSInstallPrompt() {
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    
    if (isIOS && !isInstalledPWA()) {
        // Check if we've shown this recently
        const lastPrompt = localStorage.getItem('ios_pwa_prompt');
        if (lastPrompt && Date.now() - parseInt(lastPrompt) < 86400000) { // 24 hours
            return;
        }
        
        // Create and show the install prompt
        const prompt = document.createElement('div');
        prompt.id = 'ios-install-prompt';
        prompt.className = 'fixed bottom-4 left-4 right-4 z-[9999] bg-gray-800 border border-gray-700 rounded-xl shadow-2xl p-4 transform translate-y-full transition-transform duration-300';
        prompt.innerHTML = `
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-viewlix-red/20 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-viewlix-red" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-white mb-1">Install Viewlix App</p>
                    <p class="text-xs text-gray-400">Add to home screen to get push notifications!</p>
                    <p class="text-xs text-gray-500 mt-1">Tap <svg class="inline w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L12 14M12 2L8 6M12 2L16 6M4 14V20H20V14"/></svg> then "Add to Home Screen"</p>
                </div>
                <button onclick="document.getElementById('ios-install-prompt').remove(); localStorage.setItem('ios_pwa_prompt', Date.now())" class="text-gray-400 hover:text-white p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(prompt);
        
        // Animate in
        setTimeout(() => {
            prompt.classList.remove('translate-y-full');
        }, 1000);
        
        // Auto hide after 10 seconds
        setTimeout(() => {
            if (document.getElementById('ios-install-prompt')) {
                prompt.classList.add('translate-y-full');
                setTimeout(() => prompt.remove(), 300);
            }
        }, 10000);
    }
}

/**
 * Request notification permission and get FCM token
 */
async function requestFcmPermission() {
    if (!firebaseMessaging || !currentFirebaseUser) return;
    
    // Skip FCM in development (self-signed SSL certificates cause issues)
    const hostname = window.location.hostname;
    if (hostname === 'localhost' || hostname.endsWith('.test') || hostname.endsWith('.local')) {
        console.log('🔔 FCM skipped in development environment');
        return;
    }
    
    // Check if VAPID key is configured
    if (!window.firebaseVapidKey) {
        console.log('🔔 FCM skipped - no VAPID key configured');
        return;
    }
    
    // Check if web push is supported (handles iOS PWA detection)
    if (!isWebPushSupported()) {
        // Show iOS install prompt for non-PWA iOS users
        showIOSInstallPrompt();
        console.log('🔔 Web push not supported on this browser/platform');
        return;
    }
    
    try {
        // Request permission
        const permission = await Notification.requestPermission();
        
        if (permission === 'granted') {
            console.log('🔔 Notification permission granted');
            await getFcmToken();
        } else {
            console.log('🔔 Notification permission denied');
        }
    } catch (error) {
        console.warn('Error requesting notification permission:', error.message);
    }
}

/**
 * Get FCM token and save to Firebase
 */
async function getFcmToken() {
    if (!firebaseMessaging || !currentFirebaseUser) return;
    
    // Skip if no VAPID key
    if (!window.firebaseVapidKey) {
        console.log('🔔 FCM skipped - no VAPID key');
        return;
    }
    
    // Double-check web push support
    if (!isWebPushSupported()) {
        return;
    }
    
    try {
        // First register the service worker
        let swRegistration = null;
        try {
            swRegistration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
            console.log('🔔 Service Worker registered');
        } catch (swError) {
            console.warn('🔔 Service Worker registration failed:', swError.message);
            return;
        }
        
        const token = await getToken(firebaseMessaging, {
            vapidKey: window.firebaseVapidKey,
            serviceWorkerRegistration: swRegistration
        });
        
        if (token) {
            console.log('🔔 FCM Token obtained');
            await saveFcmToken(token);
        } else {
            console.warn('🔔 No FCM token received');
        }
    } catch (error) {
        // Silently handle FCM errors - push notifications are optional
        if (error.code === 'messaging/permission-blocked') {
            console.log('🔔 Push notifications blocked by user');
        } else if (error.name === 'AbortError') {
            console.log('🔔 FCM unavailable - push notifications disabled');
        } else {
            console.warn('🔔 FCM error:', error.message || error);
        }
    }
}

/**
 * Save FCM token to Firebase for the current user
 */
async function saveFcmToken(token) {
    if (!firebaseDb || !currentFirebaseUser) return;
    
    try {
        const tokenRef = ref(firebaseDb, `fcmTokens/${currentFirebaseUser.uid}/${token.substring(0, 20)}`);
        await set(tokenRef, {
            token: token,
            createdAt: Date.now(),
            platform: navigator.platform,
            userAgent: navigator.userAgent.substring(0, 100)
        });
        console.log('🔔 FCM Token saved to Firebase');
    } catch (error) {
        console.error('Error saving FCM token:', error);
    }
}

/**
 * Handle foreground messages
 */
window.setupForegroundMessages = function() {
    if (!firebaseMessaging) return;
    
    onMessage(firebaseMessaging, (payload) => {
        console.log('🔔 Foreground message received:', payload);
        
        // Show a toast notification
        showToastNotification(payload);
    });
};

/**
 * Show toast notification for foreground messages
 */
function showToastNotification(payload) {
    const title = payload.notification?.title || payload.data?.title || 'New Notification';
    const body = payload.notification?.body || payload.data?.body || '';
    const image = payload.data?.image || null;
    const link = payload.data?.link || '/';
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 z-[9999] max-w-sm bg-gray-800 border border-gray-700 rounded-lg shadow-lg p-4 transform translate-x-full transition-transform duration-300';
    toast.innerHTML = `
        <div class="flex items-start gap-3">
            ${image ? `<img src="${image}" class="w-12 h-12 rounded-lg object-cover" alt="">` : ''}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">${title}</p>
                <p class="text-xs text-gray-400 line-clamp-2">${body}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-gray-400 hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Click to navigate
    toast.addEventListener('click', (e) => {
        if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'SVG' && e.target.tagName !== 'PATH') {
            window.location.href = link;
        }
    });
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

// Avatar upload function
/**
 * Attempt to open a file picker with permission handling.
 * Uses the File System Access API when available (Chrome/Edge/Android),
 * and falls back to a hidden input[type=file] for Safari/iOS and other browsers.
 * Returns a File object or null if the user cancels.
 */
window.pickFileWithPermission = async function(accept = { accept: { 'image/*': ['.png', '.jpg', '.jpeg', '.webp'] } }) {
    // Prefer native file system picker when available
    if (window.showOpenFilePicker) {
        try {
            const handles = await window.showOpenFilePicker({ multiple: false, types: [accept] });
            if (!handles || !handles.length) return null;
            const file = await handles[0].getFile();
            return file;
        } catch (err) {
            // NotAllowedError = user denied permission or dismissed picker
            console.debug('File System Access API picker failed or cancelled', err && err.name);
            return null;
        }
    }

    // Fallback: create a hidden input element and await user selection
    return await new Promise((resolve) => {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = Object.keys(accept.accept || {}).join(',') || 'image/*';
        input.style.display = 'none';
        document.body.appendChild(input);

        input.addEventListener('change', () => {
            const file = input.files && input.files[0] ? input.files[0] : null;
            resolve(file);
            input.remove();
        }, { once: true });

        // If the user cancels the picker, resolve null after a short timeout
        input.addEventListener('cancel', () => { resolve(null); input.remove(); }, { once: true });

        // Trigger click
        input.click();
    });
};

/**
 * Upload avatar file to Firebase Storage. If `file` is omitted, open a file picker
 * with mobile-friendly permission flow and use the selected file.
 */
window.uploadAvatarToFirebase = async function(file) {
    if (!firebaseApp) throw new Error('Firebase not initialized');
    const storage = getStorage(firebaseApp);
    const userId = currentFirebaseUser?.uid;
    if (!userId) throw new Error('User not authenticated');

    let chosenFile = file || null;
    if (!chosenFile) {
        // Prompt the user to pick a file (mobile-safe)
        chosenFile = await window.pickFileWithPermission();
        if (!chosenFile) throw new Error('No file selected');
    }

    const avatarRef = storageRef(storage, `avatars/${userId}/${Date.now()}_${chosenFile.name}`);
    await uploadBytes(avatarRef, chosenFile);
    return await getDownloadURL(avatarRef);
};

// External JS for watch page
// NOTE: This file must be loaded after Blade-populated globals: mediaData, providedSources, defaultEmbed

/* Helper: update favorite icon (expects global isFavorited and element with id 'favoriteIcon') */
function updateFavoriteIcon() {
	const icon = document.getElementById('favoriteIcon');
	if (!icon) return;

	if (window.isFavorited) {
		icon.setAttribute('fill', 'currentColor');
		icon.classList.remove('text-gray-400');
		icon.classList.add('text-viewlix-red');
	} else {
		icon.setAttribute('fill', 'none');
		icon.classList.remove('text-viewlix-red');
		icon.classList.add('text-gray-400');
	}
}

// ============================================================
// VidSrc & Viewlix Player Watch Progress & Resume Playback
// Specs: https://vidsrc2.ru/vidsrc/docs/
// Handles: content.viewlix.online, cdn.viewlix.online, vidsrc
// ============================================================

let lastSavedTime = 0;
let lastSavedProgress = 0;
let currentPlaybackTime = 0;
let currentDuration = 0;
let isCompleted = false;
let upNextTimer = null;
let initialResumePosition = 0;
const SAVE_INTERVAL_SECONDS = 10; // Sync to Firebase RTDB every 10s during playback

// Helper: Format seconds into MM:SS or H:MM:SS
function formatSecondsToTime(totalSeconds) {
	const sec = Math.floor(Math.max(0, totalSeconds || 0));
	const hours = Math.floor(sec / 3600);
	const minutes = Math.floor((sec % 3600) / 60);
	const seconds = sec % 60;
	const pad = n => String(n).padStart(2, '0');
	if (hours > 0) {
		return `${hours}:${pad(minutes)}:${pad(seconds)}`;
	}
	return `${minutes}:${pad(seconds)}`;
}

// Origin verification for VidSrc & Viewlix custom domains
function isAllowedPlayerOrigin(origin) {
	if (!origin || typeof origin !== 'string') return true; // allow relative or same-window postMessages
	try {
		const url = new URL(origin);
		const host = url.hostname.toLowerCase();

		// 1. Allow local development origins (localhost, 127.0.0.1, ::1)
		if (host === 'localhost' || host === '127.0.0.1' || host === '::1') {
			return true;
		}

		// 2. Allow same hostname as current page
		if (host === window.location.hostname.toLowerCase()) {
			return true;
		}

		// 3. Allow Viewlix official domains & subdomains
		if (host.endsWith('viewlix.online') || host.endsWith('viewlix.site') || host.endsWith('viewlix.to') || host.includes('viewlix')) {
			return true;
		}

		// 4. Check against all provided video source base URLs
		if (typeof providedSources !== 'undefined' && Array.isArray(providedSources)) {
			for (const s of providedSources) {
				if (s && (s.base_url || s.embed)) {
					try {
						const target = s.base_url || s.embed;
						const sHost = new URL(target, window.location.origin).hostname.toLowerCase();
						if (host === sHost || host.endsWith('.' + sHost) || sHost.endsWith('.' + host)) {
							return true;
						}
					} catch (e) { }
				}
			}
		}

		// 5. Check against all iframe sources currently in the DOM
		const iframes = document.querySelectorAll('iframe');
		for (const ifr of iframes) {
			if (ifr.src) {
				try {
					const ifrHost = new URL(ifr.src, window.location.origin).hostname.toLowerCase();
					if (host === ifrHost || host.endsWith('.' + ifrHost) || ifrHost.endsWith('.' + host)) {
						return true;
					}
				} catch (e) { }
			}
		}

		// 6. Common stream provider domains
		return host.endsWith('vidsrc2.ru') ||
			host.endsWith('vidsrc-ip.com') ||
			host.includes('vidsrc') ||
			host.includes('vidlink') ||
			host.includes('embed') ||
			host.includes('player') ||
			host.includes('stream');
	} catch (e) {
		return true;
	}
}

// Fallback watch progress saver if app.js is deferred or not yet initialized
if (typeof window.saveWatchProgress !== 'function') {
	window.saveWatchProgress = async function (mediaId, progressData) {
		const mediaKey = String(mediaId);
		try {
			const allProgress = JSON.parse(localStorage.getItem('vidLinkProgress') || '{}');
			allProgress[mediaKey] = { ...progressData, last_updated: Date.now() };
			localStorage.setItem('vidLinkProgress', JSON.stringify(allProgress));
		} catch (e) { }

		try {
			const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
			const sanctumToken = localStorage.getItem('auth_token') || localStorage.getItem('token') || '';
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
			}).catch(() => { });
		} catch (e) { }
	};
}

// Helper: Build progress data matching Firebase RTDB & LocalStorage schema
function buildProgressData(watchedTime, duration, status = 'playing') {
	let existingShowProgress = {};
	try {
		const allProgress = JSON.parse(localStorage.getItem('vidLinkProgress') || '{}');
		const prevData = allProgress[String(mediaData.id)];
		if (prevData?.show_progress) {
			existingShowProgress = { ...prevData.show_progress };
		}
	} catch (e) { }

	const progressData = {
		id: mediaData.id,
		type: mediaData.type,
		title: mediaData.title,
		poster_path: mediaData.posterPath || mediaData.poster_path || null,
		backdrop_path: mediaData.backdropPath || mediaData.backdrop_path || null,
		progress: {
			watched: watchedTime,
			duration: duration
		},
		last_updated: Date.now()
	};

	if (mediaData.type !== 'movie' && mediaData.season && mediaData.episode) {
		progressData.last_season_watched = String(mediaData.season);
		progressData.last_episode_watched = String(mediaData.episode);

		const seasonKey = `s${mediaData.season}e${mediaData.episode}`;
		progressData.show_progress = {
			...existingShowProgress,
			[seasonKey]: {
				season: String(mediaData.season),
				episode: String(mediaData.episode),
				progress: {
					watched: watchedTime,
					duration: duration
				}
			}
		};
	}

	return progressData;
}

// Inspect local cache to find saved watch timestamp for current item
function getSavedProgressPosition() {
	try {
		const allProgress = JSON.parse(localStorage.getItem('vidLinkProgress') || '{}');
		const saved = allProgress[String(mediaData.id)];
		if (saved) {
			if (mediaData.type === 'movie') {
				const w = Number(saved.progress?.watched) || 0;
				const d = Number(saved.progress?.duration) || 0;
				if (w >= 10 && (d <= 0 || (w / d) < 0.95)) {
					return w;
				}
			} else {
				const seasonKey = `s${mediaData.season}e${mediaData.episode}`;
				const epData = saved.show_progress?.[seasonKey];
				if (epData?.progress) {
					const w = Number(epData.progress.watched) || 0;
					const d = Number(epData.progress.duration) || 0;
					if (w >= 10 && (d <= 0 || (w / d) < 0.95)) {
						return w;
					}
				} else if (saved.last_season_watched == mediaData.season && saved.last_episode_watched == mediaData.episode && saved.progress) {
					const w = Number(saved.progress.watched) || 0;
					const d = Number(saved.progress.duration) || 0;
					if (w >= 10 && (d <= 0 || (w / d) < 0.95)) {
						return w;
					}
				}
			}
		}
	} catch (e) { }
	return 0;
}

// Format embed URL according to VidSrc specification
function formatEmbedUrl(rawUrl, resumeSeconds = 0) {
	if (!rawUrl || rawUrl === 'about:blank') return rawUrl;

	try {
		const url = new URL(rawUrl, window.location.origin);
		const host = url.hostname.toLowerCase();
		const isCustomOrVidSrc = host.includes('viewlix.online') || host.includes('vidsrc');

		if (isCustomOrVidSrc) {
			// Direct autoplay supported on custom domain (content.viewlix.online)
			url.searchParams.set('autoplay', '1');

			// Default subtitles language to english
			if (!url.searchParams.has('ds_lang')) {
				url.searchParams.set('ds_lang', 'en');
			}

			// TV auto-next parameter
			if (mediaData.type !== 'movie') {
				url.searchParams.set('autonext', '1');
			}

			// Resume playback parameter (startAt={seconds})
			if (resumeSeconds >= 10) {
				url.searchParams.set('startAt', String(Math.floor(resumeSeconds)));
			} else {
				url.searchParams.delete('startAt');
			}
		}

		return url.toString();
	} catch (e) {
		return rawUrl;
	}
}

// Show resume notification toast with Start Over option
function showResumeToast(seconds) {
	const toast = document.getElementById('resumeToast');
	const text = document.getElementById('resumeToastText');
	if (!toast || !text) return;

	text.textContent = `Resuming from ${formatSecondsToTime(seconds)}`;
	toast.classList.remove('hidden');
	// Trigger transition
	setTimeout(() => {
		toast.classList.remove('opacity-0', '-translate-y-2');
		toast.classList.add('opacity-100', 'translate-y-0');
	}, 50);

	// Auto-dismiss after 8 seconds
	setTimeout(() => {
		hideResumeToast();
	}, 8000);
}

function hideResumeToast() {
	const toast = document.getElementById('resumeToast');
	if (!toast) return;
	toast.classList.remove('opacity-100', 'translate-y-0');
	toast.classList.add('opacity-0', '-translate-y-2');
	setTimeout(() => {
		toast.classList.add('hidden');
	}, 300);
}

// Show Up Next auto-advance countdown overlay
function showUpNextOverlay() {
	if (upNextTimer) clearInterval(upNextTimer);

	const overlay = document.getElementById('upNextOverlay');
	const titleEl = document.getElementById('upNextTitle');
	const countdownEl = document.getElementById('upNextCountdown');
	if (!overlay || !mediaData.nextEpisodeUrl) return;

	if (titleEl) {
		const epNum = mediaData.nextEpisodeNumber ? `E${mediaData.nextEpisodeNumber}: ` : '';
		titleEl.textContent = `${epNum}${mediaData.nextEpisodeName || 'Next Episode'}`;
	}

	let remaining = 5;
	if (countdownEl) countdownEl.textContent = String(remaining);

	overlay.classList.remove('hidden');
	setTimeout(() => {
		overlay.classList.remove('opacity-0', 'translate-y-4');
		overlay.classList.add('opacity-100', 'translate-y-0');
	}, 50);

	upNextTimer = setInterval(() => {
		remaining--;
		if (countdownEl) countdownEl.textContent = String(remaining);
		if (remaining <= 0) {
			clearInterval(upNextTimer);
			upNextTimer = null;
			window.location.href = mediaData.nextEpisodeUrl;
		}
	}, 1000);
}

function hideUpNextOverlay() {
	if (upNextTimer) {
		clearInterval(upNextTimer);
		upNextTimer = null;
	}
	const overlay = document.getElementById('upNextOverlay');
	if (!overlay) return;
	overlay.classList.remove('opacity-100', 'translate-y-0');
	overlay.classList.add('opacity-0', 'translate-y-4');
	setTimeout(() => {
		overlay.classList.add('hidden');
	}, 300);
}

// Attach UI listeners for resume toast and up next overlay
document.addEventListener('DOMContentLoaded', function () {
	const startOverBtn = document.getElementById('resumeStartOverBtn');
	if (startOverBtn) {
		startOverBtn.addEventListener('click', function () {
			hideResumeToast();
			currentPlaybackTime = 0;
			lastSavedTime = 0;
			initialResumePosition = 0;

			// Reset progress in storage
			const resetData = buildProgressData(0, currentDuration || 1);
			if (typeof window.saveWatchProgress === 'function') {
				window.saveWatchProgress(mediaData.id, resetData);
			}

			// Reload current source from beginning (startAt=0)
			applySelectedSource(true, 0);
			showWatchToast('Playing from beginning');
		});
	}

	const dismissResumeBtn = document.getElementById('resumeDismissBtn');
	if (dismissResumeBtn) {
		dismissResumeBtn.addEventListener('click', hideResumeToast);
	}

	const upNextCancelBtn = document.getElementById('upNextCancelBtn');
	if (upNextCancelBtn) {
		upNextCancelBtn.addEventListener('click', hideUpNextOverlay);
	}

	const upNextPlayNowBtn = document.getElementById('upNextPlayNowBtn');
	if (upNextPlayNowBtn) {
		upNextPlayNowBtn.addEventListener('click', function () {
			if (upNextTimer) clearInterval(upNextTimer);
			if (mediaData.nextEpisodeUrl) {
				window.location.href = mediaData.nextEpisodeUrl;
			}
		});
	}
});

// ============================================================
// PostMessage Listener for VidSrc PLAYER_EVENT & Fallbacks
// ============================================================

window.addEventListener('message', function (event) {
	if (!isAllowedPlayerOrigin(event.origin)) return;

	let eventData = event.data;
	if (typeof eventData === 'string') {
		try {
			eventData = JSON.parse(eventData);
		} catch (e) {
			return;
		}
	}

	if (!eventData || typeof eventData !== 'object') return;

	// Handle TV state updates from embedded player
	if (eventData.type === 'TV_STATE' || eventData.event === 'TV_STATE') {
		const s = Number(eventData.season ?? eventData.data?.season);
		const ep = Number(eventData.episode ?? eventData.data?.episode);
		if (s && ep) {
			mediaData.season = s;
			mediaData.episode = ep;
		}
		return;
	}

	// Extract payload data (support nested or flat objects)
	const rawPayload = (typeof eventData.data === 'object' && eventData.data !== null) ? eventData.data : eventData;
	const eventType = (eventData.type || eventData.event || '').toString();

	// Extract playback status
	let status = rawPayload.player_status || rawPayload.status || rawPayload.event;
	if (!status) {
		if (eventType === 'pause' || eventType === 'paused') status = 'paused';
		else if (eventType === 'ended' || eventType === 'completed') status = 'completed';
		else if (eventType === 'seeked') status = 'seeked';
		else status = 'playing';
	}

	// Extract watched time
	let watchedTime = Number(
		rawPayload.player_progress ??
		rawPayload.currentTime ??
		rawPayload.watched ??
		rawPayload.progress?.watched ??
		rawPayload.progress ??
		rawPayload.time ??
		rawPayload.seconds
	);

	// Extract total duration
	let duration = Number(
		rawPayload.player_duration ??
		rawPayload.duration ??
		rawPayload.totalTime ??
		rawPayload.progress?.duration ??
		rawPayload.totalDuration
	);

	// Fallback check for MEDIA_DATA structure
	if ((!Number.isFinite(watchedTime) || !Number.isFinite(duration) || duration <= 0) && eventData.type === 'MEDIA_DATA' && eventData.data) {
		const mediaKey = String(mediaData.id);
		const item = eventData.data[mediaKey];
		if (item?.progress) {
			watchedTime = Number(item.progress.watched);
			duration = Number(item.progress.duration);
		}
	}

	if (Number.isFinite(watchedTime)) {
		currentPlaybackTime = watchedTime;
	}
	if (Number.isFinite(duration) && duration > 0) {
		currentDuration = duration;
	}

	if (!Number.isFinite(watchedTime) || !Number.isFinite(duration) || duration <= 0) {
		return;
	}

	const progressData = buildProgressData(watchedTime, duration, status);

	// Always update localStorage cache immediately for zero data loss
	try {
		const allProgress = JSON.parse(localStorage.getItem('vidLinkProgress') || '{}');
		allProgress[String(mediaData.id)] = progressData;
		localStorage.setItem('vidLinkProgress', JSON.stringify(allProgress));
	} catch (e) { }

	// Handle playback completion
	if (status === 'completed' || (duration > 0 && (watchedTime / duration) >= 0.95)) {
		isCompleted = true;
		const completedData = buildProgressData(duration, duration, 'completed');
		if (typeof window.saveWatchProgress === 'function') {
			window.saveWatchProgress(mediaData.id, completedData);
		}
		lastSavedTime = duration;

		// TV / Anime: auto advance next episode
		if (mediaData.type !== 'movie' && mediaData.nextEpisodeUrl) {
			showUpNextOverlay();
		} else if (mediaData.type === 'movie') {
			showWatchToast(`You completed ${mediaData.title}!`);
		}
		return;
	}

	// Handle pause or seek - save immediately
	if (status === 'paused' || status === 'seeked') {
		if (typeof window.saveWatchProgress === 'function') {
			window.saveWatchProgress(mediaData.id, progressData);
		}
		lastSavedTime = watchedTime;
		return;
	}

	// Handle active playback - save early once (after 3s) and then throttle every SAVE_INTERVAL_SECONDS
	if (status === 'playing' || status === 'timeupdate') {
		const timeSinceLastSave = Math.abs(watchedTime - lastSavedTime);
		const shouldEarlySave = lastSavedTime === 0 && watchedTime >= 3;
		if (shouldEarlySave || timeSinceLastSave >= SAVE_INTERVAL_SECONDS) {
			if (typeof window.saveWatchProgress === 'function') {
				window.saveWatchProgress(mediaData.id, progressData);
			}
			lastSavedTime = watchedTime;
		}
	}
});

// Save final progress when leaving the page (unload, pagehide, tab switch)
function saveFinalProgressOnLeave() {
	try {
		if (currentPlaybackTime > 0 && currentDuration > 0 && typeof window.saveWatchProgress === 'function') {
			const progressData = buildProgressData(currentPlaybackTime, currentDuration);
			window.saveWatchProgress(mediaData.id, progressData);
		}
	} catch (e) { }
}

window.addEventListener('beforeunload', saveFinalProgressOnLeave);
window.addEventListener('pagehide', saveFinalProgressOnLeave);
document.addEventListener('visibilitychange', function () {
	if (document.visibilityState === 'hidden') {
		saveFinalProgressOnLeave();
	}
});

// ============================================================
// Multi-source / server switching (iframe source selection)
// Relies on globals providedSources and defaultEmbed
// ============================================================

function buildSources() {
	if (Array.isArray(providedSources) && providedSources.length > 0) {
		return providedSources.map(s => ({ name: s.name || 'Source', embed: s.embed || defaultEmbed }));
	}

	return [{ name: 'Viewlix (ad-free)', embed: defaultEmbed }];
}

const sources = buildSources();
const sourceSelect = document.getElementById('sourceSelect');
const openSourceBtn = document.getElementById('openSourceBtn');
const playerIframe = document.getElementById('playerIframe') || document.querySelector('.video-container iframe');

function applySelectedSource(force = false, overrideResumeSeconds = null) {
	if (!playerIframe) return;

	const idx = sourceSelect ? parseInt(sourceSelect.value || 0) : 0;
	const baseSrc = sources[idx]?.embed || defaultEmbed;

	// Calculate resume timestamp: override > active playback > initial saved progress
	let resumeAt = 0;
	if (overrideResumeSeconds !== null) {
		resumeAt = overrideResumeSeconds;
	} else if (currentPlaybackTime >= 10) {
		resumeAt = currentPlaybackTime;
	} else if (initialResumePosition >= 10) {
		resumeAt = initialResumePosition;
	}

	const finalSrc = formatEmbedUrl(baseSrc, resumeAt);

	if (force || playerIframe.getAttribute('src') !== finalSrc) {
		playerIframe.setAttribute('src', finalSrc);

		// If resuming playback, display the resume indicator
		if (resumeAt >= 10) {
			showResumeToast(resumeAt);
		}
	}

	// Stylize player frame for Viewlix source
	try {
		const lower = String(baseSrc || '').toLowerCase();
		const isViewlix = lower.includes('viewlix.online') || lower.includes('viewlix.site') || lower.includes('viewlix');
		if (isViewlix) {
			playerIframe.setAttribute('scrolling', 'no');
			playerIframe.style.overflow = 'hidden';
			playerIframe.classList.add('hide-iframe-scrollbar');
		} else {
			playerIframe.removeAttribute('scrolling');
			playerIframe.style.overflow = '';
			playerIframe.classList.remove('hide-iframe-scrollbar');
		}
	} catch (e) { }

	// Save preference
	try {
		const key = `preferredSource_${mediaData.type}_${mediaData.id}`;
		localStorage.setItem(key, String(idx));
	} catch (e) { }

	if (openSourceBtn) {
		openSourceBtn.classList.remove('hidden');
	}
}

function populateSourceOptions() {
	if (!sourceSelect) {
		applySelectedSource();
		return;
	}

	sourceSelect.innerHTML = '';
	sources.forEach((s, idx) => {
		const opt = document.createElement('option');
		opt.value = idx;
		opt.text = s.name;
		sourceSelect.appendChild(opt);
	});

	try {
		const key = `preferredSource_${mediaData.type}_${mediaData.id}`;
		const saved = localStorage.getItem(key);
		if (saved !== null && !isNaN(parseInt(saved)) && sources[parseInt(saved)]) {
			sourceSelect.value = saved;
		} else {
			sourceSelect.value = 0;
		}
	} catch (e) {
		sourceSelect.value = 0;
	}

	applySelectedSource();
}

if (sourceSelect) sourceSelect.addEventListener('change', () => applySelectedSource(true));
if (openSourceBtn) {
	openSourceBtn.addEventListener('click', function () {
		const idx = parseInt(sourceSelect?.value || 0);
		const baseSrc = sources[idx]?.embed || defaultEmbed;
		const finalUrl = formatEmbedUrl(baseSrc, currentPlaybackTime || initialResumePosition);
		window.open(finalUrl, '_blank');
	});
}

// Player initialization and spinner management
function initPlayer() {
	// 1. Instantly check local storage for resume position
	initialResumePosition = getSavedProgressPosition();
	if (initialResumePosition >= 10) {
		lastSavedTime = initialResumePosition;
		console.log(`📺 Resuming from local cache: ${Math.floor(initialResumePosition)}s`);
	}

	// 2. Populate source dropdown and apply embed with startAt
	populateSourceOptions();

	// 3. Attach load listener to fade out loading spinner
	if (playerIframe) {
		playerIframe.addEventListener('load', function () {
			const spinner = document.getElementById('playerLoadingSpinner');
			if (spinner) {
				spinner.classList.add('opacity-0');
				setTimeout(() => spinner.classList.add('hidden'), 500);
			}
		});

		// Timeout fallback so spinner never remains stuck
		setTimeout(() => {
			const spinner = document.getElementById('playerLoadingSpinner');
			if (spinner) {
				spinner.classList.add('opacity-0');
				setTimeout(() => spinner.classList.add('hidden'), 500);
			}
		}, 3500);
	}

	// 4. Asynchronously check Firebase RTDB for cross-device watch progress
	if (typeof window.getWatchProgress === 'function') {
		window.getWatchProgress(mediaData.id).then(saved => {
			if (!saved) return;
			let remoteWatched = 0;
			let remoteDuration = 0;

			if (mediaData.type === 'movie') {
				remoteWatched = Number(saved.progress?.watched) || 0;
				remoteDuration = Number(saved.progress?.duration) || 0;
			} else {
				const seasonKey = `s${mediaData.season}e${mediaData.episode}`;
				const epData = saved.show_progress?.[seasonKey];
				if (epData?.progress) {
					remoteWatched = Number(epData.progress.watched) || 0;
					remoteDuration = Number(epData.progress.duration) || 0;
				} else if (saved.last_season_watched == mediaData.season && saved.last_episode_watched == mediaData.episode && saved.progress) {
					remoteWatched = Number(saved.progress.watched) || 0;
					remoteDuration = Number(saved.progress.duration) || 0;
				}
			}

			// If remote has newer / further progress and player has not advanced far yet
			if (remoteWatched >= 10 && (remoteDuration <= 0 || (remoteWatched / remoteDuration) < 0.95)) {
				if (currentPlaybackTime < 10 && Math.abs(remoteWatched - initialResumePosition) > 5) {
					initialResumePosition = remoteWatched;
					lastSavedTime = remoteWatched;
					console.log(`📺 Resuming from Firebase RTDB: ${Math.floor(remoteWatched)}s`);
					applySelectedSource(true, remoteWatched);
				}
			}
		}).catch(() => { });
	}
}

if (document.readyState === 'loading') {
	document.addEventListener('DOMContentLoaded', initPlayer);
} else {
	initPlayer();
}

// ============================================================
// TV Show Watch Page: Episode List Interactions & Tabs
// ============================================================

function setEpisodeViewMode(mode) {
	const detailedView = document.getElementById('episodesDetailedView');
	const compactView = document.getElementById('episodesCompactView');
	const btnCards = document.getElementById('btnViewCards');
	const btnCompact = document.getElementById('btnViewCompact');

	if (!detailedView || !compactView) return;

	if (mode === 'compact') {
		detailedView.classList.add('hidden');
		compactView.classList.remove('hidden');

		if (btnCards) {
			btnCards.classList.remove('bg-viewlix-red', 'text-white');
			btnCards.classList.add('bg-viewlix-gray', 'text-gray-400');
		}
		if (btnCompact) {
			btnCompact.classList.add('bg-viewlix-red', 'text-white');
			btnCompact.classList.remove('bg-viewlix-gray', 'text-gray-400');
		}
	} else {
		detailedView.classList.remove('hidden');
		compactView.classList.add('hidden');

		if (btnCards) {
			btnCards.classList.add('bg-viewlix-red', 'text-white');
			btnCards.classList.remove('bg-viewlix-gray', 'text-gray-400');
		}
		if (btnCompact) {
			btnCompact.classList.remove('bg-viewlix-red', 'text-white');
			btnCompact.classList.add('bg-viewlix-gray', 'text-gray-400');
		}
	}

	try {
		localStorage.setItem('tvWatchEpisodeViewMode', mode);
	} catch (e) { }
}

function filterEpisodes(query) {
	const q = (query || '').trim().toLowerCase();
	const detailedCards = document.querySelectorAll('#episodesDetailedView .episode-card');
	const compactChips = document.querySelectorAll('#episodesCompactView .episode-chip');
	const emptyNotice = document.getElementById('episodesEmptySearchNotice');
	let visibleCount = 0;

	detailedCards.forEach(card => {
		const searchData = (card.getAttribute('data-search') || '').toLowerCase();
		if (!q || searchData.includes(q)) {
			card.classList.remove('hidden');
			visibleCount++;
		} else {
			card.classList.add('hidden');
		}
	});

	compactChips.forEach(chip => {
		const searchData = (chip.getAttribute('data-search') || '').toLowerCase();
		if (!q || searchData.includes(q)) {
			chip.classList.remove('hidden');
		} else {
			chip.classList.add('hidden');
		}
	});

	if (emptyNotice) {
		if (visibleCount === 0 && q.length > 0) {
			emptyNotice.classList.remove('hidden');
		} else {
			emptyNotice.classList.add('hidden');
		}
	}
}

function switchSeason(seasonNumber, targetUrlTemplate) {
	if (!seasonNumber) return;
	if (targetUrlTemplate) {
		window.location.href = targetUrlTemplate.replace('__SEASON__', seasonNumber);
	}
}

function setWatchTab(tabName) {
	const tabBtns = document.querySelectorAll('.watch-tab-btn');
	const tabPanels = document.querySelectorAll('.watch-tab-panel');

	tabBtns.forEach(btn => {
		if (btn.getAttribute('data-tab') === tabName) {
			btn.classList.add('border-viewlix-red', 'text-white', 'bg-white/5');
			btn.classList.remove('border-transparent', 'text-gray-400', 'hover:text-gray-200');
		} else {
			btn.classList.remove('border-viewlix-red', 'text-white', 'bg-white/5');
			btn.classList.add('border-transparent', 'text-gray-400', 'hover:text-gray-200');
		}
	});

	tabPanels.forEach(panel => {
		if (panel.id === `tab-panel-${tabName}`) {
			panel.classList.remove('hidden');
		} else {
			panel.classList.add('hidden');
		}
	});
}

function shareCurrentEpisode(title, showName) {
	const shareData = {
		title: `${showName} - ${title} | Viewlix`,
		text: `Watch ${showName} on Viewlix!`,
		url: window.location.href
	};

	if (navigator.share) {
		navigator.share(shareData).catch(() => { });
	} else {
		navigator.clipboard.writeText(window.location.href).then(() => {
			showWatchToast('Link copied to clipboard!');
		}).catch(() => {
			showWatchToast('Failed to copy link');
		});
	}
}

function showWatchToast(message) {
	let toast = document.getElementById('watchToast');
	if (!toast) {
		toast = document.createElement('div');
		toast.id = 'watchToast';
		toast.className = 'fixed bottom-20 left-1/2 -translate-x-1/2 z-50 px-4 py-2.5 bg-viewlix-gray/95 backdrop-blur-md border border-viewlix-border text-white text-sm font-medium rounded-xl shadow-2xl transition-all duration-300 pointer-events-none opacity-0 translate-y-2';
		document.body.appendChild(toast);
	}
	toast.textContent = message;
	toast.classList.remove('opacity-0', 'translate-y-2');
	toast.classList.add('opacity-100', 'translate-y-0');

	setTimeout(() => {
		toast.classList.remove('opacity-100', 'translate-y-0');
		toast.classList.add('opacity-0', 'translate-y-2');
	}, 2500);
}

function toggleEpisodeListCollapse() {
	const container = document.getElementById('episodesContentContainer');
	const chevron = document.getElementById('episodeCollapseChevron');
	const label = document.getElementById('episodeCollapseLabel');
	if (!container) return;

	const isCurrentlyHidden = container.classList.contains('hidden');

	if (isCurrentlyHidden) {
		container.classList.remove('hidden');
		if (chevron) chevron.classList.remove('rotate-180');
		if (label) label.textContent = 'Collapse';
		try { localStorage.setItem('tvWatchEpisodesCollapsed', 'false'); } catch (e) { }

		// Scroll to active episode on expand if needed
		setTimeout(() => {
			const scrollPanel = document.getElementById('episodesScrollPanel');
			const activeCard = document.querySelector('#episodesDetailedView .episode-card-active') || document.querySelector('#episodesCompactView .episode-chip.bg-viewlix-red');
			if (activeCard && scrollPanel) {
				const cardTop = activeCard.offsetTop;
				const panelTop = scrollPanel.offsetTop;
				const targetScroll = Math.max(0, cardTop - panelTop - 20);
				scrollPanel.scrollTo({ top: targetScroll, behavior: 'smooth' });
			}
		}, 100);
	} else {
		container.classList.add('hidden');
		if (chevron) chevron.classList.add('rotate-180');
		if (label) label.textContent = 'Expand';
		try { localStorage.setItem('tvWatchEpisodesCollapsed', 'true'); } catch (e) { }
	}
}

document.addEventListener('DOMContentLoaded', function () {
	// Restore view mode preference
	try {
		const savedMode = localStorage.getItem('tvWatchEpisodeViewMode');
		if (savedMode) {
			setEpisodeViewMode(savedMode);
		}
	} catch (e) { }

	// Restore collapse preference (default expanded)
	try {
		const isCollapsed = localStorage.getItem('tvWatchEpisodesCollapsed') === 'true';
		if (isCollapsed) {
			const container = document.getElementById('episodesContentContainer');
			const chevron = document.getElementById('episodeCollapseChevron');
			const label = document.getElementById('episodeCollapseLabel');
			if (container) container.classList.add('hidden');
			if (chevron) chevron.classList.add('rotate-180');
			if (label) label.textContent = 'Expand';
		}
	} catch (e) { }

	// Auto-scroll active episode inside the scroll panel (NOT the whole page)
	setTimeout(() => {
		const scrollPanel = document.getElementById('episodesScrollPanel');
		const activeCard = document.querySelector('#episodesDetailedView .episode-card-active') || document.querySelector('#episodesCompactView .episode-chip.bg-viewlix-red');
		if (activeCard && scrollPanel) {
			const cardTop = activeCard.offsetTop;
			const panelTop = scrollPanel.offsetTop;
			const targetScroll = Math.max(0, cardTop - panelTop - 20);
			scrollPanel.scrollTo({ top: targetScroll, behavior: 'smooth' });
		}
	}, 350);

	// Episode search input listener
	const searchInput = document.getElementById('episodeSearchInput');
	const clearBtn = document.getElementById('episodeSearchClearBtn');
	if (searchInput) {
		searchInput.addEventListener('input', function (e) {
			filterEpisodes(e.target.value);
			if (clearBtn) {
				if (e.target.value.length > 0) {
					clearBtn.classList.remove('hidden');
				} else {
					clearBtn.classList.add('hidden');
				}
			}
		});
	}
	if (clearBtn && searchInput) {
		clearBtn.addEventListener('click', function () {
			searchInput.value = '';
			clearBtn.classList.add('hidden');
			filterEpisodes('');
			searchInput.focus();
		});
	}
});




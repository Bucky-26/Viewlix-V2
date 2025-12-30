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
// VidLink Continue Watching Progress Tracking (moved here)
// Saves to Firebase (authenticated) + localStorage (fallback)
// Relies on global: mediaData
// ============================================================

let lastSavedProgress = 0;
let lastSavedTime = 0;
const SAVE_INTERVAL_SECONDS = 30; // Save to Firebase every 30 seconds

function buildProgressData(watchedTime, duration) {
	const progressData = {
		id: mediaData.id,
		type: mediaData.type,
		title: mediaData.title,
		poster_path: mediaData.posterPath,
		backdrop_path: mediaData.backdropPath,
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

// Listen for VidLink postMessage events
window.addEventListener('message', function(event) {
	// Accept messages from VidLink
	if (event.origin !== 'https://vidlink.pro') return;

	// Handle MEDIA_DATA from VidLink
	if (event.data?.type === 'MEDIA_DATA') {
		const data = event.data.data;
		const mediaKey = String(mediaData.id);

		if (data[mediaKey]) {
			const progress = data[mediaKey].progress;
			if (progress) {
				const timeSinceLastSave = Math.abs(progress.watched - lastSavedTime);

				if (typeof window.saveWatchProgress === 'function') {
					const progressData = buildProgressData(progress.watched, progress.duration);

					if (timeSinceLastSave >= SAVE_INTERVAL_SECONDS) {
						window.saveWatchProgress(mediaData.id, progressData);
						lastSavedTime = progress.watched;
					} else {
						try {
							const allProgress = JSON.parse(localStorage.getItem('vidLinkProgress') || '{}');
							allProgress[mediaKey] = progressData;
							localStorage.setItem('vidLinkProgress', JSON.stringify(allProgress));
						} catch (e) {}
					}
				}
			}
		}
		return;
	}

	// Handle manual progress tracking (fallback)
	try {
		const data = typeof event.data === 'string' ? JSON.parse(event.data) : event.data;

		if (data.currentTime !== undefined && data.duration !== undefined) {
			const watchedTime = data.currentTime;
			const duration = data.duration;

			if (duration > 0) {
				const timeSinceLastSave = Math.abs(watchedTime - lastSavedTime);
				const progressData = buildProgressData(watchedTime, duration);

				if (typeof window.saveWatchProgress === 'function') {
					if (timeSinceLastSave >= SAVE_INTERVAL_SECONDS) {
						window.saveWatchProgress(mediaData.id, progressData);
						lastSavedTime = watchedTime;
					} else {
						try {
							const allProgress = JSON.parse(localStorage.getItem('vidLinkProgress') || '{}');
							allProgress[String(mediaData.id)] = progressData;
							localStorage.setItem('vidLinkProgress', JSON.stringify(allProgress));
						} catch (e) {}
					}
				}
			}
		}
	} catch (error) {
		// ignore
	}
});

// Save progress when leaving the page
window.addEventListener('beforeunload', function() {
	try {
		const allProgress = JSON.parse(localStorage.getItem('vidLinkProgress') || '{}');
		const mediaKey = String(mediaData.id);
		const currentMedia = allProgress[mediaKey];

		if (currentMedia?.progress && typeof window.saveWatchProgress === 'function') {
			window.saveWatchProgress(mediaData.id, currentMedia);
		}
	} catch (e) {}
});

// Load saved progress on page load
document.addEventListener('DOMContentLoaded', async function() {
	if (typeof window.getWatchProgress === 'function') {
		try {
			const saved = await window.getWatchProgress(mediaData.id);

			if (saved) {
				if (mediaData.type === 'movie') {
					if (saved.progress?.watched > 0) {
						lastSavedProgress = (saved.progress.watched / saved.progress.duration) * 100;
						lastSavedTime = saved.progress.watched;
						console.log(`📺 Resuming from ${Math.floor(saved.progress.watched)}s`);
					}
				} else {
					const seasonKey = `s${mediaData.season}e${mediaData.episode}`;
					if (saved.show_progress?.[seasonKey]?.progress?.watched > 0) {
						const epProgress = saved.show_progress[seasonKey].progress;
						lastSavedProgress = (epProgress.watched / epProgress.duration) * 100;
						lastSavedTime = epProgress.watched;
						console.log(`📺 Resuming S${mediaData.season}E${mediaData.episode} from ${Math.floor(epProgress.watched)}s`);
					}
				}
			}
		} catch (e) {
			// ignore
		}
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

	return [{ name: 'Default', embed: defaultEmbed }];
}

const sources = buildSources();
const sourceSelect = document.getElementById('sourceSelect');
const openSourceBtn = document.getElementById('openSourceBtn');
const playerIframe = document.getElementById('playerIframe') || document.querySelector('.video-container iframe');

function applySelectedSource() {
	if (!playerIframe || !sourceSelect) return;
	const idx = parseInt(sourceSelect.value || 0);
	const src = sources[idx]?.embed || defaultEmbed;
	if (playerIframe.getAttribute('src') !== src) {
		playerIframe.setAttribute('src', src);
	}

	try {
		const lower = String(src || '').toLowerCase();
		const isViewlix = lower.includes('player.viewlix.site') || lower.includes('viewlix.site') || lower.includes('viewlix');
		if (isViewlix) {
			playerIframe.setAttribute('scrolling', 'no');
			playerIframe.style.overflow = 'hidden';
			playerIframe.classList.add('hide-iframe-scrollbar');
		} else {
			playerIframe.removeAttribute('scrolling');
			playerIframe.style.overflow = '';
			playerIframe.classList.remove('hide-iframe-scrollbar');
		}
	} catch (e) {}

	try {
		const key = `preferredSource_${mediaData.type}_${mediaData.id}`;
		localStorage.setItem(key, String(idx));
	} catch (e) {}

	if (openSourceBtn) {
		openSourceBtn.classList.remove('hidden');
	}
}

function populateSourceOptions() {
	if (!sourceSelect) return;
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

if (sourceSelect) sourceSelect.addEventListener('change', applySelectedSource);
if (openSourceBtn) {
	openSourceBtn.addEventListener('click', function() {
		const idx = parseInt(sourceSelect.value || 0);
		const url = sources[idx]?.embed || defaultEmbed;
		window.open(url, '_blank');
	});
}

document.addEventListener('DOMContentLoaded', populateSourceOptions);


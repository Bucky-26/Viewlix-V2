// tv-show-detail.js
// DOM-only helpers for TV show detail page.
// Assumes a global boolean `isFavorited` exists (defined inline by Blade).

function updateFavoriteButton() {
    const icon = document.getElementById('favoriteIcon');
    const text = document.getElementById('favoriteBtnText');
    if (!icon || !text) return;

    // Read global isFavorited (may be declared with `let isFavorited = false;` in blade)
    const fav = (typeof window.isFavorited !== 'undefined') ? window.isFavorited : (typeof isFavorited !== 'undefined' ? isFavorited : false);

    if (fav) {
        icon.setAttribute('fill', 'currentColor');
        icon.classList.add('text-viewlix-red');
        text.textContent = 'Remove from Favorites';
    } else {
        icon.setAttribute('fill', 'none');
        icon.classList.remove('text-viewlix-red');
        text.textContent = 'Add to Favorites';
    }
}

// Expose on window so Blade inline code can call it directly
window.updateFavoriteButton = updateFavoriteButton;

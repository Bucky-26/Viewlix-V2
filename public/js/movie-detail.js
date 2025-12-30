// movie-detail.js
// DOM helper for movie detail page. Reads global `isFavorited` and updates the favorite button UI.

function updateFavoriteButton() {
    const icon = document.getElementById('favoriteIcon');
    const text = document.getElementById('favoriteBtnText');
    if (!icon || !text) return;

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

window.updateFavoriteButton = updateFavoriteButton;

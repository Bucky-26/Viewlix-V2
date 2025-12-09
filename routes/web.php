<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\TvShowController;
use App\Http\Controllers\AnimeController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WatchHistoryController;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');

// Movies (browsing is public)
Route::get('/movies', [MovieController::class, 'index'])->name('movies');
Route::get('/movie/{id}', [MovieController::class, 'show'])->name('movie.show');

// TV Shows (browsing is public)
Route::get('/tv-shows', [TvShowController::class, 'index'])->name('tv-shows');
Route::get('/tv-show/{id}', [TvShowController::class, 'show'])->name('tv-show.show');
Route::get('/tv-show/{id}/season/{season}', [TvShowController::class, 'season'])->name('tv-show.season');

// Anime (browsing is public)
Route::get('/anime', [AnimeController::class, 'index'])->name('anime');
Route::get('/anime/{id}', [AnimeController::class, 'show'])->name('anime.show');

// Auth routes
require __DIR__.'/auth.php';

// Watch routes (require authentication)
Route::middleware(['auth', 'verified'])->group(function () {
    // Watch content
    Route::get('/movie/{id}/watch', [MovieController::class, 'watch'])->name('movie.watch');
    Route::get('/tv-show/{id}/watch/{season?}/{episode?}', [TvShowController::class, 'watch'])->name('tv-show.watch');
    Route::get('/anime/{id}/watch/{season?}/{episode?}', [AnimeController::class, 'watch'])->name('anime.watch');
    
    // Dashboard
    Route::get('/dashboard', function () {
        return redirect()->route('home');
    })->name('dashboard');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Account page
    Route::get('/account', function () {
        return view('pages.account');
    })->name('account');
    
    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites');
    Route::post('/favorites/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/favorites/check', [FavoriteController::class, 'check'])->name('favorites.check');
    Route::delete('/favorites', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    
    // Watch History
    Route::get('/watch-history', [WatchHistoryController::class, 'index'])->name('watch-history');
    Route::post('/watch-history', [WatchHistoryController::class, 'store'])->name('watch-history.store');
    Route::get('/watch-history/progress', [WatchHistoryController::class, 'getProgress'])->name('watch-history.progress');
    Route::get('/watch-history/continue', [WatchHistoryController::class, 'continueWatching'])->name('watch-history.continue');
    Route::delete('/watch-history', [WatchHistoryController::class, 'destroy'])->name('watch-history.destroy');
    Route::post('/watch-history/clear', [WatchHistoryController::class, 'clear'])->name('watch-history.clear');
});

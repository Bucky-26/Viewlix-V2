<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\TvShowController;
use App\Http\Controllers\AnimeController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WatchHistoryController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\MobileAppController;
use App\Models\MobileApp;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;
use RalphJSmit\Laravel\SEO\Support\SEOData;

// Google OAuth Routes (public)
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');
// Search suggestions (autocomplete)
Route::get('/search/suggest', [HomeController::class, 'suggest'])->name('search.suggest');

// Filipino / Tagalog hub (movies + series)
Route::get('/filipino', [HomeController::class, 'filipino'])->name('filipino');

// Movies (browsing is public)
Route::get('/movies', [MovieController::class, 'index'])->name('movies');
// Use slug format: {tmdbId}-{slugified-title}
Route::get('/movie/{slug}', [MovieController::class, 'show'])->name('movie.show');

// TV Shows (browsing is public)
Route::get('/tv-shows', [TvShowController::class, 'index'])->name('tv-shows');
// Use slug format: {tmdbId}-{slugified-name}
Route::get('/tv-show/{slug}', [TvShowController::class, 'show'])->name('tv-show.show');
Route::get('/tv-show/{slug}/season/{season}', [TvShowController::class, 'season'])->name('tv-show.season');
// Terms and Privacy Pages
Route::get('/terms', function () {
    return view('pages.terms', [
        'SEOData' => new SEOData(
            title: 'Terms of Service - Viewlix',
            description: 'Read Viewlix Terms of Service. Understand the terms and conditions for using our streaming platform.',
            url: route('terms'),
        ),
    ]);
})->name('terms');

Route::get('/privacy', function () {
    return view('pages.privacy', [
        'SEOData' => new SEOData(
            title: 'Privacy Policy - Viewlix',
            description: 'Viewlix Privacy Policy. Learn how we collect, use, and protect your personal information.',
            url: route('privacy'),
        ),
    ]);
})->name('privacy');
// About page
Route::get('/about', function () {
    return view('pages.about', [
        'SEOData' => new SEOData(
            title: 'About Viewlix',
            description: 'Learn about Viewlix — watch movies and TV shows for free without ads or subscription.',
            url: route('about'),
        ),
    ]);
})->name('about');
// Anime (browsing is public)
Route::get('/anime', [AnimeController::class, 'index'])->name('anime');
Route::get('/anime/{id}', [AnimeController::class, 'show'])->name('anime.show');

// Download app page
Route::get('/download', function () {
    $androidApp = MobileApp::android();

    return view('pages.download', [
        'SEOData' => new SEOData(
            title: 'Download Viewlix App - Stream Anywhere',
            description: 'Download the Viewlix Android app. Stream your favorite movies, TV shows, and anime anywhere, anytime. Free to download and use!',
            url: route('download'),
        ),
        'androidApp' => $androidApp,
    ]);
})->name('download');

// Direct APK download (public)
Route::get('/download/android', [\App\Http\Controllers\Admin\MobileAppController::class, 'downloadAndroid'])->name('download.android');

// Download endpoints (public, no authentication required)
Route::get('/api/downloads/movie/{tmdbId}', [DownloadController::class, 'getMovieDownloads'])->name('downloads.movie');
Route::get('/api/downloads/tv/{tmdbId}', [DownloadController::class, 'getTvDownloads'])->name('downloads.tv');
// Proxy route to stream remote files through our server with the required Referer header
Route::get('/download/proxy', [DownloadController::class, 'proxyFile'])->name('download.proxy');

// Auth routes
require __DIR__.'/auth.php';

// Watch routes (require authentication)
Route::middleware(['auth', 'verified'])->group(function () {
    // Watch content
    Route::get('/movie/{slug}/watch', [MovieController::class, 'watch'])->name('movie.watch');
    Route::get('/tv-show/{slug}/watch/{season?}/{episode?}', [TvShowController::class, 'watch'])->name('tv-show.watch');
    Route::get('/anime/{id}/watch/{season?}/{episode?}', [AnimeController::class, 'watch'])->name('anime.watch');
    
    // Dashboard
    Route::get('/dashboard', function () {
        return redirect()->route('home');
    })->name('dashboard');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Avatar upload
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    
    // Account page
    Route::get('/account', function () {
        return view('pages.account');
    })->name('account');
    
    // Google Account Linking
    Route::get('/auth/google/link', [GoogleController::class, 'link'])->name('auth.google.link');
    Route::get('/auth/google/link/callback', [GoogleController::class, 'linkCallback'])->name('auth.google.link.callback');
    Route::post('/auth/google/unlink', [GoogleController::class, 'unlink'])->name('auth.google.unlink');
    
    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites');
    Route::post('/favorites/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    Route::get('/favorites/check', [FavoriteController::class, 'check'])->name('favorites.check');
    Route::delete('/favorites', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    
    // Watch History (Firebase-powered, just renders the page)
    Route::get('/watch-history', [WatchHistoryController::class, 'index'])->name('watch-history');
    
    // Firebase token endpoint (for web session auth)
    Route::get('/firebase-token', function (\App\Services\FirebaseService $firebase) {
        if (!$firebase->isConfigured()) {
            return response()->json(['error' => 'Firebase not configured'], 503);
        }
        
        $token = $firebase->createCustomToken();
        
        if (!$token) {
            return response()->json(['error' => 'Failed to create token'], 500);
        }
        
        return response()->json(['token' => $token]);
    })->name('firebase.token');
});

// Public endpoint to get user avatar by user id
Route::get('/avatar/{user}', function (\App\Models\User $user) {
    if ($user->avatar) {
        return redirect($user->avatar);
    }
    // Return a default avatar image if not set
    return redirect(asset('images/default-avatar.png'));
})->name('avatar.public');

// Sitemap and Robots
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap.index');
Route::get('/sitemap-main.xml', [SitemapController::class, 'main'])->name('sitemap.main');
Route::get('/sitemap-movies.xml', [SitemapController::class, 'movies'])->name('sitemap.movies');
Route::get('/sitemap-tv-shows.xml', [SitemapController::class, 'tvShows'])->name('sitemap.tv-shows');
Route::get('/sitemap-anime.xml', [SitemapController::class, 'anime'])->name('sitemap.anime');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Admin Routes
Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
    Route::get('/analytics/data', [AdminController::class, 'analyticsData'])->name('analytics.data');
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::post('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::post('/users/{user}/verify', [AdminController::class, 'verifyUser'])->name('users.verify');
    Route::post('/users/{user}/resend-verification', [AdminController::class, 'resendVerification'])->name('users.resend-verification');
    
    // Admin: view a single user's favorites and Firebase watch history
    Route::get('/users/{user}/favorites', [AdminController::class, 'userFavorites'])->name('users.favorites');
    Route::get('/users/{user}/watch-history', [AdminController::class, 'userWatchHistory'])->name('users.watch-history');
    
    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications');
    Route::get('/notifications/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('notifications.create');
    Route::post('/notifications/send', [\App\Http\Controllers\Admin\NotificationController::class, 'send'])->name('notifications.send');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('notifications.destroy');

    // User management: sessions, session details and session controls
    Route::get('/users-management', [UserManagementController::class, 'index'])->name('users-management.index');
    Route::get('/users-management/{user}', [UserManagementController::class, 'show'])->name('users-management.show');
    Route::post('/users-management/{user}/logout-all', [UserManagementController::class, 'logoutAllSessions'])->name('users-management.logout-all');
    Route::post('/users-management/{user}/{session}/logout', [UserManagementController::class, 'logoutSession'])->name('users-management.logout-session');

    // Mobile App management (Android)
    Route::get('/mobile-app', [MobileAppController::class, 'index'])->name('mobile-app.index');
    Route::post('/mobile-app/{platform}', [MobileAppController::class, 'store'])->name('mobile-app.store');
    Route::get('/mobile-app/{platform}/download', [MobileAppController::class, 'download'])->name('mobile-app.download');

    // Banner management
    Route::resource('banners', \App\Http\Controllers\Admin\BannerController::class);
    Route::post('/banners/{banner}/toggle', [\App\Http\Controllers\Admin\BannerController::class, 'toggle'])->name('banners.toggle');
});

// Developer Portfolio (static page)
Route::get('/developer/profile/isoy', function () {
    return view('developer.profile.isoy');
})->name('developer.profile.isoy');

// Logo endpoint (serves the SVG logo).
// NOTE: This endpoint is available at /logo.png but returns SVG content (Content-Type: image/svg+xml).
// If you need a true raster PNG, we can generate and serve a PNG either by pre-generating
// `public/logo.png` or using Imagick/Intervention on the server.
Route::get('/logo.png', function () {
    $svg = '<svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M4 4h4l4 12 4-12h4L12 22 4 4z"/></svg>';

    return response($svg, 200)
        ->header('Content-Type', 'image/svg+xml')
        ->header('Content-Disposition', 'inline; filename="logo.png"');
})->name('logo');

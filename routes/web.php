<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\TvShowController;
use App\Http\Controllers\AnimeController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WatchHistoryController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Developer\DeveloperController;
use App\Http\Controllers\Developer\DeveloperAdminController;
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

// Download Page
Route::get('/download', function () {
    $iosApp = \App\Models\MobileApp::ios();
    $androidApp = \App\Models\MobileApp::android();
    return view('pages.download', compact('iosApp', 'androidApp'));
})->name('download');

// Public App Download Route
Route::get('/app/download/android', [\App\Http\Controllers\Admin\MobileAppController::class, 'downloadAndroid'])->name('app.download');

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
// Anime (browsing is public)
Route::get('/anime', [AnimeController::class, 'index'])->name('anime');
Route::get('/anime/{id}', [AnimeController::class, 'show'])->name('anime.show');

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
    
    // User Management with Sessions
    Route::get('/users-management', [\App\Http\Controllers\Admin\UserManagementController::class, 'index'])->name('users-management.index');
    Route::get('/users-management/{user}', [\App\Http\Controllers\Admin\UserManagementController::class, 'show'])->name('users-management.show');
    Route::post('/users-management/{user}/logout-session/{session}', [\App\Http\Controllers\Admin\UserManagementController::class, 'logoutSession'])->name('users-management.logout-session');
    Route::post('/users-management/{user}/logout-all', [\App\Http\Controllers\Admin\UserManagementController::class, 'logoutAllSessions'])->name('users-management.logout-all');
    Route::delete('/users-management/{user}', [\App\Http\Controllers\Admin\UserManagementController::class, 'delete'])->name('users-management.delete');
    
    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications');
    Route::get('/notifications/create', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('notifications.create');
    Route::post('/notifications/send', [\App\Http\Controllers\Admin\NotificationController::class, 'send'])->name('notifications.send');
    Route::delete('/notifications/{id}', [\App\Http\Controllers\Admin\NotificationController::class, 'destroy'])->name('notifications.destroy');
    
    // Mobile App Management
    Route::get('/mobile-app', [\App\Http\Controllers\Admin\MobileAppController::class, 'index'])->name('mobile-app.index');
    Route::post('/mobile-app/{platform}', [\App\Http\Controllers\Admin\MobileAppController::class, 'store'])->name('mobile-app.store');
    Route::get('/mobile-app/{platform}/download', [\App\Http\Controllers\Admin\MobileAppController::class, 'download'])->name('mobile-app.download');
});

// Developer Routes (Public portfolio and blog)
Route::prefix('developer')->name('developer.')->group(function () {
    // Portfolio and blog are public
    Route::get('/', [DeveloperController::class, 'portfolio'])->name('portfolio');
    Route::get('/blogs', [DeveloperController::class, 'blogIndex'])->name('blogs');
    Route::get('/blog/{slug}', [DeveloperController::class, 'showBlog'])->name('blog.show');
    
    // Developer admin panel (requires auth and admin privileges)
    Route::middleware(['auth', 'verified', 'admin'])->prefix('adminpane')->name('admin.')->group(function () {
        Route::get('/', [DeveloperAdminController::class, 'dashboard'])->name('dashboard');
        
        // Blog Post Management
        Route::get('/posts', [DeveloperAdminController::class, 'index'])->name('posts');
        Route::get('/posts/create', [DeveloperAdminController::class, 'create'])->name('posts.create');
        Route::post('/posts', [DeveloperAdminController::class, 'store'])->name('posts.store');
        Route::get('/posts/{blogPost}/edit', [DeveloperAdminController::class, 'edit'])->name('posts.edit');
        Route::put('/posts/{blogPost}', [DeveloperAdminController::class, 'update'])->name('posts.update');
        Route::delete('/posts/{blogPost}', [DeveloperAdminController::class, 'destroy'])->name('posts.destroy');
    });
});

<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\AnimeController as V1AnimeController;
use App\Http\Controllers\Api\V1\AppController as V1AppController;
use App\Http\Controllers\Api\V1\AuthController as V1AuthController;
use App\Http\Controllers\Api\V1\FavoriteController as V1FavoriteController;
use App\Http\Controllers\Api\V1\HomeController as V1HomeController;
use App\Http\Controllers\Api\V1\MovieController as V1MovieController;
use App\Http\Controllers\Api\V1\SearchController as V1SearchController;
use App\Http\Controllers\Api\V1\SupportController as V1SupportController;
use App\Http\Controllers\Api\V1\TvShowController as V1TvShowController;
use App\Http\Controllers\Api\V1\WatchHistoryController as V1WatchHistoryController;
use App\Services\TMDBService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Preview API endpoint
Route::get('/preview/{type}/{id}', function (string $type, int $id, TMDBService $tmdb) {
    try {
        switch ($type) {
            case 'movie':
                $data = $tmdb->getMovieDetails($id);
                break;
            case 'tv':
            case 'anime':
                $data = $tmdb->getTvShowDetails($id);
                break;
            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }
        
        if (!$data) {
            return response()->json(['error' => 'Not found'], 404);
        }
        
        return response()->json($data);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to fetch data: ' . $e->getMessage()], 500);
    }
});

// Movies list (JSON)
Route::get('/movies', function (\Illuminate\Http\Request $request, TMDBService $tmdb) {
    $filter = $request->get('filter', 'popular');
    $page = (int) $request->get('page', 1);

    // Performance options
    $minimal = (bool) $request->get('minimal', false);
    $includeLogos = (bool) $request->get('include_logos', false);
    $posterSize = $request->get('poster_size', 'w342');
    $backdropSize = $request->get('backdrop_size', 'w780');
    $logoSize = $request->get('logo_size', 'w280');
    try {
        // Attempt to return cached payload immediately when available to
        // avoid waiting on TMDB for cold-cache requests.
        $cacheKey = match ($filter) {
            'top_rated' => "movies.top_rated.{$page}",
            'now_playing' => "movies.now_playing.{$page}",
            'upcoming' => "movies.upcoming.{$page}",
            default => "movies.popular.{$page}",
        };

        if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
            $movies = \Illuminate\Support\Facades\Cache::get($cacheKey);
        } else {
            $movies = match ($filter) {
                'top_rated' => $tmdb->getTopRatedMovies($page),
                'now_playing' => $tmdb->getNowPlayingMovies($page),
                'upcoming' => $tmdb->getUpcomingMovies($page),
                default => $tmdb->getPopularMovies($page),
            };
        }

        if (!empty($movies['results']) && is_array($movies['results'])) {
            // Map results to either full TMDB item (default) or a compact payload
            $movies['results'] = array_map(function ($item) use ($tmdb, $minimal, $includeLogos, $posterSize, $backdropSize, $logoSize) {
                // Provide proxied poster/backdrop URLs with requested sizes
                $poster = $tmdb->getImageUrl($item['poster_path'] ?? null, $posterSize, true);
                $backdrop = $tmdb->getImageUrl($item['backdrop_path'] ?? null, $backdropSize, true);

                if ($minimal) {
                    $compact = [
                        'id' => $item['id'] ?? null,
                        'title' => $item['title'] ?? $item['name'] ?? null,
                        'overview' => $item['overview'] ?? '',
                        'release_date' => $item['release_date'] ?? $item['first_air_date'] ?? null,
                        'vote_average' => $item['vote_average'] ?? 0,
                        'poster_url' => $poster,
                        'backdrop_url' => $backdrop,
                    ];

                    // Populate logo_url when requested. Call TMDB helper so
                    // callers requesting logos receive a usable proxied URL.
                    if ($includeLogos && isset($item['id'])) {
                        $compact['logo_url'] = $tmdb->getMovieLogoUrl((int) $item['id'], 'US', $logoSize, true);
                    } else {
                        $compact['logo_url'] = '';
                    }

                    return $compact;
                }

                // Default: enrich the original payload
                $item['poster_url'] = $poster;
                $item['backdrop_url'] = $backdrop;
                $item['logo_url'] = $includeLogos && isset($item['id']) ? $tmdb->getMovieLogoUrl((int) $item['id'], 'US', 'w500', true) : '';
                return $item;
            }, $movies['results']);
        }

        return response()->json($movies);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to fetch movies: ' . $e->getMessage()], 500);
    }
});

// TV Shows list (JSON)
Route::get('/tv-shows', function (\Illuminate\Http\Request $request, TMDBService $tmdb) {
    $filter = $request->get('filter', 'popular');
    $page = (int) $request->get('page', 1);

    // Performance options
    $minimal = (bool) $request->get('minimal', false);
    $includeLogos = (bool) $request->get('include_logos', false);
    $posterSize = $request->get('poster_size', 'w342');
    $backdropSize = $request->get('backdrop_size', 'w780');

    try {
        // Try to return a cached payload immediately if present. This
        // avoids waiting on TMDB for cold-cache requests when a cached
        // copy exists. Keys mirror those used by TMDBService.
        $cacheKey = match ($filter) {
            'top_rated' => "tv.top_rated.{$page}",
            'on_the_air' => "tv.on_the_air.{$page}",
            'airing_today' => "tv.airing_today.{$page}",
            default => "tv.popular.{$page}",
        };

        if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
            $tv = \Illuminate\Support\Facades\Cache::get($cacheKey);
        } else {
            $tv = match ($filter) {
                'top_rated' => $tmdb->getTopRatedTvShows($page),
                'on_the_air' => $tmdb->getOnTheAirTvShows($page),
                'airing_today' => $tmdb->getAiringTodayTvShows($page),
                default => $tmdb->getPopularTvShows($page),
            };
        }

        if (!empty($tv['results']) && is_array($tv['results'])) {
            $tv['results'] = array_map(function ($item) use ($tmdb, $minimal, $includeLogos, $posterSize, $backdropSize) {
                $poster = $tmdb->getImageUrl($item['poster_path'] ?? null, $posterSize, true);
                $backdrop = $tmdb->getImageUrl($item['backdrop_path'] ?? null, $backdropSize, true);

                if ($minimal) {
                    $compact = [
                        'id' => $item['id'] ?? null,
                        'title' => $item['title'] ?? $item['name'] ?? null,
                        'overview' => $item['overview'] ?? '',
                        'first_air_date' => $item['first_air_date'] ?? null,
                        'vote_average' => $item['vote_average'] ?? 0,
                        'poster_url' => $poster,
                        'backdrop_url' => $backdrop,
                    ];

                    if ($includeLogos && isset($item['id'])) {
                        $compact['logo_url'] = $tmdb->getTvLogoUrl((int) $item['id'], 'US', 'w280', true);
                    } else {
                        $compact['logo_url'] = '';
                    }

                    return $compact;
                }

                $item['poster_url'] = $poster;
                $item['backdrop_url'] = $backdrop;
                $item['logo_url'] = $includeLogos && isset($item['id']) ? $tmdb->getTvLogoUrl((int) $item['id'], 'US', 'w500', true) : '';
                return $item;
            }, $tv['results']);
        }

        return response()->json($tv);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to fetch TV shows: ' . $e->getMessage()], 500);
    }
});

// Single TV Show details (JSON)
Route::get('/tv-shows/{id}', function (int $id, TMDBService $tmdb) {
    try {
        $tvShow = $tmdb->getTvShowDetails($id);
        if (! $tvShow) return response()->json(['error' => 'Not found'], 404);
        $credits = $tmdb->getTvCredits($id);
        $similar = $tmdb->getSimilarTvShows($id);

        // Add proxied image URLs and logo
        $tvShow['poster_url'] = $tmdb->getImageUrl($tvShow['poster_path'] ?? null, 'w500', true);
        $tvShow['backdrop_url'] = $tmdb->getImageUrl($tvShow['backdrop_path'] ?? null, 'w1280', true);
        $tvShow['logo_url'] = $tmdb->getTvLogoUrl($id, 'US', 'w500', true);

        $similarResults = [];
        if (!empty($similar['results']) && is_array($similar['results'])) {
            $similarResults = array_map(function ($item) use ($tmdb) {
                $item['poster_url'] = $tmdb->getImageUrl($item['poster_path'] ?? null, 'w500', true);
                $item['backdrop_url'] = $tmdb->getImageUrl($item['backdrop_path'] ?? null, 'w1280', true);
                $item['logo_url'] = isset($item['id']) ? $tmdb->getTvLogoUrl((int) $item['id'], 'US', 'w500', true) : '';
                return $item;
            }, $similar['results']);
        }

        return response()->json(array_merge($tvShow, [
            'credits' => $credits ?? [],
            'similar' => $similarResults,
        ]));
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to fetch TV show: ' . $e->getMessage()], 500);
    }
});

// Anime list (JSON)
Route::get('/anime', function (\Illuminate\Http\Request $request, TMDBService $tmdb) {
    $filter = $request->get('filter', 'popular');
    $page = (int) $request->get('page', 1);

    // Performance options
    $minimal = (bool) $request->get('minimal', false);
    $includeLogos = (bool) $request->get('include_logos', false);
    $posterSize = $request->get('poster_size', 'w342');
    $backdropSize = $request->get('backdrop_size', 'w780');

    try {
        $cacheKey = match ($filter) {
            'top_rated' => "anime.top_rated.{$page}",
            'airing' => "anime.airing.{$page}",
            default => "anime.popular.{$page}",
        };

        if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
            $anime = \Illuminate\Support\Facades\Cache::get($cacheKey);
        } else {
            $anime = match ($filter) {
                'top_rated' => $tmdb->getTopRatedAnime($page),
                'airing' => $tmdb->getAiringAnime($page),
                default => $tmdb->getPopularAnime($page),
            };
        }

        if (!empty($anime['results']) && is_array($anime['results'])) {
            $anime['results'] = array_map(function ($item) use ($tmdb, $minimal, $includeLogos, $posterSize, $backdropSize) {
                $poster = $tmdb->getImageUrl($item['poster_path'] ?? null, $posterSize, true);
                $backdrop = $tmdb->getImageUrl($item['backdrop_path'] ?? null, $backdropSize, true);

                if ($minimal) {
                    $compact = [
                        'id' => $item['id'] ?? null,
                        'title' => $item['title'] ?? $item['name'] ?? null,
                        'overview' => $item['overview'] ?? '',
                        'first_air_date' => $item['first_air_date'] ?? null,
                        'vote_average' => $item['vote_average'] ?? 0,
                        'poster_url' => $poster,
                        'backdrop_url' => $backdrop,
                    ];

                    if ($includeLogos && isset($item['id'])) {
                        $compact['logo_url'] = $tmdb->getTvLogoUrl((int) $item['id'], 'US', 'w280', true);
                    } else {
                        $compact['logo_url'] = '';
                    }

                    return $compact;
                }

                $item['poster_url'] = $poster;
                $item['backdrop_url'] = $backdrop;
                $item['logo_url'] = $includeLogos && isset($item['id']) ? $tmdb->getTvLogoUrl((int) $item['id'], 'US', 'w500', true) : '';
                return $item;
            }, $anime['results']);
        }

        return response()->json($anime);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to fetch anime: ' . $e->getMessage()], 500);
    }
});

// Single anime details (JSON)
Route::get('/anime/{id}', function (int $id, TMDBService $tmdb) {
    try {
        $anime = $tmdb->getAnimeDetails($id);
        if (! $anime) return response()->json(['error' => 'Not found'], 404);
        $similar = $tmdb->getSimilarTvShows($id);

        // Add proxied image URLs and logo
        $anime['poster_url'] = $tmdb->getImageUrl($anime['poster_path'] ?? null, 'w500', true);
        $anime['backdrop_url'] = $tmdb->getImageUrl($anime['backdrop_path'] ?? null, 'w1280', true);
        $anime['logo_url'] = $tmdb->getTvLogoUrl($id, 'US', 'w500', true);

        $similarResults = [];
        if (!empty($similar['results']) && is_array($similar['results'])) {
            $similarResults = array_map(function ($item) use ($tmdb) {
                $item['poster_url'] = $tmdb->getImageUrl($item['poster_path'] ?? null, 'w500', true);
                $item['backdrop_url'] = $tmdb->getImageUrl($item['backdrop_path'] ?? null, 'w1280', true);
                $item['logo_url'] = isset($item['id']) ? $tmdb->getTvLogoUrl((int) $item['id'], 'US', 'w500', true) : '';
                return $item;
            }, $similar['results']);
        }

        return response()->json(array_merge($anime, [
            'similar' => $similarResults,
        ]));
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to fetch anime: ' . $e->getMessage()], 500);
    }
});

// Return minimal app configuration (for frontend JS to initialize SDKs)
Route::get('/app-config', function () {
    $fb = config('services.firebase', []);

    // Only expose keys safe for frontend initialization
    $firebaseConfig = [
        'apiKey' => $fb['api_key'] ?? null,
        'authDomain' => $fb['auth_domain'] ?? null,
        'databaseURL' => $fb['database_url'] ?? null,
        'projectId' => $fb['project_id'] ?? null,
        'storageBucket' => $fb['storage_bucket'] ?? null,
        'messagingSenderId' => $fb['messaging_sender_id'] ?? null,
        'appId' => $fb['app_id'] ?? null,
        'measurementId' => $fb['measurement_id'] ?? null,
    ];

    return response()->json([
        'firebase' => [
            'config' => $firebaseConfig,
            'vapidKey' => $fb['vapid_key'] ?? null,
        ],
    ]);
});

// API route to request a Firebase custom token for the currently authenticated user
// Supports web session authentication as well as Sanctum token authentication
Route::middleware(['web'])->get('/firebase-token', function (Request $request, \App\Services\FirebaseService $firebase) {
    $user = $request->user('sanctum') ?? $request->user('web') ?? \Illuminate\Support\Facades\Auth::user();

    if (!$user) {
        return response()->json(['error' => 'Unauthenticated.'], 401);
    }

    $isLocalRequest = app()->environment('local') || in_array($request->ip(), ['127.0.0.1', '::1'], true);

    if (! $firebase->isConfigured()) {
        $payload = ['error' => 'Firebase not configured'];
        if ($isLocalRequest) {
            $payload['debug'] = [
                'credentials_configured' => \App\Services\FirebaseService::getCredentials() !== null,
                'config_services_firebase' => array_intersect_key(config('services.firebase', []), array_flip(['project_id','database_url','api_key','messaging_sender_id','client_email']))
            ];
        }

        return response()->json($payload, 503);
    }

    $token = $firebase->createCustomToken($user);

    if (! $token) {
        $payload = ['error' => 'Failed to create token'];

        if ($isLocalRequest) {
            $payload['debug'] = [
                'authenticated' => true,
                'user' => $user->only(['id','email','firebase_uuid']),
            ];
        }

        return response()->json($payload, 500);
    }

    return response()->json(['token' => $token]);
});

// Watch Progress Endpoints (backed by Firebase Realtime Database and SQL)
Route::middleware(['web'])->group(function () {
    Route::post('/watch-progress', [\App\Http\Controllers\WatchProgressController::class, 'save'])->name('api.watch-progress.save');
    Route::get('/watch-progress/all', [\App\Http\Controllers\WatchProgressController::class, 'all'])->name('api.watch-progress.all');
    Route::get('/watch-progress/{mediaId}', [\App\Http\Controllers\WatchProgressController::class, 'get'])->name('api.watch-progress.get');
    Route::delete('/watch-progress/{mediaId}', [\App\Http\Controllers\WatchProgressController::class, 'destroy'])->name('api.watch-progress.destroy');
    Route::delete('/watch-progress', [\App\Http\Controllers\WatchProgressController::class, 'clear'])->name('api.watch-progress.clear');
});

// Development-only helper: create a Firebase custom token for user id 1
// This route is intentionally only active in the local environment to aid testing.
Route::get('/firebase-token-debug', function (\App\Services\FirebaseService $firebase) {
    // Allow in local environment or when request originates from localhost (127.0.0.1 / ::1)
    $isLocalIp = in_array(request()->ip(), ['127.0.0.1', '::1'], true);
    if (! (app()->environment('local') || $isLocalIp)) {
        return response()->json(['error' => 'Not available'], 403);
    }

    if (! $firebase->isConfigured()) {
        return response()->json(['error' => 'Firebase not configured'], 503);
    }

    $user = \App\Models\User::find(1);
    if (! $user) {
        return response()->json(['error' => 'User not found (id 1)'], 404);
    }

    try {
        $auth = $firebase->getAuth();
        if (! $auth) {
            return response()->json(['error' => 'Firebase auth not available'], 503);
        }

        $firebaseUuid = $user->getFirebaseUuid();
        $customToken = $auth->createCustomToken($firebaseUuid, [
            'email' => $user->email,
            'name' => $user->name,
        ]);

        return response()->json(['token' => $customToken->toString()]);
    } catch (\Throwable $e) {
        \Log::error('Firebase debug token generation failed: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to create token'], 500);
    }
});

// Local-only debug: show whether the current request is authenticated and session cookie presence
Route::get('/debug-session', function () {
    // Only allow from local environment or localhost IPs
    $isLocalIp = in_array(request()->ip(), ['127.0.0.1', '::1'], true);
    if (! (app()->environment('local') || $isLocalIp)) {
        return response()->json(['error' => 'Not available'], 403);
    }

    $sessionName = config('session.cookie');
    $cookieSent = array_key_exists($sessionName, request()->cookies->all());

    return response()->json([
        'authenticated' => \Illuminate\Support\Facades\Auth::check(),
        'user' => optional(\Illuminate\Support\Facades\Auth::user())->only(['id','email','firebase_uuid']),
        'session_id' => session()->getId(),
        'session_cookie_name' => $sessionName,
        'session_cookie_sent' => $cookieSent,
        'request_ip' => request()->ip(),
        'cookies' => array_keys(request()->cookies->all()),
    ]);
});

// Streaming Providers list (JSON)
Route::get('/streaming-providers', function (\Illuminate\Http\Request $request, TMDBService $tmdb) {
    $provider = (string) $request->get('provider', 'netflix');
    $type = (string) $request->get('type', 'movie');
    $page = (int) $request->get('page', 1);

    $posterSize = $request->get('poster_size', 'w342');
    $backdropSize = $request->get('backdrop_size', 'w780');

    try {
        $data = $tmdb->getProviderContent($provider, $type, $page);

        if (!empty($data['results']) && is_array($data['results'])) {
            $data['results'] = array_map(function ($item) use ($tmdb, $type, $posterSize, $backdropSize) {
                $poster = $tmdb->getImageUrl($item['poster_path'] ?? null, $posterSize, true);
                $backdrop = $tmdb->getImageUrl($item['backdrop_path'] ?? null, $backdropSize, true);

                return [
                    'id' => $item['id'] ?? null,
                    'title' => $item['title'] ?? $item['name'] ?? 'Untitled',
                    'name' => $item['name'] ?? $item['title'] ?? 'Untitled',
                    'overview' => $item['overview'] ?? '',
                    'release_date' => $item['release_date'] ?? $item['first_air_date'] ?? null,
                    'first_air_date' => $item['first_air_date'] ?? $item['release_date'] ?? null,
                    'vote_average' => $item['vote_average'] ?? 0,
                    'poster_path' => $item['poster_path'] ?? null,
                    'backdrop_path' => $item['backdrop_path'] ?? null,
                    'poster_url' => $poster,
                    'backdrop_url' => $backdrop,
                    'media_type' => $type === 'tv' ? 'tv' : 'movie',
                ];
            }, $data['results']);
        }

        return response()->json($data);
    } catch (\Throwable $e) {
        \Log::error("Failed to fetch streaming provider data for {$provider} ({$type}): " . $e->getMessage());
        return response()->json(['error' => 'Failed to fetch provider content'], 500);
    }
});

// Support & Live Chat Endpoints (public)
Route::post('/support/ticket', [\App\Http\Controllers\SupportController::class, 'submitTicket']);
Route::get('/support/tickets', [\App\Http\Controllers\SupportController::class, 'getTickets']);
Route::post('/support/chat/send', [\App\Http\Controllers\SupportController::class, 'sendChatMessage']);
Route::get('/support/chat/history', [\App\Http\Controllers\SupportController::class, 'getChatHistory']);
Route::post('/support/ticket/reopen-request', [\App\Http\Controllers\SupportController::class, 'requestTicketReopen']);

// V3 Authentication Endpoints (for proxy authorization and API consumers)
Route::prefix('v3/auth')->group(function () {
    // If accessed via GET in browser or misconfigured OAuth URL, redirect to authorization page
    Route::get('/login', function (Request $request) {
        return redirect()->route('oauth.authorize', $request->query());
    });

    // API Login endpoint (handles form submission and redirect)
    Route::post('/login', [AuthController::class, 'login']);

    // Token verification endpoint (Protected by Sanctum)
    Route::middleware('auth:sanctum')->get('/me', [AuthController::class, 'me']);
});

/*
|--------------------------------------------------------------------------
| Mobile App API (v1)
|--------------------------------------------------------------------------
| Dedicated, standardized RESTful endpoints designed for the mobile app
| (Flutter / React Native / Kotlin / Swift).
*/
Route::prefix('v1')->group(function () {
    // App & Metadata
    Route::get('/app/config', [V1AppController::class, 'config']);
    Route::get('/app/check-update', [V1AppController::class, 'checkUpdate']);
    Route::get('/banners', [V1AppController::class, 'banners']);

    // Home Aggregated Feed
    Route::get('/home', [V1HomeController::class, 'index']);

    // Public Authentication
    Route::prefix('auth')->group(function () {
        Route::post('/register', [V1AuthController::class, 'register']);
        Route::post('/login', [V1AuthController::class, 'login']);
    });

    // Movies
    Route::prefix('movies')->group(function () {
        Route::get('/', [V1MovieController::class, 'index']);
        Route::get('/{id}', [V1MovieController::class, 'show'])->whereNumber('id');
        Route::get('/{id}/sources', [V1MovieController::class, 'sources'])->whereNumber('id');
    });

    // TV Shows
    Route::prefix('tv-shows')->group(function () {
        Route::get('/', [V1TvShowController::class, 'index']);
        Route::get('/{id}', [V1TvShowController::class, 'show'])->whereNumber('id');
        Route::get('/{id}/seasons/{season}', [V1TvShowController::class, 'season'])->whereNumber(['id', 'season']);
        Route::get('/{id}/seasons/{season}/episodes/{episode}/sources', [V1TvShowController::class, 'episodeSources'])->whereNumber(['id', 'season', 'episode']);
    });

    // Anime
    Route::prefix('anime')->group(function () {
        Route::get('/', [V1AnimeController::class, 'index']);
        Route::get('/{id}', [V1AnimeController::class, 'show'])->whereNumber('id');
        Route::get('/{id}/seasons/{season}', [V1AnimeController::class, 'season'])->whereNumber(['id', 'season']);
        Route::get('/{id}/seasons/{season}/episodes/{episode}/sources', [V1AnimeController::class, 'episodeSources'])->whereNumber(['id', 'season', 'episode']);
    });

    // Search, Discovery, Genres & Streaming Providers
    Route::get('/search', [V1SearchController::class, 'search']);
    Route::get('/search/suggestions', [V1SearchController::class, 'suggestions']);
    Route::get('/genres', [V1SearchController::class, 'genres']);
    Route::get('/discover', [V1SearchController::class, 'discover']);
    Route::get('/providers', [V1SearchController::class, 'providers']);

    // Authenticated Mobile Routes
    Route::middleware('auth:sanctum')->group(function () {
        // Auth & Profile Management
        Route::prefix('auth')->group(function () {
            Route::get('/me', [V1AuthController::class, 'me']);
            Route::put('/profile', [V1AuthController::class, 'updateProfile']);
            Route::put('/password', [V1AuthController::class, 'updatePassword']);
            Route::post('/avatar', [V1AuthController::class, 'uploadAvatar']);
            Route::get('/firebase-token', [V1AuthController::class, 'firebaseToken']);
            Route::post('/logout', [V1AuthController::class, 'logout']);
            Route::delete('/delete-account', [V1AuthController::class, 'deleteAccount']);
        });

        // Favorites / Watchlist
        Route::prefix('favorites')->group(function () {
            Route::get('/', [V1FavoriteController::class, 'index']);
            Route::post('/toggle', [V1FavoriteController::class, 'toggle']);
            Route::get('/check/{mediaId}', [V1FavoriteController::class, 'check'])->whereNumber('mediaId');
            Route::delete('/{mediaId}', [V1FavoriteController::class, 'destroy'])->whereNumber('mediaId');
        });

        // Watch History & Progress (Syncs local SQL & Firebase RTDB)
        Route::prefix('watch-history')->group(function () {
            Route::get('/', [V1WatchHistoryController::class, 'index']);
            Route::post('/', [V1WatchHistoryController::class, 'save']);
            Route::get('/{mediaId}', [V1WatchHistoryController::class, 'show'])->whereNumber('mediaId');
            Route::delete('/{mediaId}', [V1WatchHistoryController::class, 'destroy'])->whereNumber('mediaId');
            Route::delete('/', [V1WatchHistoryController::class, 'clear']);
        });

        // In-App Support Tickets & Chat
        Route::prefix('support')->group(function () {
            Route::get('/tickets', [V1SupportController::class, 'index']);
            Route::post('/tickets', [V1SupportController::class, 'store']);
            Route::get('/tickets/{ticketId}', [V1SupportController::class, 'show']);
            Route::post('/tickets/{ticketId}/messages', [V1SupportController::class, 'sendMessage']);
            Route::post('/tickets/{ticketId}/reopen', [V1SupportController::class, 'requestReopen']);
        });
    });
});




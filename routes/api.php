<?php

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
            $movies['results'] = array_map(function ($item) use ($tmdb, $minimal, $includeLogos, $posterSize, $backdropSize) {
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

                    // Avoid making per-item TMDB image API calls when returning
                    // a minimal payload. If the caller explicitly requested
                    // logos but the images are not cached, we return an empty
                    // logo_url rather than triggering N extra TMDB requests.
                    if ($includeLogos && isset($item['id'])) {
                        $imgCacheKey = "movie.{$item['id']}.images";
                        if (\Illuminate\Support\Facades\Cache::has($imgCacheKey)) {
                            $images = \Illuminate\Support\Facades\Cache::get($imgCacheKey);
                            $logoPath = null;
                            if (!empty($images['logos'])) {
                                foreach ($images['logos'] as $logo) {
                                    if (!empty($logo['iso_3166_1']) && strtoupper($logo['iso_3166_1']) === 'US') {
                                        $logoPath = $logo['file_path'] ?? null;
                                        break;
                                    }
                                }
                                if (!$logoPath) {
                                    $logoPath = $images['logos'][0]['file_path'] ?? null;
                                }
                            }

                            $compact['logo_url'] = $logoPath ? $tmdb->getImageUrl($logoPath, 'w280', true) : '';
                        } else {
                            $compact['logo_url'] = '';
                        }
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

// Single movie details (JSON)
Route::get('/movies/{id}', function (int $id, TMDBService $tmdb) {
    try {
        $movie = $tmdb->getMovieDetails($id);
        if (! $movie) return response()->json(['error' => 'Not found'], 404);
        $credits = $tmdb->getMovieCredits($id);
        $similar = $tmdb->getSimilarMovies($id);

        // Add proxied image URLs and logo
        $movie['poster_url'] = $tmdb->getImageUrl($movie['poster_path'] ?? null, 'w500', true);
        $movie['backdrop_url'] = $tmdb->getImageUrl($movie['backdrop_path'] ?? null, 'w1280', true);
        $movie['logo_url'] = $tmdb->getMovieLogoUrl($id, 'US', 'w500', true);

        // Enrich similar results if present
        $similarResults = [];
        if (!empty($similar['results']) && is_array($similar['results'])) {
            $similarResults = array_map(function ($item) use ($tmdb) {
                $item['poster_url'] = $tmdb->getImageUrl($item['poster_path'] ?? null, 'w500', true);
                $item['backdrop_url'] = $tmdb->getImageUrl($item['backdrop_path'] ?? null, 'w1280', true);
                $item['logo_url'] = isset($item['id']) ? $tmdb->getMovieLogoUrl((int) $item['id'], 'US', 'w500', true) : '';
                return $item;
            }, $similar['results']);
        }

        return response()->json(array_merge($movie, [
            'credits' => $credits ?? [],
            'similar' => $similarResults,
        ]));
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to fetch movie: ' . $e->getMessage()], 500);
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
// This uses sanctum/session authentication; callers should include credentials.
Route::middleware('auth:sanctum')->get('/firebase-token', function (\App\Services\FirebaseService $firebase) {
    $isLocalRequest = app()->environment('local') || in_array(request()->ip(), ['127.0.0.1', '::1'], true);

    if (! $firebase->isConfigured()) {
        $payload = ['error' => 'Firebase not configured'];
        if ($isLocalRequest) {
            $serviceAccountPath = storage_path('app/firebase/service-account.json');
            $payload['debug'] = [
                'service_account_exists' => file_exists($serviceAccountPath),
                'service_account_path' => $serviceAccountPath,
                // Do not dump the entire service account contents; only show that path and existence
                'config_services_firebase' => array_intersect_key(config('services.firebase', []), array_flip(['project_id','database_url','api_key','messaging_sender_id']))
            ];
        }

        return response()->json($payload, 503);
    }

    $token = $firebase->createCustomToken();

    if (! $token) {
        $payload = ['error' => 'Failed to create token'];

        if ($isLocalRequest) {
            $payload['debug'] = [
                'authenticated' => \Illuminate\Support\Facades\Auth::check(),
                'user' => optional(\Illuminate\Support\Facades\Auth::user())->only(['id','email','firebase_uuid']),
            ];
        }

        return response()->json($payload, 500);
    }

    return response()->json(['token' => $token]);
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

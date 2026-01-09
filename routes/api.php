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

    try {
        $movies = match ($filter) {
            'top_rated' => $tmdb->getTopRatedMovies($page),
            'now_playing' => $tmdb->getNowPlayingMovies($page),
            'upcoming' => $tmdb->getUpcomingMovies($page),
            default => $tmdb->getPopularMovies($page),
        };

        // Enrich results with proxied image URLs and logo if available
        if (!empty($movies['results']) && is_array($movies['results'])) {
            $movies['results'] = array_map(function ($item) use ($tmdb) {
                $item['poster_url'] = $tmdb->getImageUrl($item['poster_path'] ?? null, 'w500', true);
                $item['backdrop_url'] = $tmdb->getImageUrl($item['backdrop_path'] ?? null, 'w1280', true);
                $item['logo_url'] = isset($item['id']) ? $tmdb->getMovieLogoUrl((int) $item['id'], 'US', 'w500', true) : '';
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

    try {
        $tv = match ($filter) {
            'top_rated' => $tmdb->getTopRatedTvShows($page),
            'on_the_air' => $tmdb->getOnTheAirTvShows($page),
            'airing_today' => $tmdb->getAiringTodayTvShows($page),
            default => $tmdb->getPopularTvShows($page),
        };

        if (!empty($tv['results']) && is_array($tv['results'])) {
            $tv['results'] = array_map(function ($item) use ($tmdb) {
                $item['poster_url'] = $tmdb->getImageUrl($item['poster_path'] ?? null, 'w500', true);
                $item['backdrop_url'] = $tmdb->getImageUrl($item['backdrop_path'] ?? null, 'w1280', true);
                $item['logo_url'] = isset($item['id']) ? $tmdb->getTvLogoUrl((int) $item['id'], 'US', 'w500', true) : '';
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

    try {
        $anime = match ($filter) {
            'top_rated' => $tmdb->getTopRatedAnime($page),
            'airing' => $tmdb->getAiringAnime($page),
            default => $tmdb->getPopularAnime($page),
        };

        if (!empty($anime['results']) && is_array($anime['results'])) {
            $anime['results'] = array_map(function ($item) use ($tmdb) {
                $item['poster_url'] = $tmdb->getImageUrl($item['poster_path'] ?? null, 'w500', true);
                $item['backdrop_url'] = $tmdb->getImageUrl($item['backdrop_path'] ?? null, 'w1280', true);
                // Anime are TV shows under the hood; use TV logo helper
                $item['logo_url'] = isset($item['id']) ? $tmdb->getTvLogoUrl((int) $item['id'], 'US', 'w500', true) : '';
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
    if (! $firebase->isConfigured()) {
        return response()->json(['error' => 'Firebase not configured'], 503);
    }

    $token = $firebase->createCustomToken();

    if (! $token) {
        return response()->json(['error' => 'Failed to create token'], 500);
    }

    return response()->json(['token' => $token]);
});

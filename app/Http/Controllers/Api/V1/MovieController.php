<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Favorite;
use App\Models\VideoSource;
use App\Models\WatchHistory;
use App\Services\TMDBService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovieController extends BaseApiController
{
    public function __construct(protected TMDBService $tmdb)
    {
    }

    /**
     * Get paginated list of movies with filter support.
     * GET /api/v1/movies?filter=popular&page=1
     */
    public function index(Request $request): JsonResponse
    {
        $filter = $request->query('filter', 'popular');
        $page = max(1, (int) $request->query('page', 1));

        $data = match ($filter) {
            'top_rated'   => $this->tmdb->getTopRatedMovies($page),
            'now_playing' => $this->tmdb->getNowPlayingMovies($page),
            'upcoming'    => $this->tmdb->getUpcomingMovies($page),
            'trending'    => $this->tmdb->getTrendingMovies('week'),
            'filipino'    => $this->tmdb->getFilipinoMovies($page),
            default       => $this->tmdb->getPopularMovies($page),
        };

        if (!$data) {
            return $this->error('Failed to retrieve movies list.', 500);
        }

        $results = $this->enrichMediaList($data['results'] ?? [], $this->tmdb, 'movie');

        return $this->success([
            'filter'        => $filter,
            'page'          => $data['page'] ?? $page,
            'total_pages'   => $data['total_pages'] ?? 1,
            'total_results' => $data['total_results'] ?? count($results),
            'results'       => $results,
        ]);
    }

    /**
     * Get full details of a single movie including credits, trailers, and similar titles.
     * GET /api/v1/movies/{id}
     */
    public function show(int $id): JsonResponse
    {
        $movie = $this->tmdb->getMovieDetails($id);

        if (!$movie) {
            return $this->error('Movie not found.', 404);
        }

        $movie['media_type'] = 'movie';
        $movie['poster_url'] = $this->tmdb->getImageUrl($movie['poster_path'] ?? null, 'w500', true);
        $movie['backdrop_url'] = $this->tmdb->getImageUrl($movie['backdrop_path'] ?? null, 'original', true);
        $movie['logo_url'] = $this->tmdb->getMovieLogoUrl($id, 'US', 'w500', true);

        // Format YouTube trailers/teasers
        $trailers = [];
        if (!empty($movie['videos']['results'])) {
            foreach ($movie['videos']['results'] as $vid) {
                if (($vid['site'] ?? '') === 'YouTube') {
                    $trailers[] = [
                        'id'        => $vid['id'] ?? null,
                        'name'      => $vid['name'] ?? null,
                        'key'       => $vid['key'] ?? null,
                        'youtube_url' => 'https://www.youtube.com/watch?v=' . ($vid['key'] ?? ''),
                        'type'      => $vid['type'] ?? 'Trailer',
                        'official'  => (bool) ($vid['official'] ?? false),
                    ];
                }
            }
        }
        $movie['trailers'] = $trailers;

        // Enrich similar movies
        if (!empty($movie['similar']['results'])) {
            $movie['similar']['results'] = $this->enrichMediaList(
                array_slice($movie['similar']['results'], 0, 10),
                $this->tmdb,
                'movie'
            );
        }

        // Check user library status (favorites & watch progress)
        $user = Auth::guard('sanctum')->user();
        $isFavorited = false;
        $progress = null;

        if ($user) {
            try {
                $isFavorited = Favorite::where('user_id', $user->id)
                    ->where('media_id', $id)
                    ->where('media_type', 'movie')
                    ->exists();

                $history = WatchHistory::where('user_id', $user->id)
                    ->where('media_id', $id)
                    ->where('media_type', 'movie')
                    ->first();

                if ($history) {
                    $progress = [
                        'watched_time' => $history->watched_time,
                        'duration'     => $history->duration,
                        'progress'     => $history->progress,
                        'is_completed' => $history->is_completed,
                        'last_updated' => $history->updated_at?->toIso8601String(),
                    ];
                }
            } catch (\Throwable $e) {
                // Ignore DB error for metadata on public detail view
            }
        }

        $movie['user_meta'] = [
            'is_favorited' => $isFavorited,
            'progress'     => $progress,
        ];

        return $this->success($movie);
    }

    /**
     * Get video stream and embed sources for a movie.
     * GET /api/v1/movies/{id}/sources
     */
    public function sources(int $id): JsonResponse
    {
        $sources = [];
        $defaultEmbed = null;

        try {
            $sourceData = VideoSource::getSourcesForContent('movie', $id);
            $sources = $sourceData['sources'] ?? [];
            $defaultEmbed = $sourceData['default_embed'] ?? null;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Movie sources lookup failed: ' . $e->getMessage());
        }

        return $this->success([
            'id'            => $id,
            'type'          => 'movie',
            'default_embed' => $defaultEmbed,
            'sources'       => $sources,
        ]);
    }
}

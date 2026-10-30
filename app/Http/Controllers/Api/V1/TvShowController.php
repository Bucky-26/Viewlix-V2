<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Favorite;
use App\Models\VideoSource;
use App\Models\WatchHistory;
use App\Services\TMDBService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TvShowController extends BaseApiController
{
    public function __construct(protected TMDBService $tmdb)
    {
    }

    /**
     * Get paginated list of TV series with filter support.
     * GET /api/v1/tv-shows?filter=popular&page=1
     */
    public function index(Request $request): JsonResponse
    {
        $filter = $request->query('filter', 'popular');
        $page = max(1, (int) $request->query('page', 1));

        $data = match ($filter) {
            'top_rated'    => $this->tmdb->getTopRatedTvShows($page),
            'on_the_air'   => $this->tmdb->getOnTheAirTvShows($page),
            'airing_today' => $this->tmdb->getAiringTodayTvShows($page),
            'trending'     => $this->tmdb->getTrendingTvShows('week'),
            'filipino'     => $this->tmdb->getFilipinoTvShows($page),
            default        => $this->tmdb->getPopularTvShows($page),
        };

        if (!$data) {
            return $this->error('Failed to retrieve TV shows list.', 500);
        }

        $results = $this->enrichMediaList($data['results'] ?? [], $this->tmdb, 'tv');

        return $this->success([
            'filter'        => $filter,
            'page'          => $data['page'] ?? $page,
            'total_pages'   => $data['total_pages'] ?? 1,
            'total_results' => $data['total_results'] ?? count($results),
            'results'       => $results,
        ]);
    }

    /**
     * Get TV show details including season list, cast, and similar shows.
     * GET /api/v1/tv-shows/{id}
     */
    public function show(int $id): JsonResponse
    {
        $tvShow = $this->tmdb->getTvShowDetails($id);

        if (!$tvShow) {
            return $this->error('TV Show not found.', 404);
        }

        $tvShow['media_type'] = 'tv';
        $tvShow['poster_url'] = $this->tmdb->getImageUrl($tvShow['poster_path'] ?? null, 'w500', true);
        $tvShow['backdrop_url'] = $this->tmdb->getImageUrl($tvShow['backdrop_path'] ?? null, 'original', true);
        $tvShow['logo_url'] = $this->tmdb->getTvLogoUrl($id, 'US', 'w500', true);

        // Enrich seasons list
        if (!empty($tvShow['seasons']) && is_array($tvShow['seasons'])) {
            $tvShow['seasons'] = array_map(function ($s) {
                $s['poster_url'] = $this->tmdb->getImageUrl($s['poster_path'] ?? null, 'w342', true);
                return $s;
            }, $tvShow['seasons']);
        }

        // Format YouTube trailers/teasers
        $trailers = [];
        if (!empty($tvShow['videos']['results'])) {
            foreach ($tvShow['videos']['results'] as $vid) {
                if (($vid['site'] ?? '') === 'YouTube') {
                    $trailers[] = [
                        'id'          => $vid['id'] ?? null,
                        'name'        => $vid['name'] ?? null,
                        'key'         => $vid['key'] ?? null,
                        'youtube_url' => 'https://www.youtube.com/watch?v=' . ($vid['key'] ?? ''),
                        'type'        => $vid['type'] ?? 'Trailer',
                        'official'    => (bool) ($vid['official'] ?? false),
                    ];
                }
            }
        }
        $tvShow['trailers'] = $trailers;

        // Enrich similar TV shows
        if (!empty($tvShow['similar']['results'])) {
            $tvShow['similar']['results'] = $this->enrichMediaList(
                array_slice($tvShow['similar']['results'], 0, 10),
                $this->tmdb,
                'tv'
            );
        }

        // User library status
        $user = Auth::guard('sanctum')->user();
        $isFavorited = false;
        $lastWatched = null;

        if ($user) {
            try {
                $isFavorited = Favorite::where('user_id', $user->id)
                    ->where('media_id', $id)
                    ->where('media_type', 'tv')
                    ->exists();

                $history = WatchHistory::where('user_id', $user->id)
                    ->where('media_id', $id)
                    ->where('media_type', 'tv')
                    ->latest('updated_at')
                    ->first();

                if ($history) {
                    $lastWatched = [
                        'season'       => $history->season,
                        'episode'      => $history->episode,
                        'watched_time' => $history->watched_time,
                        'duration'     => $history->duration,
                        'progress'     => $history->progress,
                        'last_updated' => $history->updated_at?->toIso8601String(),
                    ];
                }
            } catch (\Throwable $e) {
                // Ignore DB error for metadata on public detail view
            }
        }

        $tvShow['user_meta'] = [
            'is_favorited' => $isFavorited,
            'last_watched' => $lastWatched,
        ];

        return $this->success($tvShow);
    }

    /**
     * Get episode list and details for a specific season.
     * GET /api/v1/tv-shows/{id}/seasons/{season}
     */
    public function season(int $id, int $season): JsonResponse
    {
        $seasonData = $this->tmdb->getTvSeasonDetails($id, $season);

        if (!$seasonData) {
            return $this->error('Season not found.', 404);
        }

        if (!empty($seasonData['episodes']) && is_array($seasonData['episodes'])) {
            $seasonData['episodes'] = array_map(function ($ep) {
                $ep['still_url'] = $this->tmdb->getImageUrl($ep['still_path'] ?? null, 'w300', true);
                return $ep;
            }, $seasonData['episodes']);
        }

        $seasonData['poster_url'] = $this->tmdb->getImageUrl($seasonData['poster_path'] ?? null, 'w500', true);

        return $this->success($seasonData);
    }

    /**
     * Get video stream and embed sources for an episode.
     * GET /api/v1/tv-shows/{id}/seasons/{season}/episodes/{episode}/sources
     */
    public function episodeSources(int $id, int $season, int $episode): JsonResponse
    {
        $sources = [];
        $defaultEmbed = null;

        try {
            $sourceData = VideoSource::getSourcesForContent('tv', $id, $season, $episode);
            $sources = $sourceData['sources'] ?? [];
            $defaultEmbed = $sourceData['default_embed'] ?? null;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('TV episode sources lookup failed: ' . $e->getMessage());
        }

        return $this->success([
            'id'            => $id,
            'type'          => 'tv',
            'season'        => $season,
            'episode'       => $episode,
            'default_embed' => $defaultEmbed,
            'sources'       => $sources,
        ]);
    }
}

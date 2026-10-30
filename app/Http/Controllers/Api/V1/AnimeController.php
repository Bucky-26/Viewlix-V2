<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Favorite;
use App\Models\VideoSource;
use App\Models\WatchHistory;
use App\Services\TMDBService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnimeController extends BaseApiController
{
    public function __construct(protected TMDBService $tmdb)
    {
    }

    /**
     * Get paginated list of anime series.
     * GET /api/v1/anime?filter=popular&page=1
     */
    public function index(Request $request): JsonResponse
    {
        $filter = $request->query('filter', 'popular');
        $page = max(1, (int) $request->query('page', 1));

        $data = match ($filter) {
            'top_rated' => $this->tmdb->getTopRatedAnime($page),
            'airing'    => $this->tmdb->getAiringAnime($page),
            default     => $this->tmdb->getPopularAnime($page),
        };

        if (!$data) {
            return $this->error('Failed to retrieve anime list.', 500);
        }

        $results = $this->enrichMediaList($data['results'] ?? [], $this->tmdb, 'anime');

        return $this->success([
            'filter'        => $filter,
            'page'          => $data['page'] ?? $page,
            'total_pages'   => $data['total_pages'] ?? 1,
            'total_results' => $data['total_results'] ?? count($results),
            'results'       => $results,
        ]);
    }

    /**
     * Get Anime details including seasons, episodes, and similar titles.
     * GET /api/v1/anime/{id}
     */
    public function show(int $id): JsonResponse
    {
        $anime = $this->tmdb->getAnimeDetails($id) ?? $this->tmdb->getTvShowDetails($id);

        if (!$anime) {
            return $this->error('Anime not found.', 404);
        }

        $anime['media_type'] = 'anime';
        $anime['poster_url'] = $this->tmdb->getImageUrl($anime['poster_path'] ?? null, 'w500', true);
        $anime['backdrop_url'] = $this->tmdb->getImageUrl($anime['backdrop_path'] ?? null, 'original', true);
        $anime['logo_url'] = $this->tmdb->getTvLogoUrl($id, 'US', 'w500', true);

        // Enrich seasons list
        if (!empty($anime['seasons']) && is_array($anime['seasons'])) {
            $anime['seasons'] = array_map(function ($s) {
                $s['poster_url'] = $this->tmdb->getImageUrl($s['poster_path'] ?? null, 'w342', true);
                return $s;
            }, $anime['seasons']);
        }

        // Format YouTube trailers/teasers
        $trailers = [];
        if (!empty($anime['videos']['results'])) {
            foreach ($anime['videos']['results'] as $vid) {
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
        $anime['trailers'] = $trailers;

        // Enrich similar
        if (!empty($anime['similar']['results'])) {
            $anime['similar']['results'] = $this->enrichMediaList(
                array_slice($anime['similar']['results'], 0, 10),
                $this->tmdb,
                'anime'
            );
        }

        // Check user meta
        $user = Auth::guard('sanctum')->user();
        $isFavorited = false;
        $lastWatched = null;

        if ($user) {
            try {
                $isFavorited = Favorite::where('user_id', $user->id)
                    ->where('media_id', $id)
                    ->where('media_type', 'anime')
                    ->exists();

                $history = WatchHistory::where('user_id', $user->id)
                    ->where('media_id', $id)
                    ->where('media_type', 'anime')
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

        $anime['user_meta'] = [
            'is_favorited' => $isFavorited,
            'last_watched' => $lastWatched,
        ];

        return $this->success($anime);
    }

    /**
     * Get episode list for an anime season.
     * GET /api/v1/anime/{id}/seasons/{season}
     */
    public function season(int $id, int $season): JsonResponse
    {
        $seasonData = $this->tmdb->getTvSeasonDetails($id, $season);

        if (!$seasonData) {
            return $this->error('Anime season not found.', 404);
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
     * Get video stream and embed sources for an anime episode.
     * GET /api/v1/anime/{id}/seasons/{season}/episodes/{episode}/sources
     */
    public function episodeSources(int $id, int $season, int $episode): JsonResponse
    {
        $sources = [];
        $defaultEmbed = null;

        try {
            $sourceData = VideoSource::getSourcesForContent('anime', $id, $season, $episode);
            $sources = $sourceData['sources'] ?? [];
            $defaultEmbed = $sourceData['default_embed'] ?? null;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Anime episode sources lookup failed: ' . $e->getMessage());
        }

        return $this->success([
            'id'            => $id,
            'type'          => 'anime',
            'season'        => $season,
            'episode'       => $episode,
            'default_embed' => $defaultEmbed,
            'sources'       => $sources,
        ]);
    }
}

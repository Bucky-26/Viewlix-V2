<?php

namespace App\Http\Controllers\Api\V1;

use App\Services\TMDBService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends BaseApiController
{
    public function __construct(protected TMDBService $tmdb)
    {
    }

    /**
     * Search across movies, TV series, and anime.
     * GET /api/v1/search?query=batman&type=movie&page=1
     */
    public function search(Request $request): JsonResponse
    {
        $query = (string) ($request->query('query') ?? $request->query('q') ?? '');
        $type = (string) $request->query('type', 'all');
        $page = max(1, (int) $request->query('page', 1));

        if (trim($query) === '') {
            return $this->error('Search query is required.', 422);
        }

        $data = match ($type) {
            'movie' => $this->tmdb->searchMovies($query, $page),
            'tv'    => $this->tmdb->searchTvShows($query, $page),
            default => $this->tmdb->multiSearch($query, $page),
        };

        if (!$data) {
            return $this->error('Failed to perform search.', 500);
        }

        // Filter out people or empty media types for cleaner mobile results
        $rawResults = array_filter($data['results'] ?? [], function ($item) {
            return ($item['media_type'] ?? '') !== 'person';
        });

        $results = $this->enrichMediaList(array_values($rawResults), $this->tmdb, $type === 'all' ? null : $type);

        return $this->success([
            'query'         => $query,
            'type'          => $type,
            'page'          => $data['page'] ?? $page,
            'total_pages'   => $data['total_pages'] ?? 1,
            'total_results' => $data['total_results'] ?? count($results),
            'results'       => $results,
        ]);
    }

    /**
     * Fast autocomplete suggestions for mobile search input.
     * GET /api/v1/search/suggestions?q=avatar
     */
    public function suggestions(Request $request): JsonResponse
    {
        $query = (string) ($request->query('query') ?? $request->query('q') ?? '');

        if (trim($query) === '') {
            return $this->success([]);
        }

        $results = $this->tmdb->multiSearch($query, 1);
        $suggestions = [];

        foreach (array_slice($results['results'] ?? [], 0, 10) as $item) {
            $mediaType = $item['media_type'] ?? null;
            if ($mediaType === 'person' || empty($item['id'])) {
                continue;
            }

            $title = $item['title'] ?? $item['name'] ?? null;
            if (!$title) {
                continue;
            }

            $date = $item['release_date'] ?? $item['first_air_date'] ?? null;
            $year = $date ? substr($date, 0, 4) : null;

            $suggestions[] = [
                'id'           => $item['id'],
                'title'        => $title,
                'media_type'   => $mediaType ?: (isset($item['title']) ? 'movie' : 'tv'),
                'year'         => $year,
                'vote_average' => $item['vote_average'] ?? 0,
                'poster_url'   => $this->tmdb->getImageUrl($item['poster_path'] ?? null, 'w185', true),
            ];
        }

        return $this->success($suggestions);
    }

    /**
     * Get genre lists for movies and TV shows.
     * GET /api/v1/genres
     */
    public function genres(): JsonResponse
    {
        $movieGenres = $this->tmdb->getMovieGenres()['genres'] ?? [];
        $tvGenres = $this->tmdb->getTvGenres()['genres'] ?? [];

        return $this->success([
            'movie' => $movieGenres,
            'tv'    => $tvGenres,
        ]);
    }

    /**
     * Discover media by genres, year, and sorting criteria.
     * GET /api/v1/discover?type=movie&with_genres=28&page=1
     */
    public function discover(Request $request): JsonResponse
    {
        $type = $request->query('type', 'movie');
        $params = $request->only([
            'with_genres',
            'without_genres',
            'sort_by',
            'primary_release_year',
            'first_air_date_year',
            'year',
            'page',
            'vote_average.gte',
            'with_original_language',
        ]);

        $page = max(1, (int) ($params['page'] ?? 1));
        $params['page'] = $page;

        $data = $this->tmdb->discover($type, $params);

        if (!$data) {
            return $this->error('Failed to discover content.', 500);
        }

        $results = $this->enrichMediaList($data['results'] ?? [], $this->tmdb, $type);

        return $this->success([
            'type'          => $type,
            'page'          => $data['page'] ?? $page,
            'total_pages'   => $data['total_pages'] ?? 1,
            'total_results' => $data['total_results'] ?? count($results),
            'results'       => $results,
        ]);
    }

    /**
     * Get content by popular streaming provider (Netflix, Disney+, HBO, etc.).
     * GET /api/v1/providers?provider=netflix&type=movie&page=1
     */
    public function providers(Request $request): JsonResponse
    {
        $provider = (string) $request->query('provider', 'netflix');
        $type = (string) $request->query('type', 'movie');
        $page = max(1, (int) $request->query('page', 1));

        $data = $this->tmdb->getProviderContent($provider, $type, $page);

        if (!$data) {
            return $this->error('Failed to fetch provider content.', 500);
        }

        $results = $this->enrichMediaList($data['results'] ?? [], $this->tmdb, $type);

        return $this->success([
            'provider'      => $provider,
            'type'          => $type,
            'page'          => $data['page'] ?? $page,
            'total_pages'   => $data['total_pages'] ?? 1,
            'total_results' => $data['total_results'] ?? count($results),
            'results'       => $results,
        ]);
    }
}


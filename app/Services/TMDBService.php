<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class TMDBService
{
    protected string $baseUrl;
    protected string $accessToken;
    protected string $imageBaseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.tmdb.base_url');
        $this->accessToken = config('services.tmdb.access_token');
        $this->imageBaseUrl = config('services.tmdb.image_base_url');
    }

    /**
     * Make API request to TMDB
     */
    protected function request(string $endpoint, array $params = []): ?array
    {
        $response = Http::withToken($this->accessToken)
            ->get("{$this->baseUrl}{$endpoint}", $params);

        if ($response->successful()) {
            return $response->json();
        }

        return null;
    }

    /**
     * Get image URL
     *
     * By default this returns the direct TMDB image URL (no proxy).
     * Set $useProxy = true to return a wsrv.nl proxied URL which can
     * be used for caching/resizing/webp output. This is opt-in so
     * internal service methods that expect a direct TMDB URL aren't
     * affected.
     *
     * @param string|null $path TMDB file path (e.g. /abc123.jpg)
     * @param string $size TMDB size token (e.g. 'w500' or 'original') or numeric width
     * @param bool $useProxy Return a wsrv.nl proxied URL when true
     * @return string
     */
    public function getImageUrl(?string $path, string $size = 'w500', bool $useProxy = false): string
    {
        if (!$path) {
            return '';
        }

        // Build direct TMDB URL first
        $tmdbUrl = "{$this->imageBaseUrl}/{$size}{$path}";

        if (!$useProxy) {
            return $tmdbUrl;
        }

        // If proxying, use the original TMDB image so the proxy can resize reliably
        $tmdbOriginal = "{$this->imageBaseUrl}/original{$path}";

        // Determine numeric width from TMDB size token or numeric size
        $width = 0;
        if (preg_match('/^w(\d+)$/i', $size, $m)) {
            $width = (int) $m[1];
        } elseif (is_numeric($size)) {
            $width = (int) $size;
        }

        if ($width <= 0) {
            $width = 500;
        }

        $encoded = rawurlencode($tmdbOriginal);
        // Use wsrv.nl to proxy/resize and deliver webp; default quality 80
        return "https://wsrv.nl/?url={$encoded}&w={$width}&output=webp&q=80";
    }

    // ==================== MOVIES ====================

    /**
     * Get popular movies
     */
    public function getPopularMovies(int $page = 1): ?array
    {
        return Cache::remember("movies.popular.{$page}", 3600, function () use ($page) {
            return $this->request('/movie/popular', ['page' => $page]);
        });
    }

    /**
     * Get top rated movies
     */
    public function getTopRatedMovies(int $page = 1): ?array
    {
        return Cache::remember("movies.top_rated.{$page}", 3600, function () use ($page) {
            return $this->request('/movie/top_rated', ['page' => $page]);
        });
    }

    /**
     * Get now playing movies
     */
    public function getNowPlayingMovies(int $page = 1): ?array
    {
        return Cache::remember("movies.now_playing.{$page}", 3600, function () use ($page) {
            return $this->request('/movie/now_playing', ['page' => $page]);
        });
    }

    /**
     * Get upcoming movies
     */
    public function getUpcomingMovies(int $page = 1): ?array
    {
        return Cache::remember("movies.upcoming.{$page}", 3600, function () use ($page) {
            return $this->request('/movie/upcoming', ['page' => $page]);
        });
    }

    /**
     * Get trending movies
     */
    public function getTrendingMovies(string $timeWindow = 'week'): ?array
    {
        return Cache::remember("movies.trending.{$timeWindow}", 3600, function () use ($timeWindow) {
            return $this->request("/trending/movie/{$timeWindow}");
        });
    }

    /**
     * Get movie details
     */
    public function getMovieDetails(int $id): ?array
    {
        return Cache::remember("movie.{$id}", 3600, function () use ($id) {
            return $this->request("/movie/{$id}", [
                'append_to_response' => 'credits,videos,similar,recommendations'
            ]);
        });
    }

    /**
     * Search movies
     */
    public function searchMovies(string $query, int $page = 1): ?array
    {
        return $this->request('/search/movie', [
            'query' => $query,
            'page' => $page
        ]);
    }

    /**
     * Get movie credits
     */
    public function getMovieCredits(int $id): ?array
    {
        return Cache::remember("movie.{$id}.credits", 3600, function () use ($id) {
            return $this->request("/movie/{$id}/credits");
        });
    }

    /**
     * Get similar movies
     */
    public function getSimilarMovies(int $id): ?array
    {
        return Cache::remember("movie.{$id}.similar", 3600, function () use ($id) {
            return $this->request("/movie/{$id}/similar");
        });
    }

    // ==================== TV SHOWS ====================

    /**
     * Get popular TV shows
     */
    public function getPopularTvShows(int $page = 1): ?array
    {
        return Cache::remember("tv.popular.{$page}", 3600, function () use ($page) {
            return $this->request('/tv/popular', ['page' => $page]);
        });
    }

    /**
     * Get top rated TV shows
     */
    public function getTopRatedTvShows(int $page = 1): ?array
    {
        return Cache::remember("tv.top_rated.{$page}", 3600, function () use ($page) {
            return $this->request('/tv/top_rated', ['page' => $page]);
        });
    }

    /**
     * Get on the air TV shows
     */
    public function getOnTheAirTvShows(int $page = 1): ?array
    {
        return Cache::remember("tv.on_the_air.{$page}", 3600, function () use ($page) {
            return $this->request('/tv/on_the_air', ['page' => $page]);
        });
    }

    /**
     * Get airing today TV shows
     */
    public function getAiringTodayTvShows(int $page = 1): ?array
    {
        return Cache::remember("tv.airing_today.{$page}", 3600, function () use ($page) {
            return $this->request('/tv/airing_today', ['page' => $page]);
        });
    }

    /**
     * Get trending TV shows
     */
    public function getTrendingTvShows(string $timeWindow = 'week'): ?array
    {
        return Cache::remember("tv.trending.{$timeWindow}", 3600, function () use ($timeWindow) {
            return $this->request("/trending/tv/{$timeWindow}");
        });
    }

    /**
     * Get TV show details
     */
    public function getTvShowDetails(int $id): ?array
    {
        return Cache::remember("tv.{$id}", 3600, function () use ($id) {
            return $this->request("/tv/{$id}", [
                'append_to_response' => 'credits,videos,similar,recommendations'
            ]);
        });
    }

    /**
     * Get TV show season details
     */
    public function getTvSeasonDetails(int $tvId, int $seasonNumber): ?array
    {
        return Cache::remember("tv.{$tvId}.season.{$seasonNumber}", 3600, function () use ($tvId, $seasonNumber) {
            return $this->request("/tv/{$tvId}/season/{$seasonNumber}");
        });
    }

    /**
     * Search TV shows
     */
    public function searchTvShows(string $query, int $page = 1): ?array
    {
        return $this->request('/search/tv', [
            'query' => $query,
            'page' => $page
        ]);
    }

    /**
     * Get TV credits
     */
    public function getTvCredits(int $id): ?array
    {
        return Cache::remember("tv.{$id}.credits", 3600, function () use ($id) {
            return $this->request("/tv/{$id}/credits");
        });
    }

    /**
     * Get similar TV shows
     */
    public function getSimilarTvShows(int $id): ?array
    {
        return Cache::remember("tv.{$id}.similar", 3600, function () use ($id) {
            return $this->request("/tv/{$id}/similar");
        });
    }

    // ==================== ANIME ====================
    // Anime uses TV shows endpoint with genre filter (Animation = 16)

    /**
     * Get popular anime
     */
    public function getPopularAnime(int $page = 1): ?array
    {
        return Cache::remember("anime.popular.{$page}", 3600, function () use ($page) {
            return $this->request('/discover/tv', [
                'with_genres' => '16',
                'with_original_language' => 'ja',
                'sort_by' => 'popularity.desc',
                'page' => $page
            ]);
        });
    }

    /**
     * Get Filipino / Tagalog movies (discover by original language 'tl')
     */
    public function getFilipinoMovies(int $page = 1): ?array
    {
        return Cache::remember("movies.filipino.{$page}", 3600, function () use ($page) {
            return $this->request('/discover/movie', [
                'with_original_language' => 'tl',
                'sort_by' => 'popularity.desc',
                'page' => $page,
            ]);
        });
    }

    /**
     * Get Filipino / Tagalog TV shows (discover by original language 'tl')
     */
    public function getFilipinoTvShows(int $page = 1): ?array
    {
        return Cache::remember("tv.filipino.{$page}", 3600, function () use ($page) {
            return $this->request('/discover/tv', [
                'with_original_language' => 'tl',
                'sort_by' => 'popularity.desc',
                'page' => $page,
            ]);
        });
    }

    /**
     * Get top rated anime
     */
    public function getTopRatedAnime(int $page = 1): ?array
    {
        return Cache::remember("anime.top_rated.{$page}", 3600, function () use ($page) {
            return $this->request('/discover/tv', [
                'with_genres' => '16',
                'with_original_language' => 'ja',
                'sort_by' => 'vote_average.desc',
                'vote_count.gte' => 100,
                'page' => $page
            ]);
        });
    }

    /**
     * Get airing anime
     */
    public function getAiringAnime(int $page = 1): ?array
    {
        return Cache::remember("anime.airing.{$page}", 3600, function () use ($page) {
            return $this->request('/discover/tv', [
                'with_genres' => '16',
                'with_original_language' => 'ja',
                'with_status' => '0',
                'sort_by' => 'popularity.desc',
                'page' => $page
            ]);
        });
    }

    /**
     * Get anime details (uses TV show endpoint)
     */
    public function getAnimeDetails(int $id): ?array
    {
        return $this->getTvShowDetails($id);
    }

    // ==================== MULTI SEARCH ====================

    /**
     * Multi search (movies, TV, people)
     */
    public function multiSearch(string $query, int $page = 1): ?array
    {
        return $this->request('/search/multi', [
            'query' => $query,
            'page' => $page
        ]);
    }

    // ==================== GENRES ====================

    /**
     * Get movie genres
     */
    public function getMovieGenres(): ?array
    {
        return Cache::remember('genres.movie', 86400, function () {
            return $this->request('/genre/movie/list');
        });
    }

    /**
     * Get movie images (posters, backdrops, logos)
     */
    public function getMovieImages(int $id): ?array
    {
        return Cache::remember("movie.{$id}.images", 3600, function () use ($id) {
            return $this->request("/movie/{$id}/images");
        });
    }

    /**
     * Return a preferred movie logo file path (tries country first, falls back to first available)
     */
    public function getMovieLogoPath(int $id, string $country = 'US'): ?string
    {
        $images = $this->getMovieImages($id);

        if (!$images || empty($images['logos'])) {
            return null;
        }

        // Try to find a logo matching the requested country
        foreach ($images['logos'] as $logo) {
            if (!empty($logo['iso_3166_1']) && strtoupper($logo['iso_3166_1']) === strtoupper($country)) {
                return $logo['file_path'] ?? null;
            }
        }

        // Fallback to first available logo
        return $images['logos'][0]['file_path'] ?? null;
    }

    /**
     * Return a fully-qualified logo image URL or empty string
     */
    /**
     * Return a fully-qualified logo image URL or empty string.
     * By default this will return a wsrv.nl proxied URL (webp, quality 80).
     * Set $useProxy = false to return the direct TMDB image URL instead.
     *
     * @param int $id
     * @param string $country
     * @param string $size    TMDB size token like 'w500' or numeric width like '280'
     * @param bool $useProxy
     * @return string
     */
    public function getMovieLogoUrl(int $id, string $country = 'US', string $size = 'w500', bool $useProxy = true): string
    {
        $path = $this->getMovieLogoPath($id, $country);
        if (!$path) {
            return '';
        }

        // If caller explicitly wants the direct TMDB URL, return it
        if (!$useProxy) {
            return $this->getImageUrl($path, $size);
        }

    // Build the TMDB image URL first.
    // Use the original (full-size) TMDB image when proxying so the proxy can
    // reliably resize to the requested width. Requesting a non-supported
    // TMDB size token (e.g. w280) may return 404 from TMDB, which then
    // causes the proxy to fail. Using 'original' avoids that.
    $tmdbUrl = $this->getImageUrl($path, 'original');

        // Determine numeric width for wsrv from TMDB size token or numeric size
        $width = 0;
        // If size is like 'w500' extract digits
        if (preg_match('/^w(\d+)$/i', $size, $m)) {
            $width = (int) $m[1];
        } elseif (is_numeric($size)) {
            $width = (int) $size;
        }

        // Fallback width if parsing failed
        if ($width <= 0) {
            $width = 500;
        }

        $encoded = rawurlencode($tmdbUrl);
        // Use wsrv.nl to proxy/resize and deliver webp; matches the sample link pattern
        return "https://wsrv.nl/?url={$encoded}&w=280&h=150&output=webp&q=80";
    }

    /**
     * Get TV genres
     */
    public function getTvGenres(): ?array
    {
        return Cache::remember('genres.tv', 86400, function () {
            return $this->request('/genre/tv/list');
        });
    }

    /**
     * Get TV images (posters, backdrops, logos)
     */
    public function getTvImages(int $id): ?array
    {
        return Cache::remember("tv.{$id}.images", 3600, function () use ($id) {
            return $this->request("/tv/{$id}/images");
        });
    }

    /**
     * Return a preferred TV logo file path (tries country first, falls back to first available)
     */
    public function getTvLogoPath(int $id, string $country = 'US'): ?string
    {
        $images = $this->getTvImages($id);

        if (!$images || empty($images['logos'])) {
            return null;
        }

        // Try to find a logo matching the requested country
        foreach ($images['logos'] as $logo) {
            if (!empty($logo['iso_3166_1']) && strtoupper($logo['iso_3166_1']) === strtoupper($country)) {
                return $logo['file_path'] ?? null;
            }
        }

        // Fallback to first available logo
        return $images['logos'][0]['file_path'] ?? null;
    }

    /**
     * Return a fully-qualified TV logo image URL or empty string.
     * By default this will return a wsrv.nl proxied URL (webp, quality 80).
     * Set $useProxy = false to return the direct TMDB image URL instead.
     */
    public function getTvLogoUrl(int $id, string $country = 'US', string $size = 'w500', bool $useProxy = true): string
    {
        $path = $this->getTvLogoPath($id, $country);
        if (!$path) {
            return '';
        }

        if (!$useProxy) {
            return $this->getImageUrl($path, $size);
        }

        $tmdbUrl = $this->getImageUrl($path, 'original');

        $width = 0;
        if (preg_match('/^w(\d+)$/i', $size, $m)) {
            $width = (int) $m[1];
        } elseif (is_numeric($size)) {
            $width = (int) $size;
        }

        if ($width <= 0) {
            $width = 500;
        }

        $encoded = rawurlencode($tmdbUrl);
        return "https://wsrv.nl/?url={$encoded}&w=280&output=webp&q=80";
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Banner;
use App\Models\WatchHistory;
use App\Services\TMDBService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends BaseApiController
{
    public function __construct(protected TMDBService $tmdb)
    {
    }

    /**
     * Get aggregated home feed data for the mobile app home screen.
     * GET /api/v1/home
     */
    public function index(Request $request): JsonResponse
    {
        // 1. Active Banners
        $banners = Banner::active()->map(function ($banner) {
            return [
                'id'          => $banner->id,
                'type'        => $banner->type,
                'title'       => $banner->title,
                'descr      iption' => $banner->description,
                'image'       => $banner->image,
                'icon'        => $banner->icon,
                'button_text' => $banner->button_text,
                'button_url'  => $banner->button_url,
                'hotlinks'    => $banner->hotlinks ?? [],
            ];
        });

        // 2. Curated TMDB Sections
        $trendingMovies = $this->tmdb->getTrendingMovies('day');
        $popularMovies = $this->tmdb->getPopularMovies(1);
        $topRatedMovies = $this->tmdb->getTopRatedMovies(1);
        $trendingTv = $this->tmdb->getTrendingTvShows('day');
        $popularTv = $this->tmdb->getPopularTvShows(1);
        $popularAnime = $this->tmdb->getPopularAnime(1);
        $filipinoMovies = $this->tmdb->getFilipinoMovies(1);

        // 3. Optional: Continue Watching for Authenticated User
        $user = Auth::guard('sanctum')->user();
        $continueWatching = [];

        if ($user) {
            $continueWatching = WatchHistory::where('user_id', $user->id)
                ->where('progress', '<', 95)
                ->recent()
                ->take(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'id'            => $item->media_id,
                        'media_id'      => $item->media_id,
                        'media_type'    => $item->media_type,
                        'title'         => $item->title,
                        'poster_url'    => $item->poster_url,
                        'backdrop_url'  => $item->backdrop_url,
                        'season'        => $item->season,
                        'episode'       => $item->episode,
                        'progress'      => $item->progress,
                        'watched_time'  => $item->watched_time,
                        'duration'      => $item->duration,
                        'formatted_time'=> $item->formatted_watch_time,
                        'last_watched'  => $item->updated_at?->toIso8601String(),
                    ];
                });
        }

        return $this->success([
            'banners'            => $banners,
            'continue_watching'  => $continueWatching,
            'trending_movies'    => $this->enrichMediaList($trendingMovies['results'] ?? [], $this->tmdb, 'movie'),
            'popular_movies'     => $this->enrichMediaList($popularMovies['results'] ?? [], $this->tmdb, 'movie'),
            'top_rated_movies'   => $this->enrichMediaList($topRatedMovies['results'] ?? [], $this->tmdb, 'movie'),
            'trending_tv'        => $this->enrichMediaList($trendingTv['results'] ?? [], $this->tmdb, 'tv'),
            'popular_tv'         => $this->enrichMediaList($popularTv['results'] ?? [], $this->tmdb, 'tv'),
            'popular_anime'      => $this->enrichMediaList($popularAnime['results'] ?? [], $this->tmdb, 'anime'),
            'filipino_movies'    => $this->enrichMediaList($filipinoMovies['results'] ?? [], $this->tmdb, 'movie'),
        ]);
    }
}

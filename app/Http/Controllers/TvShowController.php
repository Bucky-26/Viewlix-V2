<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use Illuminate\Http\Request;

class TvShowController extends Controller
{
    public function __construct(protected TMDBService $tmdb)
    {
    }

    public function index(Request $request)
    {
        $filter = $request->get('filter', 'popular');
        $page = $request->get('page', 1);

        $tvShows = match ($filter) {
            'top_rated' => $this->tmdb->getTopRatedTvShows($page),
            'on_the_air' => $this->tmdb->getOnTheAirTvShows($page),
            'airing_today' => $this->tmdb->getAiringTodayTvShows($page),
            default => $this->tmdb->getPopularTvShows($page),
        };

        return view('pages.tv-shows', [
            'tvShows' => $tvShows['results'] ?? [],
            'currentPage' => $tvShows['page'] ?? 1,
            'totalPages' => $tvShows['total_pages'] ?? 1,
            'filter' => $filter,
            'tmdb' => $this->tmdb,
        ]);
    }

    public function show(int $id)
    {
        $tvShow = $this->tmdb->getTvShowDetails($id);

        if (!$tvShow) {
            abort(404);
        }

        $credits = $this->tmdb->getTvCredits($id);
        $similar = $this->tmdb->getSimilarTvShows($id);

        return view('pages.tv-show-detail', [
            'tvShow' => $tvShow,
            'credits' => $credits ?? [],
            'similar' => $similar['results'] ?? [],
            'posterUrl' => $this->tmdb->getImageUrl($tvShow['poster_path'] ?? null, 'w500'),
            'backdropUrl' => $this->tmdb->getImageUrl($tvShow['backdrop_path'] ?? null, 'original'),
            'tmdb' => $this->tmdb,
        ]);
    }

    public function season(int $id, int $seasonNumber)
    {
        $tvShow = $this->tmdb->getTvShowDetails($id);
        $season = $this->tmdb->getTvSeasonDetails($id, $seasonNumber);

        if (!$tvShow || !$season) {
            abort(404);
        }

        return view('pages.tv-show-season', [
            'tvShow' => $tvShow,
            'season' => $season,
            'episodes' => $season['episodes'] ?? [],
            'tmdb' => $this->tmdb,
        ]);
    }

    public function watch(int $id, int $season = 1, int $episode = 1)
    {
        $tvShow = $this->tmdb->getTvShowDetails($id);
        $seasonData = $this->tmdb->getTvSeasonDetails($id, $season);

        if (!$tvShow) {
            abort(404);
        }

        // VidLink embed URL for TV: https://vidlink.pro/tv/{tmdb_id}/{season}/{episode}
        $embedUrl = config('services.vidlink.base_url') . "/tv/{$id}/{$season}/{$episode}";

        return view('pages.watch', [
            'type' => 'tv',
            'id' => $id,
            'title' => $tvShow['name'] ?? 'Unknown',
            'media' => $tvShow,
            'season' => $season,
            'episode' => $episode,
            'episodes' => $seasonData['episodes'] ?? [],
            'embedUrl' => $embedUrl,
            'tmdb' => $this->tmdb,
        ]);
    }
}

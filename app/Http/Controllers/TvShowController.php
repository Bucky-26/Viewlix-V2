<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use Illuminate\Http\Request;
use RalphJSmit\Laravel\SEO\Support\SEOData;

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

        $filterTitle = match($filter) {
            'top_rated' => 'Top Rated TV Shows',
            'on_the_air' => 'TV Shows On The Air',
            'airing_today' => 'TV Shows Airing Today',
            default => 'Popular TV Shows',
        };

        return view('pages.tv-shows', [
            'tvShows' => $tvShows['results'] ?? [],
            'currentPage' => $tvShows['page'] ?? 1,
            'totalPages' => $tvShows['total_pages'] ?? 1,
            'filter' => $filter,
            'tmdb' => $this->tmdb,
            'SEOData' => new SEOData(
                title: "{$filterTitle} - Viewlix",
                description: "Browse {$filterTitle} on Viewlix. Discover the best TV series to watch online.",
                url: route('tv-shows', ['filter' => $filter]),
            ),
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

        $title = $tvShow['name'] ?? 'TV Show';
        $description = $tvShow['overview'] ?? "Watch {$title} on Viewlix. Stream TV shows online in HD quality.";
        $imageUrl = $this->tmdb->getImageUrl($tvShow['poster_path'] ?? null, 'w500');
        $releaseYear = !empty($tvShow['first_air_date']) ? \Carbon\Carbon::parse($tvShow['first_air_date'])->format('Y') : '';

        return view('pages.tv-show-detail', [
            'tvShow' => $tvShow,
            'credits' => $credits ?? [],
            'similar' => $similar['results'] ?? [],
            'posterUrl' => $imageUrl,
            'backdropUrl' => $this->tmdb->getImageUrl($tvShow['backdrop_path'] ?? null, 'original'),
            'tmdb' => $this->tmdb,
            'SEOData' => new SEOData(
                title: "{$title}" . ($releaseYear ? " ({$releaseYear})" : '') . " - Viewlix",
                description: $description,
                url: route('tv-show.show', ['id' => $id]),
                image: $imageUrl ?: null,
                type: 'website',
            ),
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

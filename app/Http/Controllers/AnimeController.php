<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use Illuminate\Http\Request;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class AnimeController extends Controller
{
    public function __construct(protected TMDBService $tmdb)
    {
    }

    public function index(Request $request)
    {
        $filter = $request->get('filter', 'popular');
        $page = $request->get('page', 1);

        $anime = match ($filter) {
            'top_rated' => $this->tmdb->getTopRatedAnime($page),
            'airing' => $this->tmdb->getAiringAnime($page),
            default => $this->tmdb->getPopularAnime($page),
        };

        $filterTitle = match($filter) {
            'top_rated' => 'Top Rated Anime',
            'airing' => 'Airing Anime',
            default => 'Popular Anime',
        };

        return view('pages.anime', [
            'animeList' => $anime['results'] ?? [],
            'currentPage' => $anime['page'] ?? 1,
            'totalPages' => $anime['total_pages'] ?? 1,
            'filter' => $filter,
            'tmdb' => $this->tmdb,
            'SEOData' => new SEOData(
                title: "{$filterTitle} - Viewlix",
                description: "Browse {$filterTitle} on Viewlix. Discover the best anime series to watch online.",
                url: route('anime', ['filter' => $filter]),
            ),
        ]);
    }

    public function show(int $id, Request $request)
    {
        $anime = $this->tmdb->getAnimeDetails($id);

        if (!$anime) {
            abort(404);
        }

        $currentSeason = $request->get('season', 1);
        $seasonData = $this->tmdb->getTvSeasonDetails($id, $currentSeason);
        $similar = $this->tmdb->getSimilarTvShows($id);

        $title = $anime['name'] ?? 'Anime';
        $description = $anime['overview'] ?? "Watch {$title} on Viewlix. Stream anime online in HD quality.";
        $imageUrl = $this->tmdb->getImageUrl($anime['poster_path'] ?? null, 'w500');
        $releaseYear = !empty($anime['first_air_date']) ? \Carbon\Carbon::parse($anime['first_air_date'])->format('Y') : '';

        return view('pages.anime-detail', [
            'anime' => $anime,
            'currentSeason' => $currentSeason,
            'episodes' => $seasonData['episodes'] ?? [],
            'similar' => $similar['results'] ?? [],
            'posterUrl' => $imageUrl,
            'backdropUrl' => $this->tmdb->getImageUrl($anime['backdrop_path'] ?? null, 'original'),
            'tmdb' => $this->tmdb,
            'SEOData' => new SEOData(
                title: "{$title}" . ($releaseYear ? " ({$releaseYear})" : '') . " - Viewlix",
                description: $description,
                url: route('anime.show', ['id' => $id]),
                image: $imageUrl ?: null,
                type: 'website',
            ),
        ]);
    }

    public function watch(int $id, int $season = 1, int $episode = 1)
    {
        $anime = $this->tmdb->getAnimeDetails($id);

        if (!$anime) {
            abort(404);
        }

        $seasonData = $this->tmdb->getTvSeasonDetails($id, $season);

        // VidLink embed URL for anime (same as TV): https://vidlink.pro/tv/{tmdb_id}/{season}/{episode}
        $embedUrl = config('services.vidlink.base_url') . "/tv/{$id}/{$season}/{$episode}";

        return view('pages.watch', [
            'type' => 'anime',
            'id' => $id,
            'title' => $anime['name'] ?? 'Unknown',
            'media' => $anime,
            'season' => $season,
            'episode' => $episode,
            'episodes' => $seasonData['episodes'] ?? [],
            'embedUrl' => $embedUrl,
            'tmdb' => $this->tmdb,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use App\Models\VideoSource;
use Illuminate\Http\Request;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Illuminate\Support\Str;

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

    public function show(string $slug)
    {
        $parts = explode('-', $slug);
        $id = (int) ($parts[0] ?? 0);

        if ($id <= 0) {
            abort(404);
        }

        $tvShow = $this->tmdb->getTvShowDetails($id);

        if (!$tvShow) {
            abort(404);
        }

        $episodes = [];
        $firstSeasonNumber = 1;
        if (!empty($tvShow['seasons'])) {
            foreach ($tvShow['seasons'] as $s) {
                if (($s['season_number'] ?? 0) > 0) {
                    $firstSeasonNumber = $s['season_number'];
                    break;
                }
            }
            $seasonData = $this->tmdb->getTvSeasonDetails($id, $firstSeasonNumber);
            $episodes = $seasonData['episodes'] ?? [];
        }

        $credits = $this->tmdb->getTvCredits($id);
        $similar = $this->tmdb->getSimilarTvShows($id);

        $title = $tvShow['name'] ?? 'TV Show';
        $description = $tvShow['overview'] ?? "Watch {$title} on Viewlix. Stream TV shows online in HD quality.";
    $imageUrl = $this->tmdb->getImageUrl($tvShow['poster_path'] ?? null, 'w500', true);
        $releaseYear = !empty($tvShow['first_air_date']) ? \Carbon\Carbon::parse($tvShow['first_air_date'])->format('Y') : '';

        return view('pages.tv-show-detail', [
            'tvShow' => $tvShow,
            'episodes' => $episodes,
            'firstSeasonNumber' => $firstSeasonNumber,
            'credits' => $credits ?? [],
            'similar' => $similar['results'] ?? [],
            'posterUrl' => $imageUrl,
            'backdropUrl' => $this->tmdb->getImageUrl($tvShow['backdrop_path'] ?? null, 'original', true),
            'tmdb' => $this->tmdb,
            'SEOData' => new SEOData(
                title: "{$title}" . ($releaseYear ? " ({$releaseYear})" : '') . " - Viewlix",
                description: $description,
                url: route('tv-show.show', ['slug' => $id . '-' . Str::slug($title)]),
                image: $imageUrl ?: null,
                type: 'website',
            ),
        ]);
    }

    public function season(string $slug, int $seasonNumber)
    {
        $parts = explode('-', $slug);
        $id = (int) ($parts[0] ?? 0);

        if ($id <= 0) {
            abort(404);
        }

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

    public function seasonEpisodesJson(string $slug, int $seasonNumber)
    {
        $parts = explode('-', $slug);
        $id = (int) ($parts[0] ?? 0);

        if ($id <= 0) {
            return response()->json(['error' => 'Invalid ID'], 400);
        }

        $season = $this->tmdb->getTvSeasonDetails($id, $seasonNumber);

        if (!$season) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json([
            'episodes' => $season['episodes'] ?? []
        ]);
    }

    public function watch(string $slug, int $season = 1, int $episode = 1)
    {
        $parts = explode('-', $slug);
        $id = (int) ($parts[0] ?? 0);

        if ($id <= 0) {
            abort(404);
        }

        $tvShow = $this->tmdb->getTvShowDetails($id);
        $seasonData = $this->tmdb->getTvSeasonDetails($id, $season);

        if (!$tvShow) {
            abort(404);
        }

        // Dynamic Video Sources from Database
        $sourceData = VideoSource::getSourcesForContent('tv', $id, $season, $episode, $slug);
        $sources = $sourceData['sources'];
        $embedUrl = $sourceData['default_embed'] ?? ($sources[0]['embed'] ?? '');

        // Valid seasons list
        $seasons = array_values(array_filter($tvShow['seasons'] ?? [], function ($s) {
            return ($s['season_number'] ?? 0) > 0;
        }));

        // Locate current, previous, and next episode objects
        $episodesList = $seasonData['episodes'] ?? [];
        $currentEpisode = null;
        $prevEpisode = null;
        $nextEpisode = null;

        foreach ($episodesList as $idx => $ep) {
            if ((int) ($ep['episode_number'] ?? 0) === (int) $episode) {
                $currentEpisode = $ep;
                $prevEpisode = $episodesList[$idx - 1] ?? null;
                $nextEpisode = $episodesList[$idx + 1] ?? null;
                break;
            }
        }

        if (!$currentEpisode && !empty($episodesList)) {
            $currentEpisode = $episodesList[0];
            $nextEpisode = $episodesList[1] ?? null;
        }

        $credits = $this->tmdb->getTvCredits($id);
        $similar = $this->tmdb->getSimilarTvShows($id);

        return view('pages.watch', [
            'type' => 'tv',
            'id' => $id,
            'slug' => $slug,
            'title' => $tvShow['name'] ?? 'Unknown',
            'media' => $tvShow,
            'season' => $season,
            'episode' => $episode,
            'seasons' => $seasons,
            'currentSeason' => $seasonData,
            'episodes' => $episodesList,
            'currentEpisode' => $currentEpisode,
            'prevEpisode' => $prevEpisode,
            'nextEpisode' => $nextEpisode,
            'credits' => $credits ?? [],
            'similar' => $similar['results'] ?? [],
            'embedUrl' => $embedUrl,
            'sources' => $sources,
            'tmdb' => $this->tmdb,
        ]);
    }
}

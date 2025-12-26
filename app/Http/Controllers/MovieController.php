<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use Illuminate\Http\Request;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class MovieController extends Controller
{
    public function __construct(protected TMDBService $tmdb)
    {
    }

    public function index(Request $request)
    {
        $filter = $request->get('filter', 'popular');
        $page = $request->get('page', 1);

        $movies = match ($filter) {
            'top_rated' => $this->tmdb->getTopRatedMovies($page),
            'now_playing' => $this->tmdb->getNowPlayingMovies($page),
            'upcoming' => $this->tmdb->getUpcomingMovies($page),
            default => $this->tmdb->getPopularMovies($page),
        };

        $filterTitle = match($filter) {
            'top_rated' => 'Top Rated Movies',
            'now_playing' => 'Now Playing Movies',
            'upcoming' => 'Upcoming Movies',
            default => 'Popular Movies',
        };

        return view('pages.movies', [
            'movies' => $movies['results'] ?? [],
            'currentPage' => $movies['page'] ?? 1,
            'totalPages' => $movies['total_pages'] ?? 1,
            'filter' => $filter,
            'tmdb' => $this->tmdb,
            'SEOData' => new SEOData(
                title: "{$filterTitle} - Viewlix",
                description: "Browse {$filterTitle} on Viewlix. Discover the best movies to watch online.",
                url: route('movies', ['filter' => $filter]),
            ),
        ]);
    }

    public function show(int $id)
    {
        $movie = $this->tmdb->getMovieDetails($id);

        if (!$movie) {
            abort(404);
        }

        $credits = $this->tmdb->getMovieCredits($id);
        $similar = $this->tmdb->getSimilarMovies($id);

        $title = $movie['title'] ?? 'Movie';
        $description = $movie['overview'] ?? "Watch {$title} on Viewlix. Stream movies online in HD quality.";
        $imageUrl = $this->tmdb->getImageUrl($movie['poster_path'] ?? null, 'w500');
        $releaseYear = !empty($movie['release_date']) ? \Carbon\Carbon::parse($movie['release_date'])->format('Y') : '';

        return view('pages.movie-detail', [
            'movie' => $movie,
            'credits' => $credits ?? [],
            'similar' => $similar['results'] ?? [],
            'posterUrl' => $imageUrl,
            'backdropUrl' => $this->tmdb->getImageUrl($movie['backdrop_path'] ?? null, 'original'),
            'tmdb' => $this->tmdb,
            'SEOData' => new SEOData(
                title: "{$title}" . ($releaseYear ? " ({$releaseYear})" : '') . " - Viewlix",
                description: $description,
                url: route('movie.show', ['id' => $id]),
                image: $imageUrl ?: null,
                type: 'website',
            ),
        ]);
    }

    public function watch(int $id)
    {
        $movie = $this->tmdb->getMovieDetails($id);

        if (!$movie) {
            abort(404);
        }

        // VidLink embed URL for movies: https://vidlink.pro/movie/{tmdb_id}
        $embedUrl = config('services.vidlink.base_url') . "/movie/{$id}";

        // Build multiple sources: VidLink (primary) + Vidsrcme API as an alternate source.
        // Assumption: Vidsrcme accepts a movie endpoint like: https://vidsrcme.ru/api/movie/{tmdb_id}
        // If the API expects a different path or needs server-side fetching, this can be adjusted later.
        $sources = [
            ['name' => 'VidLink', 'embed' => $embedUrl],
            ['name' => 'Vidsrcme', 'embed' => "https://vidsrc.net/api/movie/{$id}"],
        ];

        return view('pages.watch', [
            'type' => 'movie',
            'id' => $id,
            'title' => $movie['title'] ?? 'Unknown',
            'media' => $movie,
            'embedUrl' => $embedUrl,
            'sources' => $sources,
            'tmdb' => $this->tmdb,
        ]);
    }
}

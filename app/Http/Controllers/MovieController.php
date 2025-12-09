<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use Illuminate\Http\Request;

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

        return view('pages.movies', [
            'movies' => $movies['results'] ?? [],
            'currentPage' => $movies['page'] ?? 1,
            'totalPages' => $movies['total_pages'] ?? 1,
            'filter' => $filter,
            'tmdb' => $this->tmdb,
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

        return view('pages.movie-detail', [
            'movie' => $movie,
            'credits' => $credits ?? [],
            'similar' => $similar['results'] ?? [],
            'posterUrl' => $this->tmdb->getImageUrl($movie['poster_path'] ?? null, 'w500'),
            'backdropUrl' => $this->tmdb->getImageUrl($movie['backdrop_path'] ?? null, 'original'),
            'tmdb' => $this->tmdb,
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

        return view('pages.watch', [
            'type' => 'movie',
            'id' => $id,
            'title' => $movie['title'] ?? 'Unknown',
            'media' => $movie,
            'embedUrl' => $embedUrl,
            'tmdb' => $this->tmdb,
        ]);
    }
}

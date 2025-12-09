<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(protected TMDBService $tmdb)
    {
    }

    public function index()
    {
        $trendingMovies = $this->tmdb->getTrendingMovies('day');
        $topRatedMovies = $this->tmdb->getTopRatedMovies();
        $trendingTv = $this->tmdb->getTrendingTvShows('day');
        $popularAnime = $this->tmdb->getPopularAnime();

        return view('welcome', [
            'trendingMovies' => $trendingMovies['results'] ?? [],
            'topRatedMovies' => $topRatedMovies['results'] ?? [],
            'trendingTv' => $trendingTv['results'] ?? [],
            'popularAnime' => $popularAnime['results'] ?? [],
            'tmdb' => $this->tmdb,
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $filter = $request->get('filter');
        $page = $request->get('page', 1);
        
        if (empty($query)) {
            return redirect()->route('home');
        }

        // Use specific search based on filter
        if ($filter === 'movie') {
            $results = $this->tmdb->searchMovies($query, $page);
        } elseif ($filter === 'tv') {
            $results = $this->tmdb->searchTvShows($query, $page);
        } else {
            $results = $this->tmdb->multiSearch($query, $page);
        }

        return view('pages.search', [
            'query' => $query,
            'filter' => $filter,
            'results' => $results['results'] ?? [],
            'currentPage' => $results['page'] ?? 1,
            'totalPages' => $results['total_pages'] ?? 1,
            'tmdb' => $this->tmdb,
        ]);
    }
}

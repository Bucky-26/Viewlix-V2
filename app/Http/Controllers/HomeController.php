<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use Illuminate\Http\Request;
use RalphJSmit\Laravel\SEO\Support\SEOData;

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
            'SEOData' => new SEOData(
                title: 'Viewlix - Watch Movies, TV Shows & Anime Online',
                description: 'Discover and watch the best movies, TV shows, and anime. Browse trending content, top-rated films, and popular series. Your ultimate streaming destination.',
                url: route('home'),
                site_name: 'Viewlix',
            ),
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
            'SEOData' => new SEOData(
                title: "Search Results for \"{$query}\" - Viewlix",
                description: "Search results for \"{$query}\" on Viewlix. Find movies, TV shows, and anime.",
                url: route('search', ['q' => $query, 'filter' => $filter]),
            ),
        ]);
    }

    /**
     * Return JSON suggestions for the search autocomplete.
     */
    public function suggest(Request $request)
    {
        $query = (string) $request->get('q', '');

        if (trim($query) === '') {
            return response()->json(['suggestions' => []]);
        }

        // Use TMDB multi search for quick suggestions (first page)
        $results = $this->tmdb->multiSearch($query, 1);

        $suggestions = [];

        foreach (array_slice($results['results'] ?? [], 0, 8) as $item) {
            $type = $item['media_type'] ?? null;
            $title = $item['title'] ?? $item['name'] ?? null;

            if (!$title) continue;

            // Determine a friendly link for the suggestion
            $link = route('search', ['q' => $title]);
            if ($type === 'movie') {
                $link = route('movie.show', ['id' => $item['id']]);
            } elseif ($type === 'tv') {
                $link = route('tv-show.show', ['id' => $item['id']]);
            }

            $image = $this->tmdb->getImageUrl($item['poster_path'] ?? $item['profile_path'] ?? null, 'w92');

            $suggestions[] = [
                'id' => $item['id'] ?? null,
                'title' => $title,
                'type' => $type,
                'image' => $image,
                'link' => $link,
            ];
        }

        return response()->json(['suggestions' => $suggestions]);
    }

    /**
     * Filipino hub page: Tagalog movies and TV series
     */
    public function filipino(Request $request)
    {
        $pageMovies = (int) $request->get('movies_page', 1);
        $pageTv = (int) $request->get('tv_page', 1);

        $movies = $this->tmdb->getFilipinoMovies($pageMovies);
        $tvShows = $this->tmdb->getFilipinoTvShows($pageTv);

        return view('pages.filipino', [
            'movies' => $movies['results'] ?? [],
            'tvShows' => $tvShows['results'] ?? [],
            'tmdb' => $this->tmdb,
            'SEOData' => new SEOData(
                title: 'Filipino Movies & Series - Viewlix',
                description: 'Browse Tagalog (Filipino) movies and TV series on Viewlix. Discover the latest and most popular Filipino titles.',
                url: route('filipino'),
            ),
        ]);
    }
}

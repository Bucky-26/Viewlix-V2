<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

class SitemapController extends Controller
{
    public function __construct(protected TMDBService $tmdb)
    {
    }

    public function index(): Response
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        $baseUrl = config('app.url');
        
        // Main sitemap
        $sitemap .= '  <sitemap>' . "\n";
        $sitemap .= "    <loc>{$baseUrl}/sitemap-main.xml</loc>\n";
        $sitemap .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
        $sitemap .= '  </sitemap>' . "\n";
        
        // Movies sitemap
        $sitemap .= '  <sitemap>' . "\n";
        $sitemap .= "    <loc>{$baseUrl}/sitemap-movies.xml</loc>\n";
        $sitemap .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
        $sitemap .= '  </sitemap>' . "\n";
        
        // TV Shows sitemap
        $sitemap .= '  <sitemap>' . "\n";
        $sitemap .= "    <loc>{$baseUrl}/sitemap-tv-shows.xml</loc>\n";
        $sitemap .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
        $sitemap .= '  </sitemap>' . "\n";
        
        // Anime sitemap
        $sitemap .= '  <sitemap>' . "\n";
        $sitemap .= "    <loc>{$baseUrl}/sitemap-anime.xml</loc>\n";
        $sitemap .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
        $sitemap .= '  </sitemap>' . "\n";
        
        $sitemap .= '</sitemapindex>';
        
        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml');
    }

    public function main(): Response
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        $baseUrl = config('app.url');
        $urls = [
            ['url' => route('home'), 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => route('movies'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => route('tv-shows'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => route('anime'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => route('terms'), 'priority' => '0.3', 'changefreq' => 'monthly'],
            ['url' => route('privacy'), 'priority' => '0.3', 'changefreq' => 'monthly'],
        ];
        
        foreach ($urls as $urlData) {
            $sitemap .= '  <url>' . "\n";
            $sitemap .= "    <loc>{$urlData['url']}</loc>\n";
            $sitemap .= "    <priority>{$urlData['priority']}</priority>\n";
            $sitemap .= "    <changefreq>{$urlData['changefreq']}</changefreq>\n";
            $sitemap .= "    <lastmod>" . date('Y-m-d') . "</lastmod>\n";
            $sitemap .= '  </url>' . "\n";
        }
        
        $sitemap .= '</urlset>';
        
        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml');
    }

    public function movies(): Response
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        $baseUrl = config('app.url');
        
        // Get popular movies (first 3 pages = 60 movies)
        for ($page = 1; $page <= 3; $page++) {
            $movies = $this->tmdb->getPopularMovies($page);
            if (isset($movies['results'])) {
                foreach ($movies['results'] as $movie) {
                    if (isset($movie['id'])) {
                        $sitemap .= '  <url>' . "\n";
                        $sitemap .= "    <loc>{$baseUrl}/movie/{$movie['id']}</loc>\n";
                        $sitemap .= "    <priority>0.8</priority>\n";
                        $sitemap .= "    <changefreq>weekly</changefreq>\n";
                        $lastmod = !empty($movie['release_date']) ? date('Y-m-d', strtotime($movie['release_date'])) : date('Y-m-d');
                        $sitemap .= "    <lastmod>{$lastmod}</lastmod>\n";
                        $sitemap .= '  </url>' . "\n";
                    }
                }
            }
        }
        
        $sitemap .= '</urlset>';
        
        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml');
    }

    public function tvShows(): Response
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        $baseUrl = config('app.url');
        
        // Get popular TV shows (first 3 pages = 60 shows)
        for ($page = 1; $page <= 3; $page++) {
            $tvShows = $this->tmdb->getPopularTvShows($page);
            if (isset($tvShows['results'])) {
                foreach ($tvShows['results'] as $tvShow) {
                    if (isset($tvShow['id'])) {
                        $sitemap .= '  <url>' . "\n";
                        $sitemap .= "    <loc>{$baseUrl}/tv-show/{$tvShow['id']}</loc>\n";
                        $sitemap .= "    <priority>0.8</priority>\n";
                        $sitemap .= "    <changefreq>weekly</changefreq>\n";
                        $lastmod = !empty($tvShow['first_air_date']) ? date('Y-m-d', strtotime($tvShow['first_air_date'])) : date('Y-m-d');
                        $sitemap .= "    <lastmod>{$lastmod}</lastmod>\n";
                        $sitemap .= '  </url>' . "\n";
                    }
                }
            }
        }
        
        $sitemap .= '</urlset>';
        
        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml');
    }

    public function anime(): Response
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        $baseUrl = config('app.url');
        
        // Get popular anime (first 3 pages = 60 anime)
        for ($page = 1; $page <= 3; $page++) {
            $anime = $this->tmdb->getPopularAnime($page);
            if (isset($anime['results'])) {
                foreach ($anime['results'] as $animeItem) {
                    if (isset($animeItem['id'])) {
                        $sitemap .= '  <url>' . "\n";
                        $sitemap .= "    <loc>{$baseUrl}/anime/{$animeItem['id']}</loc>\n";
                        $sitemap .= "    <priority>0.8</priority>\n";
                        $sitemap .= "    <changefreq>weekly</changefreq>\n";
                        $lastmod = !empty($animeItem['first_air_date']) ? date('Y-m-d', strtotime($animeItem['first_air_date'])) : date('Y-m-d');
                        $sitemap .= "    <lastmod>{$lastmod}</lastmod>\n";
                        $sitemap .= '  </url>' . "\n";
                    }
                }
            }
        }
        
        $sitemap .= '</urlset>';
        
        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml');
    }

    public function robots(): Response
    {
        $robots = "User-agent: *\n";
        $robots .= "Allow: /\n";
        $robots .= "Disallow: /admin/\n";
        $robots .= "Disallow: /dashboard\n";
        $robots .= "Disallow: /account\n";
        $robots .= "Disallow: /favorites\n";
        $robots .= "Disallow: /watch-history\n";
        $robots .= "Disallow: /profile\n";
        $robots .= "Disallow: /movie/*/watch\n";
        $robots .= "Disallow: /tv-show/*/watch\n";
        $robots .= "Disallow: /anime/*/watch\n";
        $robots .= "Disallow: /api/\n";
        $robots .= "Disallow: /firebase-token\n";
        $robots .= "\n";
        $robots .= "Sitemap: " . config('app.url') . "/sitemap.xml\n";
        
        return response($robots, 200)
            ->header('Content-Type', 'text/plain');
    }
}


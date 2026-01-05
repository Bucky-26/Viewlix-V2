<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client as GuzzleClient;

class DownloadController extends Controller
{
    private $downloadApiBase = 'https://lawngreen-lapwing-922867.hostingersite.com';

    /**
     * Get download options for a movie
     */
    public function getMovieDownloads(Request $request, $tmdbId)
    {
        try {
            $response = Http::timeout(30)
                ->post("{$this->downloadApiBase}/dl/movie/{$tmdbId}");

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'ok' => false,
                'error' => 'Failed to fetch downloads'
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Proxy a remote file and stream it to the client while setting Referer.
     * Accepts a `url` query parameter (fully-qualified) and optional `filename`.
     * This prevents the browser from needing to set forbidden headers and avoids CORS issues.
     */
    public function proxyFile(Request $request)
    {
        $url = $request->query('url');

        if (!$url || !preg_match('/^https?:\/\//', $url)) {
            return response()->json(['ok' => false, 'error' => 'Invalid or missing url parameter'], 400);
        }

        // Optional override for filename
        $filename = $request->query('filename', basename(parse_url($url, PHP_URL_PATH) ?: 'download'));

        try {
            $guzzle = new GuzzleClient([
                'timeout' => 0,
                'verify' => false,
                // Do not throw exceptions for 4xx/5xx so we can handle status codes manually
                'http_errors' => false,
            ]);

            $resp = $guzzle->request('GET', $url, [
                'stream' => true,
                'headers' => [
                    'Referer' => 'https://dl.vidsrc.vip/',
                    // Optional: set a conservative User-Agent
                    'User-Agent' => 'Viewlix-Downloader/1.0'
                ],
            ]);

            $status = $resp->getStatusCode();
            if ($status >= 400) {
                return response()->json(['ok' => false, 'error' => 'Upstream responded with status '.$status], $status);
            }

            $contentType = $resp->getHeaderLine('Content-Type') ?: 'application/octet-stream';
            $contentLength = $resp->getHeaderLine('Content-Length');
            $contentDisposition = $resp->getHeaderLine('Content-Disposition') ?: 'attachment; filename="'.$filename.'"';

            return response()->stream(function () use ($resp) {
                $body = $resp->getBody();
                while (!$body->eof()) {
                    echo $body->read(1024 * 1024);
                    if (function_exists('ob_flush')) { @ob_flush(); }
                    flush();
                }
            }, 200, array_filter([
                'Content-Type' => $contentType,
                'Content-Length' => $contentLength ?: null,
                'Content-Disposition' => $contentDisposition,
            ]));

        } catch (\Exception $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get download options for a TV show episode
     */
    public function getTvDownloads(Request $request, $tmdbId)
    {
        try {
            $season = $request->query('season', 1);
            $episode = $request->query('episode', 1);

            $response = Http::timeout(30)
                ->post("{$this->downloadApiBase}/dl/tv/{$tmdbId}?season={$season}&episode={$episode}");

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'ok' => false,
                'error' => 'Failed to fetch downloads'
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

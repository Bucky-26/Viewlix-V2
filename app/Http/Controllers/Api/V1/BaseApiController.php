<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\TMDBService;
use Illuminate\Http\JsonResponse;

abstract class BaseApiController extends Controller
{
    /**
     * Send a standardized success JSON response.
     */
    protected function success(mixed $data = null, string $message = 'Success', int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Send a standardized error JSON response.
     */
    protected function error(string $message = 'Error', int $statusCode = 400, mixed $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if (!is_null($errors)) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Helper to format and enrich a single TMDB media item with direct and proxied image URLs.
     */
    protected function enrichMediaItem(array $item, TMDBService $tmdb, ?string $fallbackType = null): array
    {
        $type = $item['media_type'] ?? $fallbackType ?? (isset($item['title']) ? 'movie' : 'tv');
        
        $item['media_type'] = $type;
        $item['poster_url'] = $tmdb->getImageUrl($item['poster_path'] ?? null, 'w500', true);
        $item['backdrop_url'] = $tmdb->getImageUrl($item['backdrop_path'] ?? null, 'w1280', true);
        
        // Normalize title / name
        if (!isset($item['title']) && isset($item['name'])) {
            $item['title'] = $item['name'];
        }
        if (!isset($item['release_date']) && isset($item['first_air_date'])) {
            $item['release_date'] = $item['first_air_date'];
        }

        return $item;
    }

    /**
     * Helper to format a list of TMDB media items.
     */
    protected function enrichMediaList(array $items, TMDBService $tmdb, ?string $fallbackType = null): array
    {
        return array_map(function ($item) use ($tmdb, $fallbackType) {
            return $this->enrichMediaItem($item, $tmdb, $fallbackType);
        }, $items);
    }
}


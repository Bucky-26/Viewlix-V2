<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\WatchHistory;
use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WatchHistoryController extends BaseApiController
{
    /**
     * Get paginated watch history / continue watching list.
     * GET /api/v1/watch-history?page=1
     */
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $type = $request->query('type');
        $perPage = min(50, max(1, (int) $request->query('per_page', 20)));

        $query = $user->watchHistory()->recent();

        if ($type && in_array($type, ['movie', 'tv', 'anime'])) {
            $query->ofType($type);
        }

        $paginator = $query->paginate($perPage);

        $items = collect($paginator->items())->map(function ($item) {
            return [
                'id'             => $item->id,
                'media_id'       => $item->media_id,
                'media_type'     => $item->media_type,
                'title'          => $item->title,
                'poster_path'    => $item->poster_path,
                'backdrop_path'  => $item->backdrop_path,
                'poster_url'     => $item->poster_url,
                'backdrop_url'   => $item->backdrop_url,
                'season'         => $item->season,
                'episode'        => $item->episode,
                'progress'       => $item->progress,
                'watched_time'   => $item->watched_time,
                'duration'       => $item->duration,
                'formatted_time' => $item->formatted_watch_time,
                'is_completed'   => $item->is_completed,
                'updated_at'     => $item->updated_at?->toIso8601String(),
            ];
        });

        return $this->success([
            'current_page' => $paginator->currentPage(),
            'per_page'     => $paginator->perPage(),
            'total'        => $paginator->total(),
            'last_page'    => $paginator->lastPage(),
            'history'      => $items,
        ]);
    }

    /**
     * Save watch progress (syncs to SQL and optionally Firebase Realtime Database).
     * POST /api/v1/watch-history
     */
    public function save(Request $request, FirebaseService $firebase): JsonResponse
    {
        $validated = $request->validate([
            'media_id'      => ['required'],
            'media_type'    => ['required', 'string', 'in:movie,tv,anime'],
            'title'         => ['required', 'string', 'max:255'],
            'poster_path'   => ['nullable', 'string'],
            'backdrop_path' => ['nullable', 'string'],
            'season'        => ['nullable', 'integer', 'min:1'],
            'episode'       => ['nullable', 'integer', 'min:1'],
            'watched_time'  => ['nullable', 'numeric', 'min:0'],
            'duration'      => ['nullable', 'numeric', 'min:0'],
            'progress'      => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        /** @var User $user */
        $user = $request->user();

        $mediaId = (int) $validated['media_id'];
        $mediaType = $validated['media_type'];
        $watchedSeconds = (int) round($validated['watched_time'] ?? 0);
        $durationSeconds = (int) round($validated['duration'] ?? 0);

        // Calculate progress percentage
        $progress = $validated['progress'] ?? 0;
        if ($progress <= 0 && $durationSeconds > 0) {
            $progress = min(100, round(($watchedSeconds / $durationSeconds) * 100, 2));
        }

        // 1. Update SQL Database
        $history = WatchHistory::updateOrCreate(
            [
                'user_id'    => $user->id,
                'media_id'   => $mediaId,
                'media_type' => $mediaType,
            ],
            [
                'title'         => $validated['title'],
                'poster_path'   => $validated['poster_path'] ?? null,
                'backdrop_path' => $validated['backdrop_path'] ?? null,
                'season'        => $validated['season'] ?? 1,
                'episode'       => $validated['episode'] ?? 1,
                'progress'      => $progress,
                'watched_time'  => $watchedSeconds,
                'duration'      => $durationSeconds,
            ]
        );

        // 2. Sync to Firebase Realtime Database if configured
        if ($firebase->isConfigured()) {
            try {
                $firebaseUuid = $user->getFirebaseUuid();
                $rtdbPayload = [
                    'id'            => $mediaId,
                    'type'          => $mediaType,
                    'title'         => $validated['title'],
                    'poster_path'   => $validated['poster_path'] ?? null,
                    'backdrop_path' => $validated['backdrop_path'] ?? null,
                    'progress'      => [
                        'watched'  => $watchedSeconds,
                        'duration' => $durationSeconds,
                    ],
                    'season'        => $validated['season'] ?? 1,
                    'episode'       => $validated['episode'] ?? 1,
                    'last_updated'  => (int) (microtime(true) * 1000),
                ];

                $firebase->updateUserProgress($firebaseUuid, (string) $mediaId, $rtdbPayload);
            } catch (\Throwable $e) {
                Log::warning('Firebase progress sync failed via Mobile API: ' . $e->getMessage());
            }
        }

        return $this->success([
            'id'           => $history->id,
            'media_id'     => $history->media_id,
            'progress'     => $history->progress,
            'is_completed' => $history->is_completed,
            'updated_at'   => $history->updated_at?->toIso8601String(),
        ], 'Progress updated successfully');
    }

    /**
     * Get watch progress for a single media item.
     * GET /api/v1/watch-history/{mediaId}?type=movie
     */
    public function show(int $mediaId, Request $request): JsonResponse
    {
        $type = $request->query('type');
        /** @var User $user */
        $user = $request->user();

        $query = $user->watchHistory()->where('media_id', $mediaId);
        if ($type) {
            $query->where('media_type', $type);
        }

        $history = $query->first();

        if (!$history) {
            return $this->success(null, 'No progress found for this media');
        }

        return $this->success([
            'id'             => $history->id,
            'media_id'       => $history->media_id,
            'media_type'     => $history->media_type,
            'title'          => $history->title,
            'season'         => $history->season,
            'episode'        => $history->episode,
            'progress'       => $history->progress,
            'watched_time'   => $history->watched_time,
            'duration'       => $history->duration,
            'formatted_time' => $history->formatted_watch_time,
            'is_completed'   => $history->is_completed,
            'updated_at'     => $history->updated_at?->toIso8601String(),
        ]);
    }

    /**
     * Remove an item from watch history.
     * DELETE /api/v1/watch-history/{mediaId}?type=movie
     */
    public function destroy(int $mediaId, Request $request, FirebaseService $firebase): JsonResponse
    {
        $type = $request->query('type');
        /** @var User $user */
        $user = $request->user();

        $query = $user->watchHistory()->where('media_id', $mediaId);
        if ($type) {
            $query->where('media_type', $type);
        }

        $query->delete();

        // Also clean up from Firebase RTDB if configured
        if ($firebase->isConfigured()) {
            try {
                $firebase->deleteUserProgress($user->getFirebaseUuid(), (string) $mediaId);
            } catch (\Throwable $e) {
                Log::warning('Firebase delete progress failed via Mobile API: ' . $e->getMessage());
            }
        }

        return $this->success(null, 'Watch history entry removed');
    }

    /**
     * Clear all watch history for the authenticated user.
     * DELETE /api/v1/watch-history
     */
    public function clear(Request $request, FirebaseService $firebase): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $user->watchHistory()->delete();

        if ($firebase->isConfigured()) {
            try {
                $firebase->clearAllUserProgress($user->getFirebaseUuid());
            } catch (\Throwable $e) {
                Log::warning('Firebase clear all progress failed via Mobile API: ' . $e->getMessage());
            }
        }

        return $this->success(null, 'All watch history cleared');
    }
}


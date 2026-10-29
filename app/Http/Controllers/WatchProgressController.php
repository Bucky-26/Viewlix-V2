<?php

namespace App\Http\Controllers;

use App\Models\WatchHistory;
use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WatchProgressController extends Controller
{
    /**
     * Resolve authenticated user from Sanctum token, session, or default guard
     */
    protected function resolveUser(Request $request): ?\App\Models\User
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::guard('sanctum')->user()
            ?? $request->user('sanctum')
            ?? Auth::guard('web')->user()
            ?? $request->user('web')
            ?? Auth::user();

        return $user;
    }

    /**
     * Save watch progress to Firebase Realtime Database (RTDB) and local database
     */
    public function save(Request $request, FirebaseService $firebaseService): JsonResponse
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return response()->json([
                'success' => false,
                'error'   => 'Unauthenticated. Please provide an Authorization: Bearer <TOKEN> header or active login session.',
            ], 401);
        }

        $validated = $request->validate([
            'id' => 'required',
            'type' => 'nullable|string|in:movie,tv,anime',
            'title' => 'nullable|string|max:255',
            'poster_path' => 'nullable|string',
            'backdrop_path' => 'nullable|string',
            'progress' => 'nullable|array',
            'progress.watched' => 'nullable|numeric|min:0',
            'progress.duration' => 'nullable|numeric|min:0',
            'watched' => 'nullable|numeric|min:0',
            'duration' => 'nullable|numeric|min:0',
            'currentTime' => 'nullable|numeric|min:0',
            'last_season_watched' => 'nullable',
            'last_episode_watched' => 'nullable',
            'show_progress' => 'nullable|array',
            'last_updated' => 'nullable|numeric',
        ]);

        $mediaId = $validated['id'];
        $type = $validated['type'] ?? 'movie';
        $title = $validated['title'] ?? 'Unknown';
        $watchedSeconds = (float) ($validated['progress']['watched'] ?? $validated['watched'] ?? $validated['currentTime'] ?? 0);
        $durationSeconds = (float) ($validated['progress']['duration'] ?? $validated['duration'] ?? 0);
        $percent = $durationSeconds > 0 ? min(100, round(($watchedSeconds / $durationSeconds) * 100, 2)) : 0;

        // Structure payload conforming to RTDB rules (.hasChildren(['id', 'type', 'title', 'progress']))
        $rtdbPayload = [
            'id' => is_numeric($mediaId) ? (int) $mediaId : $mediaId,
            'type' => $type,
            'title' => $title,
            'poster_path' => $validated['poster_path'] ?? null,
            'backdrop_path' => $validated['backdrop_path'] ?? null,
            'progress' => [
                'watched' => $watchedSeconds,
                'duration' => $durationSeconds,
            ],
            'last_updated' => (int) ($validated['last_updated'] ?? (microtime(true) * 1000)),
        ];

        if (!empty($validated['last_season_watched'])) {
            $rtdbPayload['last_season_watched'] = (string) $validated['last_season_watched'];
        }
        if (!empty($validated['last_episode_watched'])) {
            $rtdbPayload['last_episode_watched'] = (string) $validated['last_episode_watched'];
        }
        if (!empty($validated['show_progress']) && is_array($validated['show_progress'])) {
            $rtdbPayload['show_progress'] = $validated['show_progress'];
        }

        // 1. Persist directly to Firebase Realtime Database using Admin SDK
        $firebaseUuid = $user->getFirebaseUuid();
        $rtdbSaved = false;

        try {
            $rtdbSaved = (bool) $firebaseService->saveUserWatchProgress($firebaseUuid, $mediaId, $rtdbPayload);
        } catch (\Throwable $e) {
            Log::error('WatchProgressController RTDB save exception: ' . $e->getMessage());
        }

        // 2. Persist to MySQL WatchHistory table
        try {
            $season = !empty($validated['last_season_watched']) ? (int) $validated['last_season_watched'] : null;
            $episode = !empty($validated['last_episode_watched']) ? (int) $validated['last_episode_watched'] : null;

            WatchHistory::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'media_id' => (int) $mediaId,
                    'media_type' => $type,
                    'season' => $season,
                    'episode' => $episode,
                ],
                [
                    'title' => $title,
                    'poster_path' => $validated['poster_path'] ?? null,
                    'backdrop_path' => $validated['backdrop_path'] ?? null,
                    'progress' => $percent,
                    'watched_time' => (int) round($watchedSeconds),
                    'duration' => (int) round($durationSeconds),
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('WatchProgressController SQL save error: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'saved_to_rtdb' => $rtdbSaved,
            'media_id' => $mediaId,
            'watched' => $watchedSeconds,
            'duration' => $durationSeconds,
            'progress_percent' => $percent,
        ]);
    }

    /**
     * Get saved watch progress for a single media item from RTDB (or fallback to SQL)
     */
    public function get($mediaId, Request $request, FirebaseService $firebaseService): JsonResponse
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return response()->json([
                'success' => false,
                'error'   => 'Unauthenticated. Please provide an Authorization: Bearer <TOKEN> header or active login session.',
            ], 401);
        }

        $firebaseUuid = $user->getFirebaseUuid();
        $progress = null;

        // Check if RTDB egress optimization is enabled
        $optimizeRtdbEgress = \App\Models\AppSetting::getVal('optimize_rtdb_egress', true);

        if (!$optimizeRtdbEgress) {
            try {
                $progress = $firebaseService->getUserWatchProgress($firebaseUuid, $mediaId);
            } catch (\Throwable $e) {}
        }

        // If RTDB had no entry or optimization is enabled, fall back to SQL WatchHistory
        if (!$progress) {
            $sqlRecord = WatchHistory::where('user_id', $user->id)
                ->where('media_id', (int) $mediaId)
                ->latest()
                ->first();

            if ($sqlRecord) {
                $progress = [
                    'id' => $sqlRecord->media_id,
                    'type' => $sqlRecord->media_type,
                    'title' => $sqlRecord->title,
                    'poster_path' => $sqlRecord->poster_path,
                    'backdrop_path' => $sqlRecord->backdrop_path,
                    'progress' => [
                        'watched' => (float) ($sqlRecord->watched_time ?? 0),
                        'duration' => (float) ($sqlRecord->duration ?? 0),
                    ],
                    'last_season_watched' => $sqlRecord->season ? (string) $sqlRecord->season : null,
                    'last_episode_watched' => $sqlRecord->episode ? (string) $sqlRecord->episode : null,
                    'last_updated' => $sqlRecord->updated_at ? ($sqlRecord->updated_at->getTimestamp() * 1000) : null,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $progress,
        ]);
    }

    /**
     * Get all watch progress for the authenticated user
     */
    public function all(Request $request, FirebaseService $firebaseService): JsonResponse
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return response()->json([
                'success' => false,
                'error'   => 'Unauthenticated. Please provide an Authorization: Bearer <TOKEN> header or active login session.',
            ], 401);
        }

        $firebaseUuid = $user->getFirebaseUuid();
        $allProgress = [];

        // Check if RTDB egress optimization is enabled
        $optimizeRtdbEgress = \App\Models\AppSetting::getVal('optimize_rtdb_egress', true);

        if (!$optimizeRtdbEgress) {
            try {
                $allProgress = $firebaseService->getUserAllWatchProgress($firebaseUuid) ?? [];
            } catch (\Throwable $e) {}
        }

        if (empty($allProgress)) {
            $records = WatchHistory::where('user_id', $user->id)->orderBy('updated_at', 'desc')->get();
            foreach ($records as $r) {
                $allProgress[(string) $r->media_id] = [
                    'id' => $r->media_id,
                    'type' => $r->media_type,
                    'title' => $r->title,
                    'poster_path' => $r->poster_path,
                    'backdrop_path' => $r->backdrop_path,
                    'progress' => [
                        'watched' => (float) ($r->watched_time ?? 0),
                        'duration' => (float) ($r->duration ?? 0),
                    ],
                    'last_season_watched' => $r->season ? (string) $r->season : null,
                    'last_episode_watched' => $r->episode ? (string) $r->episode : null,
                    'last_updated' => $r->updated_at ? ($r->updated_at->getTimestamp() * 1000) : null,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => $allProgress,
        ]);
    }

    /**
     * Remove watch progress for a single media item
     */
    public function destroy($mediaId, Request $request, FirebaseService $firebaseService): JsonResponse
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return response()->json([
                'success' => false,
                'error'   => 'Unauthenticated. Please provide an Authorization: Bearer <TOKEN> header or active login session.',
            ], 401);
        }

        $firebaseUuid = $user->getFirebaseUuid();

        try {
            $firebaseService->deleteUserWatchProgress($firebaseUuid, $mediaId);
        } catch (\Throwable $e) {}

        try {
            WatchHistory::where('user_id', $user->id)->where('media_id', (int) $mediaId)->delete();
        } catch (\Throwable $e) {}

        return response()->json(['success' => true]);
    }

    /**
     * Clear all watch progress
     */
    public function clear(Request $request, FirebaseService $firebaseService): JsonResponse
    {
        $user = $this->resolveUser($request);

        if (!$user) {
            return response()->json([
                'success' => false,
                'error'   => 'Unauthenticated. Please provide an Authorization: Bearer <TOKEN> header or active login session.',
            ], 401);
        }

        $firebaseUuid = $user->getFirebaseUuid();

        try {
            $firebaseService->clearUserWatchProgress($firebaseUuid);
        } catch (\Throwable $e) {}

        try {
            WatchHistory::where('user_id', $user->id)->delete();
        } catch (\Throwable $e) {}

        return response()->json(['success' => true]);
    }
}

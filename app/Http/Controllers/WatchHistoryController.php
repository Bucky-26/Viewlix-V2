<?php

namespace App\Http\Controllers;

use App\Models\WatchHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WatchHistoryController extends Controller
{
    /**
     * Display user's watch history page
     */
    public function index(Request $request): View
    {
        $type = $request->get('type', 'all');
        
        $query = Auth::user()->watchHistory()->recent();
        
        if ($type !== 'all') {
            $query->ofType($type);
        }
        
        $history = $query->paginate(24);
        
        return view('pages.watch-history', [
            'history' => $history,
            'activeType' => $type,
        ]);
    }

    /**
     * Store or update watch progress
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'media_id' => 'required|integer',
            'media_type' => 'required|in:movie,tv,anime',
            'title' => 'required|string|max:255',
            'poster_path' => 'nullable|string',
            'backdrop_path' => 'nullable|string',
            'season' => 'nullable|integer',
            'episode' => 'nullable|integer',
            'watched_time' => 'required|integer|min:0',
            'duration' => 'required|integer|min:1',
        ]);

        // Calculate progress percentage
        $progress = min(100, ($validated['watched_time'] / $validated['duration']) * 100);
        $validated['progress'] = round($progress, 2);

        $user = Auth::user();

        // Update or create watch history entry
        $history = $user->watchHistory()->updateOrCreate(
            [
                'media_id' => $validated['media_id'],
                'media_type' => $validated['media_type'],
                'season' => $validated['season'] ?? null,
                'episode' => $validated['episode'] ?? null,
            ],
            $validated
        );

        return response()->json([
            'status' => 'saved',
            'progress' => $history->progress,
            'message' => 'Progress saved',
        ]);
    }

    /**
     * Get watch progress for a specific media
     */
    public function getProgress(Request $request): JsonResponse
    {
        $mediaId = $request->get('media_id');
        $mediaType = $request->get('media_type');
        $season = $request->get('season');
        $episode = $request->get('episode');

        if (!$mediaId || !$mediaType) {
            return response()->json(['progress' => null]);
        }

        $query = Auth::user()->watchHistory()
            ->where('media_id', $mediaId)
            ->where('media_type', $mediaType);

        if ($season) {
            $query->where('season', $season);
        }
        if ($episode) {
            $query->where('episode', $episode);
        }

        $history = $query->first();

        if (!$history) {
            return response()->json(['progress' => null]);
        }

        return response()->json([
            'progress' => $history->progress,
            'watched_time' => $history->watched_time,
            'duration' => $history->duration,
        ]);
    }

    /**
     * Get continue watching items
     */
    public function continueWatching(): JsonResponse
    {
        $items = Auth::user()->watchHistory()
            ->where('progress', '<', 90)
            ->where('progress', '>', 0)
            ->recent()
            ->take(10)
            ->get();

        return response()->json($items);
    }

    /**
     * Clear watch history
     */
    public function clear(Request $request): JsonResponse
    {
        $type = $request->get('type');

        $query = Auth::user()->watchHistory();
        
        if ($type && $type !== 'all') {
            $query->ofType($type);
        }

        $query->delete();

        return response()->json([
            'status' => 'cleared',
            'message' => 'Watch history cleared',
        ]);
    }

    /**
     * Remove single history entry
     */
    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'media_id' => 'required|integer',
            'media_type' => 'required|in:movie,tv,anime',
            'season' => 'nullable|integer',
            'episode' => 'nullable|integer',
        ]);

        $query = Auth::user()->watchHistory()
            ->where('media_id', $validated['media_id'])
            ->where('media_type', $validated['media_type']);

        if (isset($validated['season'])) {
            $query->where('season', $validated['season']);
        }
        if (isset($validated['episode'])) {
            $query->where('episode', $validated['episode']);
        }

        $query->delete();

        return response()->json([
            'status' => 'removed',
            'message' => 'Removed from history',
        ]);
    }
}

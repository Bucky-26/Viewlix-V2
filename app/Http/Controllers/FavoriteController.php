<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    /**
     * Display user's favorites page
     */
    public function index(Request $request): View
    {
        $type = $request->get('type', 'all');
        
        $query = Auth::user()->favorites()->latest();
        
        if ($type !== 'all') {
            $query->ofType($type);
        }
        
        $favorites = $query->paginate(24);
        
        return view('pages.favorites', [
            'favorites' => $favorites,
            'activeType' => $type,
        ]);
    }

    /**
     * Toggle favorite status (add/remove)
     */
    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'media_id' => 'required|integer',
            'media_type' => 'required|in:movie,tv,anime',
            'title' => 'required|string|max:255',
            'poster_path' => 'nullable|string',
            'backdrop_path' => 'nullable|string',
            'vote_average' => 'nullable|numeric',
            'release_date' => 'nullable|string',
        ]);

        $user = Auth::user();
        
        // Check if already favorited
        $existing = $user->favorites()
            ->where('media_id', $validated['media_id'])
            ->where('media_type', $validated['media_type'])
            ->first();

        if ($existing) {
            $existing->delete();
            return response()->json([
                'status' => 'removed',
                'message' => 'Removed from favorites',
            ]);
        }

        // Add to favorites
        $user->favorites()->create($validated);
        
        return response()->json([
            'status' => 'added',
            'message' => 'Added to favorites',
        ]);
    }

    /**
     * Check if media is favorited
     */
    public function check(Request $request): JsonResponse
    {
        $mediaId = $request->get('media_id');
        $mediaType = $request->get('media_type');

        if (!$mediaId || !$mediaType) {
            return response()->json(['favorited' => false]);
        }

        $favorited = Auth::user()->favorites()
            ->where('media_id', $mediaId)
            ->where('media_type', $mediaType)
            ->exists();

        return response()->json(['favorited' => $favorited]);
    }

    /**
     * Remove from favorites
     */
    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'media_id' => 'required|integer',
            'media_type' => 'required|in:movie,tv,anime',
        ]);

        Auth::user()->favorites()
            ->where('media_id', $validated['media_id'])
            ->where('media_type', $validated['media_type'])
            ->delete();

        return response()->json([
            'status' => 'removed',
            'message' => 'Removed from favorites',
        ]);
    }
}

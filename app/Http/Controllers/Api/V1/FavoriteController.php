<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Favorite;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends BaseApiController
{
    /**
     * Get paginated favorites for the authenticated user.
     * GET /api/v1/favorites?type=all&page=1
     */
    public function index(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $type = $request->query('type', 'all');
        $perPage = min(50, max(1, (int) $request->query('per_page', 24)));

        $query = $user->favorites()->latest();

        if ($type !== 'all') {
            $query->ofType($type);
        }

        $paginator = $query->paginate($perPage);

        $items = collect($paginator->items())->map(function ($fav) {
            return [
                'id'            => $fav->id,
                'media_id'      => $fav->media_id,
                'media_type'    => $fav->media_type,
                'title'         => $fav->title,
                'poster_path'   => $fav->poster_path,
                'backdrop_path' => $fav->backdrop_path,
                'poster_url'    => $fav->poster_url,
                'backdrop_url'  => $fav->backdrop_url,
                'vote_average'  => $fav->vote_average,
                'release_date'  => $fav->release_date,
                'created_at'    => $fav->created_at?->toIso8601String(),
            ];
        });

        return $this->success([
            'current_page' => $paginator->currentPage(),
            'per_page'     => $paginator->perPage(),
            'total'        => $paginator->total(),
            'last_page'    => $paginator->lastPage(),
            'favorites'    => $items,
        ]);
    }

    /**
     * Toggle favorite status (convenient add/remove in one endpoint for mobile).
     * POST /api/v1/favorites/toggle
     */
    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'media_id'      => ['required', 'integer'],
            'media_type'    => ['required', 'string', 'in:movie,tv,anime'],
            'title'         => ['required', 'string', 'max:255'],
            'poster_path'   => ['nullable', 'string'],
            'backdrop_path' => ['nullable', 'string'],
            'vote_average'  => ['nullable', 'numeric'],
            'release_date'  => ['nullable', 'string'],
        ]);

        /** @var User $user */
        $user = $request->user();

        $existing = $user->favorites()
            ->where('media_id', $validated['media_id'])
            ->where('media_type', $validated['media_type'])
            ->first();

        if ($existing) {
            $existing->delete();
            return $this->success([
                'status'       => 'removed',
                'is_favorited' => false,
            ], 'Removed from favorites');
        }

        $favorite = $user->favorites()->create($validated);

        return $this->success([
            'status'       => 'added',
            'is_favorited' => true,
            'favorite_id'  => $favorite->id,
        ], 'Added to favorites', 201);
    }

    /**
     * Check if a specific media item is in the user's favorites.
     * GET /api/v1/favorites/check/{mediaId}?type=movie
     */
    public function check(int $mediaId, Request $request): JsonResponse
    {
        $type = $request->query('type');
        /** @var User $user */
        $user = $request->user();

        $query = $user->favorites()->where('media_id', $mediaId);
        if ($type) {
            $query->where('media_type', $type);
        }

        $isFavorited = $query->exists();

        return $this->success([
            'media_id'     => $mediaId,
            'is_favorited' => $isFavorited,
        ]);
    }

    /**
     * Remove a media item from favorites.
     * DELETE /api/v1/favorites/{mediaId}?type=movie
     */
    public function destroy(int $mediaId, Request $request): JsonResponse
    {
        $type = $request->query('type');
        /** @var User $user */
        $user = $request->user();

        $query = $user->favorites()->where('media_id', $mediaId);
        if ($type) {
            $query->where('media_type', $type);
        }

        $deleted = $query->delete();

        if (!$deleted) {
            return $this->error('Item was not found in favorites.', 404);
        }

        return $this->success(null, 'Removed from favorites');
    }
}


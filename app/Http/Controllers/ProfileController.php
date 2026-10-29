<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\SupportTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile showcase page.
     */
    public function show(Request $request): View
    {
        $user = $request->user();

        $favoritesCount = $user->favorites()->count();
        $watchHistoryCount = $user->watchHistory()->count();
        $sessionsCount = $user->sessions()->count();
        $ticketsCount = SupportTicket::where('user_id', $user->id)->count();

        $recentFavorites = $user->favorites()->latest()->take(6)->get();
        $recentWatchHistory = $user->watchHistory()->latest('updated_at')->take(6)->get();

        $r2Configured = !empty(config('filesystems.disks.r2.key')) && !empty(config('filesystems.disks.r2.secret'));

        return view('profile.show', [
            'user' => $user,
            'favoritesCount' => $favoritesCount,
            'watchHistoryCount' => $watchHistoryCount,
            'sessionsCount' => $sessionsCount,
            'ticketsCount' => $ticketsCount,
            'recentFavorites' => $recentFavorites,
            'recentWatchHistory' => $recentWatchHistory,
            'r2Configured' => $r2Configured,
        ]);
    }

    /**
     * Display the user's profile edit form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $r2Configured = !empty(config('filesystems.disks.r2.key')) && !empty(config('filesystems.disks.r2.secret'));
        $sessions = $user->sessions()->latest('last_activity_at')->take(10)->get();

        return view('profile.edit', [
            'user' => $user,
            'r2Configured' => $r2Configured,
            'sessions' => $sessions,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        // Support redirection back to edit page or overview while maintaining test compatibility
        if ($request->input('redirect_to') === 'edit') {
            return Redirect::route('profile.edit')->with('status', 'profile-updated');
        }

        return Redirect::to('/profile')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Remove avatar from storage if exists
        $this->cleanupOldAvatar($user->avatar);

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * Store avatar in Cloudflare R2 object storage (or local fallback) and update user profile.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'], // 5MB max
        ]);

        $user = $request->user();
        $file = $request->file('avatar');
        $extension = strtolower($file->getClientOriginalExtension() ?: 'png');
        $filename = 'avatar_' . $user->id . '_' . time() . '_' . Str::random(8) . '.' . $extension;

        $r2Key = config('filesystems.disks.r2.key');
        $r2Secret = config('filesystems.disks.r2.secret');
        $isR2Configured = !empty($r2Key) && !empty($r2Secret);

        $uploadedToR2 = false;
        $avatarUrl = null;

        if ($isR2Configured) {
            try {
                $r2Path = 'avatars/' . $filename;
                $stream = fopen($file->getRealPath(), 'r+');
                
                Storage::disk('r2')->put($r2Path, $stream, [
                    'visibility' => 'public',
                    'ContentType' => $file->getMimeType(),
                ]);

                if (is_resource($stream)) {
                    fclose($stream);
                }

                $cdnDomain = rtrim(config('filesystems.disks.r2.url', 'https://cdn.viewlix.online'), '/');
                $avatarUrl = $cdnDomain . '/' . $r2Path;
                $uploadedToR2 = true;
            } catch (\Throwable $e) {
                Log::error('Cloudflare R2 avatar upload failed: ' . $e->getMessage(), [
                    'exception' => $e,
                    'user_id' => $user->id,
                ]);
                $uploadedToR2 = false;
            }
        }

        // Fallback to public local disk if R2 is unconfigured or failed
        if (!$uploadedToR2) {
            $localPath = $file->storeAs('avatars', $filename, 'public');
            $assetUrl = config('app.asset_url') ? config('app.asset_url') : config('app.url');
            $avatarUrl = rtrim($assetUrl, '/') . '/storage/' . $localPath;
        }

        // Clean up previous avatar if it was stored
        $this->cleanupOldAvatar($user->avatar);

        $user->avatar = $avatarUrl;
        $user->save();

        $statusMessage = 'Profile photo updated successfully.';

        return Redirect::back()->with('status', 'avatar-updated')->with('avatar_status_detail', $statusMessage);
    }

    /**
     * Remove the current user's profile avatar.
     */
    public function deleteAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->avatar) {
            $this->cleanupOldAvatar($user->avatar);
            $user->avatar = null;
            $user->save();
        }

        return Redirect::back()->with('status', 'avatar-deleted')->with('avatar_status_detail', 'Profile photo removed.');
    }

    /**
     * Clean up an old avatar from Cloudflare R2 or local storage.
     */
    protected function cleanupOldAvatar(?string $avatarUrl): void
    {
        if (empty($avatarUrl)) {
            return;
        }

        try {
            // Check if R2 avatar
            if (str_contains($avatarUrl, 'cdn.viewlix.online') || str_contains($avatarUrl, 'r2.cloudflarestorage.com')) {
                $path = parse_url($avatarUrl, PHP_URL_PATH);
                $path = ltrim($path, '/');
                if (!empty($path) && Storage::disk('r2')->exists($path)) {
                    Storage::disk('r2')->delete($path);
                }
            } elseif (str_contains($avatarUrl, '/storage/avatars/')) {
                // Local public avatar
                $parts = explode('/storage/', $avatarUrl);
                if (isset($parts[1]) && Storage::disk('public')->exists($parts[1])) {
                    Storage::disk('public')->delete($parts[1]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Failed cleaning up old avatar: ' . $e->getMessage());
        }
    }

    /**
     * Update user settings (playback, display, etc).
     */
    public function updateSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'default_quality' => ['nullable', 'string', 'in:auto,1080p,720p,480p'],
            'autoplay_next' => ['nullable', 'boolean'],
            'skip_intro' => ['nullable', 'boolean'],
        ]);

        $user = $request->user();
        
        $settings = $user->settings ?? [];
        
        if ($request->has('default_quality')) {
            $settings['default_quality'] = $validated['default_quality'];
        }
        
        $settings['autoplay_next'] = $request->has('autoplay_next') ? true : false;
        $settings['skip_intro'] = $request->has('skip_intro') ? true : false;

        $user->settings = $settings;
        $user->save();

        return Redirect::back()->with('status', 'settings-updated');
    }
}

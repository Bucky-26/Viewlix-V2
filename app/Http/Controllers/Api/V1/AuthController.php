<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\SupportTicket;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseApiController
{
    /**
     * Register a new user and issue an API token.
     * POST /api/v1/auth/register
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
            'device_name'           => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::create([
            'name'          => $validated['name'],
            'email'         => strtolower(trim($validated['email'])),
            'password'      => Hash::make($validated['password']),
            'firebase_uuid' => Str::uuid()->toString(),
        ]);

        $deviceName = $request->input('device_name', 'Mobile App');
        $token = $user->createToken($deviceName)->plainTextToken;

        return $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $this->formatUserData($user),
        ], 'Registration successful', 201);
    }

    /**
     * Authenticate an existing user and return an API token.
     * POST /api/v1/auth/login
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'       => ['required', 'string', 'email'],
            'password'    => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $email = strtolower(trim($request->input('email')));
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return $this->error('Invalid email or password credentials.', 401);
        }

        $deviceName = $request->input('device_name', 'Mobile App');
        $token = $user->createToken($deviceName)->plainTextToken;

        return $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => $this->formatUserData($user),
        ], 'Login successful');
    }

    /**
     * Get the authenticated user's profile and stats.
     * GET /api/v1/auth/me (Protected by auth:sanctum)
     */
    public function me(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return $this->success([
            'user' => $this->formatUserData($user),
            'stats' => [
                'favorites_count' => $user->favorites()->count(),
                'watch_history_count' => $user->watchHistory()->count(),
                'tickets_count' => SupportTicket::where('user_id', $user->id)->count(),
            ],
        ]);
    }

    /**
     * Update the authenticated user's profile details.
     * PUT /api/v1/auth/profile (Protected by auth:sanctum)
     */
    public function updateProfile(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->name = $validated['name'];
        if ($user->email !== strtolower(trim($validated['email']))) {
            $user->email = strtolower(trim($validated['email']));
            $user->email_verified_at = null;
        }
        $user->save();

        return $this->success([
            'user' => $this->formatUserData($user),
        ], 'Profile updated successfully');
    }

    /**
     * Update the authenticated user's password.
     * PUT /api/v1/auth/password (Protected by auth:sanctum)
     */
    public function updatePassword(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return $this->error('The provided current password does not match our records.', 422, [
                'current_password' => ['The current password is incorrect.']
            ]);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return $this->success(null, 'Password updated successfully');
    }

    /**
     * Upload or update the user's avatar image.
     * POST /api/v1/auth/avatar (Protected by auth:sanctum)
     */
    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'], // 5MB max
        ]);

        /** @var User $user */
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
                Log::error('Cloudflare R2 avatar upload failed via Mobile API: ' . $e->getMessage(), [
                    'user_id' => $user->id,
                ]);
            }
        }

        // Fallback to public storage
        if (!$uploadedToR2) {
            $localPath = $file->storeAs('avatars', $filename, 'public');
            $assetUrl = config('app.asset_url') ?: config('app.url');
            $avatarUrl = rtrim($assetUrl, '/') . '/storage/' . $localPath;
        }

        $user->avatar = $avatarUrl;
        $user->save();

        return $this->success([
            'avatar_url' => $avatarUrl,
            'user' => $this->formatUserData($user),
        ], 'Avatar updated successfully');
    }

    /**
     * Generate or fetch a Firebase custom token for realtime database sync.
     * GET /api/v1/auth/firebase-token (Protected by auth:sanctum)
     */
    public function firebaseToken(Request $request, FirebaseService $firebase): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (!$firebase->isConfigured()) {
            return $this->error('Firebase service is not currently configured on the server.', 503);
        }

        $token = $firebase->createCustomToken($user);

        if (!$token) {
            return $this->error('Failed to create Firebase custom token.', 500);
        }

        return $this->success([
            'firebase_token' => $token,
            'firebase_uuid' => $user->getFirebaseUuid(),
        ]);
    }

    /**
     * Revoke the current access token (logout).
     * POST /api/v1/auth/logout (Protected by auth:sanctum)
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return $this->success(null, 'Logged out successfully');
    }

    /**
     * Delete the authenticated user's account.
     * DELETE /api/v1/auth/delete-account (Protected by auth:sanctum)
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $request->validate([
            'password' => ['required', 'string'],
        ]);

        if (!Hash::check($request->input('password'), $user->password)) {
            return $this->error('The provided password was incorrect.', 422, [
                'password' => ['Incorrect password.'],
            ]);
        }

        $user->tokens()->delete();
        $user->favorites()->delete();
        $user->watchHistory()->delete();
        $user->delete();

        return $this->success(null, 'Account has been permanently deleted');
    }

    /**
     * Format user model for API response.
     */
    protected function formatUserData(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'avatar_url' => $user->avatar_url,
            'firebase_uuid' => $user->getFirebaseUuid(),
            'is_admin' => (bool) $user->is_admin,
            'settings' => $user->settings ?? [],
            'created_at' => $user->created_at?->toIso8601String(),
        ];
    }
}


<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\SupportTicket;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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
     * Authenticate or register a user via Google (Android / Mobile).
     * POST /api/v1/auth/google
     *
     * Accepts:
     * - id_token (Google ID token from Google Sign-In SDK or Credential Manager)
     * - access_token (Google OAuth access token)
     * - token (alias for id_token)
     * - device_name (optional device name string)
     */
    public function googleAuth(Request $request): JsonResponse
    {
        $request->validate([
            'id_token'         => ['nullable', 'string'],
            'token'            => ['nullable', 'string'],
            'google_token'     => ['nullable', 'string'],
            'access_token'     => ['nullable', 'string'],
            'server_auth_code' => ['nullable', 'string'],
            'device_name'      => ['nullable', 'string', 'max:100'],
        ]);

        $idToken = $request->input('id_token') 
            ?? $request->input('token') 
            ?? $request->input('google_token');
        $accessToken = $request->input('access_token');
        $authCode = $request->input('server_auth_code');

        if (empty($idToken) && empty($accessToken) && empty($authCode)) {
            return $this->error('A valid Google id_token or access_token is required.', 422, [
                'id_token' => ['Google authentication token is missing.'],
            ]);
        }

        $googleUserData = null;

        // 1. Verify via Google ID Token endpoint
        if (!empty($idToken)) {
            try {
                $response = Http::timeout(10)->get('https://oauth2.googleapis.com/tokeninfo', [
                    'id_token' => $idToken,
                ]);

                if ($response->successful()) {
                    $json = $response->json();
                    if (!empty($json['sub']) && !empty($json['email'])) {
                        $googleUserData = [
                            'id'      => (string) $json['sub'],
                            'email'   => strtolower(trim($json['email'])),
                            'name'    => $json['name'] ?? explode('@', $json['email'])[0],
                            'picture' => $json['picture'] ?? null,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Google tokeninfo verification failed: ' . $e->getMessage());
            }

            // 1b. Fallback: Check if it is a Firebase Auth ID token
            if (!$googleUserData) {
                try {
                    $firebase = app(FirebaseService::class);
                    if ($firebase->isConfigured()) {
                        $firebaseAuth = $firebase->getAuth();
                        if ($firebaseAuth) {
                            $verifiedToken = $firebaseAuth->verifyIdToken($idToken);
                            $claims = $verifiedToken->claims();
                            $sub = $claims->get('sub');
                            $email = $claims->get('email');
                            if ($sub && $email) {
                                $googleUserData = [
                                    'id'      => (string) $sub,
                                    'email'   => strtolower(trim($email)),
                                    'name'    => $claims->get('name') ?? explode('@', $email)[0],
                                    'picture' => $claims->get('picture') ?? null,
                                ];
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning('Firebase verifyIdToken fallback: ' . $e->getMessage());
                }
            }
        }

        // 2. If access_token was provided
        if (!$googleUserData && !empty($accessToken)) {
            try {
                $response = Http::timeout(10)
                    ->withToken($accessToken)
                    ->get('https://www.googleapis.com/oauth2/v3/userinfo');

                if ($response->successful()) {
                    $json = $response->json();
                    if (!empty($json['sub']) && !empty($json['email'])) {
                        $googleUserData = [
                            'id'      => (string) $json['sub'],
                            'email'   => strtolower(trim($json['email'])),
                            'name'    => $json['name'] ?? explode('@', $json['email'])[0],
                            'picture' => $json['picture'] ?? null,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Google userinfo verification failed: ' . $e->getMessage());
            }
        }

        if (!$googleUserData) {
            return $this->error('Invalid or expired Google authentication credentials.', 401);
        }

        $googleId = $googleUserData['id'];
        $email = $googleUserData['email'];
        $name = $request->input('name') ?: $googleUserData['name'];
        $avatar = $request->input('avatar') ?: $googleUserData['picture'];

        $isNewUser = false;

        // Check if user exists with this Google ID
        $user = User::where('google_id', $googleId)->first();

        if ($user) {
            // Existing Google user - update avatar if empty
            if (empty($user->avatar) && !empty($avatar)) {
                $user->avatar = $avatar;
                $user->save();
            }
        } else {
            // Check if user exists with this email (link accounts)
            $user = User::where('email', $email)->first();

            if ($user) {
                $user->google_id = $googleId;
                if (empty($user->avatar) && !empty($avatar)) {
                    $user->avatar = $avatar;
                }
                if (!$user->hasVerifiedEmail()) {
                    $user->markEmailAsVerified();
                }
                $user->save();
            } else {
                // Register a new user
                $isNewUser = true;
                $user = User::create([
                    'name'          => $name,
                    'email'         => $email,
                    'google_id'     => $googleId,
                    'avatar'        => $avatar,
                    'password'      => Hash::make(Str::random(32)),
                    'firebase_uuid' => Str::uuid()->toString(),
                ]);

                if (method_exists($user, 'markEmailAsVerified')) {
                    $user->markEmailAsVerified();
                } else {
                    $user->email_verified_at = now();
                    $user->save();
                    event(new Verified($user));
                }

                // Fire Registered event for welcome notification & emails
                try {
                    event(new Registered($user));
                } catch (\Throwable $e) {
                    Log::warning('Registered event listener failed: ' . $e->getMessage());
                }
            }
        }

        // Issue Sanctum Token
        $deviceName = $request->input('device_name', 'Android App');
        $token = $user->createToken($deviceName)->plainTextToken;

        // Optional Firebase Custom Token
        $firebaseToken = null;
        try {
            $firebase = app(FirebaseService::class);
            if ($firebase->isConfigured()) {
                $firebaseToken = $firebase->createCustomToken($user);
            }
        } catch (\Throwable $e) {
            Log::warning('Firebase custom token creation failed: ' . $e->getMessage());
        }

        return $this->success([
            'token'          => $token,
            'token_type'     => 'Bearer',
            'is_new_user'    => $isNewUser,
            'firebase_token' => $firebaseToken,
            'user'           => $this->formatUserData($user),
        ], $isNewUser ? 'Registered with Google successfully' : 'Login with Google successful', $isNewUser ? 201 : 200);
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
     * Link Google account to the currently authenticated user.
     * POST /api/v1/auth/google/link (Protected by auth:sanctum)
     */
    public function linkGoogle(Request $request): JsonResponse
    {
        $request->validate([
            'id_token'     => ['nullable', 'string'],
            'token'        => ['nullable', 'string'],
            'access_token' => ['nullable', 'string'],
        ]);

        $idToken = $request->input('id_token') ?? $request->input('token');
        $accessToken = $request->input('access_token');

        if (empty($idToken) && empty($accessToken)) {
            return $this->error('A valid Google id_token or access_token is required.', 422);
        }

        $googleUserData = null;

        if (!empty($idToken)) {
            try {
                $response = Http::timeout(10)->get('https://oauth2.googleapis.com/tokeninfo', [
                    'id_token' => $idToken,
                ]);
                if ($response->successful()) {
                    $json = $response->json();
                    if (!empty($json['sub']) && !empty($json['email'])) {
                        $googleUserData = [
                            'id'    => (string) $json['sub'],
                            'email' => strtolower(trim($json['email'])),
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Google link verification failed: ' . $e->getMessage());
            }
        }

        if (!$googleUserData && !empty($accessToken)) {
            try {
                $response = Http::timeout(10)
                    ->withToken($accessToken)
                    ->get('https://www.googleapis.com/oauth2/v3/userinfo');
                if ($response->successful()) {
                    $json = $response->json();
                    if (!empty($json['sub']) && !empty($json['email'])) {
                        $googleUserData = [
                            'id'    => (string) $json['sub'],
                            'email' => strtolower(trim($json['email'])),
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Google link userinfo verification failed: ' . $e->getMessage());
            }
        }

        if (!$googleUserData) {
            return $this->error('Invalid or expired Google token.', 401);
        }

        /** @var User $user */
        $user = $request->user();

        // Check if another user already has this google_id
        $existing = User::where('google_id', $googleUserData['id'])->where('id', '!=', $user->id)->first();
        if ($existing) {
            return $this->error('This Google account is already linked to another Viewlix user.', 422);
        }

        $user->google_id = $googleUserData['id'];
        $user->save();

        return $this->success([
            'user' => $this->formatUserData($user),
        ], 'Google account linked successfully');
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


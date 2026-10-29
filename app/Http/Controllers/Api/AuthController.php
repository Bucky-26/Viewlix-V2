<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Authenticate user, verify admin status, generate Sanctum token, and redirect or respond.
     * POST /api/v3/auth/login
     */
    public function login(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'email'        => 'required|email',
            'password'     => 'required|string',
            'redirect_uri' => 'nullable|url',
        ]);

        // 1. Attempt Authentication
        if (Auth::attempt($request->only('email', 'password'))) {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // 2. Security Check: Only allow admins to login to the proxy
            $isAdmin = ($user->is_admin ?? false)
                || (($user->role ?? null) === 'admin')
                || (method_exists($user, 'isAdmin') && $user->isAdmin());

            if (!$isAdmin) {
                Auth::logout();

                if ($request->filled('redirect_uri')) {
                    return redirect()->back()
                        ->withErrors(['email' => 'Access Denied: You do not have administrator privileges.'])
                        ->withInput($request->only('email'));
                }

                return response()->json([
                    'success' => false,
                    'error'   => 'Access Denied: Not an admin.',
                ], 403);
            }

            // 3. Generate a Sanctum Token
            $token = $user->createToken('proxy-auth')->plainTextToken;

            // 4. Handle OAuth-style Redirect
            if ($request->filled('redirect_uri')) {
                $redirectUri = $request->input('redirect_uri');
                $separator = str_contains($redirectUri, '?') ? '&' : '?';
                $redirectUrl = $redirectUri . $separator . 'token=' . urlencode($token);

                return redirect()->away($redirectUrl);
            }

            // Fallback for direct API calls
            return response()->json([
                'success' => true,
                'token'   => $token,
                'user'    => $user,
            ]);
        }

        // Authentication Failed
        if ($request->filled('redirect_uri')) {
            return redirect()->back()
                ->withErrors(['email' => 'Invalid email or password.'])
                ->withInput($request->only('email'));
        }

        return response()->json([
            'success' => false,
            'error'   => 'Invalid credentials',
        ], 401);
    }

    /**
     * Get current authenticated user details.
     * GET /api/v3/auth/me (Protected by auth:sanctum)
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'user'    => $request->user(),
        ]);
    }
}

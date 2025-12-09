<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google callback
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user exists with this Google ID
            $user = User::where('google_id', $googleUser->getId())->first();
            
            if ($user) {
                // Existing Google user - log them in
                Auth::login($user, true);
                return redirect()->intended('/')->with('success', 'Welcome back, ' . $user->name . '!');
            }
            
            // Check if user exists with this email (link accounts)
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if ($user) {
                // Link Google account to existing user
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
                
                Auth::login($user, true);
                return redirect()->intended('/')->with('success', 'Google account linked successfully!');
            }
            
            // Create new user
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => bcrypt(Str::random(24)), // Random password for Google users
                'firebase_uuid' => Str::uuid()->toString(),
                'email_verified_at' => now(), // Google emails are verified
            ]);
            
            Auth::login($user, true);
            
            return redirect('/')->with('success', 'Account created successfully!');
            
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Google authentication failed. Please try again.');
        }
    }

    /**
     * Link Google account to existing logged-in user
     */
    public function link(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle link callback
     */
    public function linkCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = Auth::user();
            
            // Check if this Google account is already linked to another user
            $existingUser = User::where('google_id', $googleUser->getId())
                ->where('id', '!=', $user->id)
                ->first();
            
            if ($existingUser) {
                return redirect('/account')->with('error', 'This Google account is already linked to another user.');
            }
            
            // Link Google account
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
            
            return redirect('/account')->with('success', 'Google account linked successfully!');
            
        } catch (\Exception $e) {
            return redirect('/account')->with('error', 'Failed to link Google account.');
        }
    }

    /**
     * Unlink Google account
     */
    public function unlink(): RedirectResponse
    {
        $user = Auth::user();
        
        // Make sure user has a password set before unlinking
        if (!$user->password || $user->password === '') {
            return redirect('/account')->with('error', 'Please set a password before unlinking Google.');
        }
        
        $user->update([
            'google_id' => null,
            'avatar' => null,
        ]);
        
        return redirect('/account')->with('success', 'Google account unlinked.');
    }
}

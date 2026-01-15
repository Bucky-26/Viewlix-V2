<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Auth\Events\Verified;
use App\Services\FirebaseService;
use App\Services\GoogleAnalyticsService;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminController extends Controller
{
    /**
     * Admin Dashboard
     */
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'admin_users' => User::where('is_admin', true)->count(),
            'google_users' => User::whereNotNull('google_id')->count(),
        ];

        $recentUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers'));
    }

    /**
     * Google Analytics via Firebase
     * Displays an embedded Google Analytics dashboard
     */
    public function analytics()
    {
        // Get Firebase configuration for the view
        $firebaseConfig = [
            'project_id' => config('services.firebase.project_id'),
            'measurement_id' => config('services.firebase.measurement_id'),
        ];

        return view('admin.analytics', compact('firebaseConfig'));
    }

    /**
     * Get analytics data via AJAX
     */
    public function analyticsData(GoogleAnalyticsService $analyticsService)
    {
        $data = $analyticsService->getDashboardStats();
        
        // Add debug info
        $data['debug'] = [
            'property_id_configured' => config('services.google_analytics.property_id'),
            'credentials_exists' => file_exists(storage_path('app/firebase/service-account.json')),
            'service_configured' => $analyticsService->isConfigured(),
        ];
        
        return response()->json($data);
    }

    /**
     * Show a user's favorites (admin)
     */
    public function userFavorites(User $user)
    {
        // If Firebase is configured, prefer reading favorites from Firebase Realtime Database
        try {
            $firebase = app(FirebaseService::class);
        } catch (\Throwable $e) {
            $firebase = null;
        }

        if ($firebase && $firebase->isConfigured()) {
            $firebaseUuid = $user->getFirebaseUuid();
            $path = "users/{$firebaseUuid}/favorites";

            $data = $firebase->getDatabasePath($path);

            $items = collect();

            if (is_array($data)) {
                foreach ($data as $key => $val) {
                    $item = is_array($val) ? $val : ['value' => $val];
                    $item['id'] = $key;
                    $items->push($item);
                }
            }

            // Map firebase items to the view's expected fields
            $mapped = $items->map(function ($i) {
                return [
                    'id' => $i['id'] ?? null,
                    'title' => $i['title'] ?? ($i['name'] ?? null),
                    'poster_url' => $i['poster_url'] ?? ($i['poster_path'] ?? null),
                    'media_type' => $i['media_type'] ?? ($i['type'] ?? null),
                    'release_date' => $i['release_date'] ?? ($i['first_air_date'] ?? null),
                ];
            });

            // Paginate the firebase-backed collection so the existing view's links() still work
            $perPage = 24;
            $page = (int) request()->get('page', 1);
            $total = $mapped->count();
            $offset = ($page - 1) * $perPage;
            $slice = $mapped->slice($offset, $perPage)->values();

            $favorites = new LengthAwarePaginator($slice, $total, $perPage, $page, [
                'path' => request()->url(),
                'query' => request()->query(),
            ]);

            return view('admin.users.favorites', compact('user', 'favorites'));
        }

        // Fallback to database-stored favorites
        $favorites = $user->favorites()->latest()->paginate(24);

        return view('admin.users.favorites', compact('user', 'favorites'));
    }

    /**
     * Show a user's watch history (reads from Firebase Realtime Database)
     */
    public function userWatchHistory(User $user, FirebaseService $firebaseService)
    {
        $firebaseUuid = $user->getFirebaseUuid();
        $path = "users/{$firebaseUuid}/watchProgress";

        $data = $firebaseService->getDatabasePath($path);

        // normalize into array of items sorted by last_updated desc
        $items = collect();

        if (is_array($data)) {
            foreach ($data as $key => $val) {
                $item = is_array($val) ? $val : [];
                $item['id'] = $key;
                $items->push($item);
            }

            $items = $items->sortByDesc(function ($i) {
                return $i['last_updated'] ?? 0;
            })->values();
        }

        // If Realtime DB returned nothing, try Firestore (some clients may write there)
        if ($items->isEmpty()) {
            try {
                $firestoreData = $firebaseService->getFirestorePath($path);
                if (is_array($firestoreData)) {
                    $items = collect();
                    foreach ($firestoreData as $key => $val) {
                        $item = is_array($val) ? $val : [];
                        $item['id'] = $key;
                        $items->push($item);
                    }

                    $items = $items->sortByDesc(function ($i) {
                        return $i['last_updated'] ?? 0;
                    })->values();
                    // replace rawData so view can show Firestore JSON when needed
                    $data = $firestoreData;
                }
            } catch (\Throwable $e) {
                // ignore and continue to DB fallback
            }
        }

        // Provide raw data and config info to the view for debugging
        $firebaseConfigured = $firebaseService->isConfigured();

        $source = 'firebase';

        // If no items found in Firebase, try fallback to DB watch history
        if ($items->count() === 0) {
            $dbHistory = $user->watchHistory()->latest()->get();

            if ($dbHistory->count()) {
                $items = $dbHistory->map(function ($h) {
                    // Map DB model to the same shape the view expects
                    return [
                        'id' => 'db-' . $h->id,
                        'title' => $h->title,
                        'episode_title' => null,
                        'type' => $h->media_type,
                        'progress' => [
                            'watched' => $h->watched_time ?? null,
                            'duration' => $h->duration ?? null,
                        ],
                        'last_updated' => $h->updated_at ? ($h->updated_at->getTimestamp() * 1000) : null,
                    ];
                })->collect();

                $source = 'db';
            }
        }

        return view('admin.users.watch-history', [
            'user' => $user,
            'items' => $items,
            'rawData' => $data,
            'firebaseConfigured' => $firebaseConfigured,
            'firebaseUuid' => $firebaseUuid,
            'source' => $source,
        ]);
    }

    /**
     * Manually verify a user's email (admin action)
     */
    public function verifyUser(User $user)
    {
        if ($user->hasVerifiedEmail()) {
            return back()->with('error', 'User is already verified.');
        }

        $user->email_verified_at = now();
        $user->save();

        // Fire the Verified event so any listeners (e.g., send confirmation email) run
        event(new Verified($user));

        return back()->with('success', 'User has been marked as verified and notified.');
    }

    /**
     * Resend the email verification link to a user (admin action)
     */
    public function resendVerification(User $user)
    {
        if ($user->hasVerifiedEmail()) {
            return back()->with('error', 'User is already verified.');
        }

        // Uses the MustVerifyEmail contract implementation to send the notification
        $user->sendEmailVerificationNotification();

        return back()->with('success', 'Verification link sent to user.');
    }

    /**
     * List all users
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            if ($request->role === 'admin') {
                $query->where('is_admin', true);
            } elseif ($request->role === 'user') {
                $query->where('is_admin', false);
            }
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Edit user form
     */
    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'is_admin' => 'boolean',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_admin' => $request->boolean('is_admin'),
        ]);

        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }

    /**
     * Delete user
     */
    public function deleteUser(User $user)
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle user admin status
     */
    public function toggleAdmin(User $user)
    {
        // Prevent self-demotion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot change your own admin status.');
        }

        $user->update(['is_admin' => !$user->is_admin]);

        $status = $user->is_admin ? 'promoted to admin' : 'demoted to user';

        return back()->with('success', "User {$user->name} has been {$status}.");
    }
}

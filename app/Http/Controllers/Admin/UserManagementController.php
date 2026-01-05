<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    /**
     * Show all users with their session data
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort', 'created_at');
        $sortOrder = $request->input('order', 'desc');

        $query = User::query()
            ->withCount('sessions')
            ->with(['sessions' => function ($q) {
                $q->where('is_active', true)->latest('last_activity_at');
            }]);

        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        }

        $query->orderBy($sortBy, $sortOrder);
        $users = $query->paginate(15);

        return view('admin.users.sessions', compact('users', 'search', 'sortBy', 'sortOrder'));
    }

    /**
     * Show detailed view of a specific user with all sessions
     */
    public function show(User $user)
    {
        $user->load('sessions');
        $activeSessions = $user->sessions()->where('is_active', true)->get();
        $inactiveSessions = $user->sessions()->where('is_active', false)->orderByDesc('logout_at')->limit(10)->get();

        return view('admin.users.session-details', compact('user', 'activeSessions', 'inactiveSessions'));
    }

    /**
     * Logout a specific user session
     */
    public function logoutSession($userId, $sessionId)
    {
        $user = User::findOrFail($userId);
        $session = $user->sessions()->findOrFail($sessionId);
        
        $session->logout();

        return back()->with('success', 'User session has been terminated.');
    }

    /**
     * Logout all sessions for a user
     */
    public function logoutAllSessions(User $user)
    {
        $user->sessions()
            ->where('is_active', true)
            ->update([
                'is_active' => false,
                'logout_at' => now(),
            ]);

        return back()->with('success', 'All user sessions have been terminated.');
    }

    /**
     * Delete a user (soft delete)
     */
    public function delete(User $user)
    {
        if ($user->isAdmin()) {
            return back()->with('error', 'Cannot delete an admin user.');
        }

        // Logout all active sessions first
        $user->sessions()
            ->where('is_active', true)
            ->update([
                'is_active' => false,
                'logout_at' => now(),
            ]);

        // Delete user
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User has been deleted.');
    }
}

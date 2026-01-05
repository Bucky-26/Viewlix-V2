@extends('layouts.admin')

@section('title', $user->name . ' - Sessions')
@section('subtitle', 'View all sessions and activity for this user.')

@section('header-actions')
<a href="{{ route('admin.users-management.index') }}" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-viewlix-border bg-viewlix-gray text-gray-300 shadow-sm hover:bg-viewlix-border hover:text-white focus:outline-none focus:ring-2 focus:ring-viewlix-red transition-colors">
    ← Back to Users
</a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    {{-- User Card --}}
    <div class="lg:col-span-2">
        <div class="bg-viewlix-dark border border-viewlix-border rounded-xl shadow-sm overflow-hidden">
            <div class="p-6">
                <div class="flex items-start justify-between mb-6">
                    <div class="flex items-center gap-x-4">
                        @if($user->avatar)
                            <img class="w-12 h-12 rounded-full object-cover border-2 border-viewlix-border" src="{{ $user->avatar }}" alt="{{ $user->name }}">
                        @else
                            <span class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-br from-viewlix-red to-red-700 border-2 border-viewlix-border flex-shrink-0">
                                <span class="text-sm font-medium text-white leading-none uppercase">{{ substr($user->name, 0, 1) }}</span>
                            </span>
                        @endif
                        <div>
                            <h2 class="text-2xl font-bold text-white">{{ $user->name }}</h2>
                            <p class="text-sm text-gray-400 mt-1">{{ $user->email }}</p>
                            <div class="flex items-center gap-x-2 mt-2">
                                @if($user->email_verified_at)
                                    <span class="inline-flex items-center gap-x-1.5 py-1 px-2 rounded-full text-xs font-medium bg-green-500/10 text-green-500">
                                        <span class="size-1.5 bg-green-500 rounded-full"></span>
                                        Email Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-x-1.5 py-1 px-2 rounded-full text-xs font-medium bg-yellow-500/10 text-yellow-500">
                                        <span class="size-1.5 bg-yellow-500 rounded-full"></span>
                                        Not Verified
                                    </span>
                                @endif
                                @if($user->isAdmin())
                                    <span class="inline-flex items-center gap-x-1.5 py-1 px-2 rounded-full text-xs font-medium bg-viewlix-red/10 text-viewlix-red">
                                        <span class="size-1.5 bg-viewlix-red rounded-full"></span>
                                        Administrator
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($activeSessions->count() > 0)
                        <form action="{{ route('admin.users-management.logout-all', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to logout all sessions for this user?');">
                            @csrf
                            <button type="submit" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-yellow-600/50 bg-yellow-600/10 text-yellow-600 hover:bg-yellow-600/20 focus:outline-none focus:ring-2 focus:ring-yellow-600 transition-colors">
                                🚪 Logout All
                            </button>
                        </form>
                    @endif
                </div>

                <div class="border-t border-viewlix-border pt-6">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-viewlix-gray/30 border border-viewlix-border/50 rounded-lg p-4">
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Total Sessions</p>
                            <p class="text-3xl font-bold text-white">{{ $user->sessions()->count() }}</p>
                        </div>
                        <div class="bg-viewlix-gray/30 border border-viewlix-border/50 rounded-lg p-4">
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Active Sessions</p>
                            <p class="text-3xl font-bold {{ $activeSessions->count() > 0 ? 'text-green-500' : 'text-gray-500' }}">{{ $activeSessions->count() }}</p>
                        </div>
                        <div class="bg-viewlix-gray/30 border border-viewlix-border/50 rounded-lg p-4">
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-2">Member Since</p>
                            <p class="text-lg font-bold text-white">{{ $user->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Stats --}}
    <div>
        <div class="bg-viewlix-dark border border-viewlix-border rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <span>ℹ️</span> Account Info
            </h3>
            <div class="space-y-4">
                <div class="pb-4 border-b border-viewlix-border/30">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Account Status</p>
                    <p class="text-sm text-white font-medium">{{ $user->email_verified_at ? '✓ Active' : '⏳ Pending' }}</p>
                </div>
                <div class="pb-4 border-b border-viewlix-border/30">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Role</p>
                    <p class="text-sm text-white font-medium">{{ $user->isAdmin() ? '👑 Administrator' : '👤 User' }}</p>
                </div>
                <div class="pb-4 border-b border-viewlix-border/30">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Account Created</p>
                    <p class="text-sm text-white font-medium">{{ $user->created_at->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Last Updated</p>
                    <p class="text-sm text-white font-medium">{{ $user->updated_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Active Sessions --}}
@if($activeSessions->count() > 0)
<div class="mb-8">
    <div class="mb-4">
        <h3 class="text-xl font-bold text-white flex items-center gap-2">
            <span class="size-3 bg-green-500 rounded-full animate-pulse"></span>
            Active Sessions ({{ $activeSessions->count() }})
        </h3>
    </div>
    <div class="space-y-4">
        @foreach($activeSessions as $session)
        <div class="bg-viewlix-dark border border-green-500/20 shadow-sm rounded-xl overflow-hidden hover:border-green-500/50 transition-colors">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h4 class="text-lg font-semibold text-white flex items-center gap-3 mb-2">
                            @if(str_contains($session->device_type, 'mobile'))
                                📱 Mobile Device
                            @elseif(str_contains($session->device_type, 'tablet'))
                                📱 Tablet
                            @else
                                💻 Desktop Computer
                            @endif
                        </h4>
                        <p class="text-sm text-gray-400">{{ $session->browser }} on {{ $session->os }}</p>
                    </div>
                    <form action="{{ route('admin.users-management.logout-session', [$user, $session]) }}" method="POST" class="inline" onsubmit="return confirm('Logout this session?');">
                        @csrf
                        <button type="submit" class="py-1.5 px-3 text-sm font-medium rounded-lg border border-red-600/50 bg-red-600/10 text-red-500 hover:bg-red-600/20 focus:outline-none focus:ring-2 focus:ring-red-600 transition-colors">
                            Logout Session
                        </button>
                    </form>
                </div>

                <div class="grid grid-cols-2 gap-4 bg-viewlix-gray/30 rounded-lg p-4 mb-4">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">IP Address</p>
                        <p class="text-sm font-mono text-gray-300">{{ $session->ip_address }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Location</p>
                        <p class="text-sm text-gray-300">
                            @if($session->country || $session->city)
                                🌍 {{ $session->country ?? 'Unknown' }}, {{ $session->city ?? 'Unknown' }}
                            @else
                                <span class="text-gray-500">Unknown Location</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Login Time</p>
                        <p class="text-sm text-gray-300">{{ $session->login_at->format('M d, Y H:i:s') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Last Active</p>
                        <p class="text-sm text-gray-300">{{ $session->last_activity_at->diffForHumans() }}</p>
                    </div>
                </div>

                <div class="pt-3 border-t border-viewlix-border/30">
                    <p class="text-xs text-gray-500 truncate">User Agent: <span class="font-mono">{{ $session->user_agent }}</span></p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@else
<div class="mb-8 bg-viewlix-dark border border-viewlix-border rounded-xl shadow-sm p-8 text-center">
    <p class="text-gray-400">No active sessions</p>
</div>
@endif

{{-- Inactive Sessions --}}
@if($inactiveSessions->count() > 0)
<div>
    <div class="mb-4">
        <h3 class="text-xl font-bold text-white flex items-center gap-2">
            <span class="size-3 bg-gray-500 rounded-full"></span>
            Inactive Sessions (Last 10)
        </h3>
    </div>
    <div class="space-y-3">
        @foreach($inactiveSessions as $session)
        <div class="bg-viewlix-dark border border-viewlix-border/50 shadow-sm rounded-lg p-4 hover:border-viewlix-border transition-colors">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-x-2 mb-2">
                        <h4 class="text-sm font-semibold text-gray-300">
                            @if(str_contains($session->device_type, 'mobile'))
                                📱 Mobile
                            @elseif(str_contains($session->device_type, 'tablet'))
                                📱 Tablet
                            @else
                                💻 Desktop
                            @endif
                        </h4>
                        <span class="text-xs text-gray-500">•</span>
                        <span class="text-xs text-gray-500">{{ $session->browser }}</span>
                        <span class="text-xs text-gray-500">•</span>
                        <span class="text-xs text-gray-500">{{ $session->os }}</span>
                    </div>
                    <div class="flex items-center gap-x-4 text-xs text-gray-400">
                        <span class="font-mono bg-viewlix-gray/50 px-2 py-1 rounded">{{ $session->ip_address }}</span>
                        <span>🌍 {{ $session->country ?? 'Unknown' }}, {{ $session->city ?? 'Unknown' }}</span>
                        <span>Logged out {{ $session->logout_at?->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection

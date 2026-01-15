@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', 'Welcome back! Here\'s what\'s happening with your platform.')

@section('content')
{{-- Stats Grid --}}
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
    {{-- Total Users Card --}}
    <div class="flex flex-col bg-viewlix-dark border border-viewlix-border shadow-sm rounded-xl">
        <div class="p-4 md:p-5">
            <div class="flex items-center gap-x-2">
                <p class="text-xs uppercase tracking-wide text-gray-400">Total users</p>
            </div>
            <div class="mt-1 flex items-center gap-x-2">
                <h3 class="text-xl sm:text-2xl font-medium text-white">{{ number_format($stats['total_users']) }}</h3>
                <span class="flex items-center gap-x-1 text-green-400">
                    <svg class="inline-block size-4 self-center" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/>
                        <polyline points="16 7 22 7 22 13"/>
                    </svg>
                    <span class="inline-block text-sm">Active</span>
                </span>
            </div>
        </div>
    </div>

    {{-- Verified Users Card --}}
    <div class="flex flex-col bg-viewlix-dark border border-viewlix-border shadow-sm rounded-xl">
        <div class="p-4 md:p-5">
            <div class="flex items-center gap-x-2">
                <p class="text-xs uppercase tracking-wide text-gray-400">Verified users</p>
            </div>
            <div class="mt-1 flex items-center gap-x-2">
                <h3 class="text-xl sm:text-2xl font-medium text-white">{{ number_format($stats['verified_users']) }}</h3>
                @if($stats['total_users'] > 0)
                <span class="inline-flex items-center gap-x-1 py-0.5 px-2 rounded-full bg-green-500/10 text-green-400 text-xs">
                    {{ round(($stats['verified_users'] / $stats['total_users']) * 100) }}%
                </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Admin Users Card --}}
    <div class="flex flex-col bg-viewlix-dark border border-viewlix-border shadow-sm rounded-xl">
        <div class="p-4 md:p-5">
            <div class="flex items-center gap-x-2">
                <p class="text-xs uppercase tracking-wide text-gray-400">Administrators</p>
            </div>
            <div class="mt-1 flex items-center gap-x-2">
                <h3 class="text-xl sm:text-2xl font-medium text-white">{{ number_format($stats['admin_users']) }}</h3>
                <span class="inline-flex items-center gap-x-1 py-0.5 px-2 rounded-full bg-viewlix-red/10 text-viewlix-red text-xs">
                    <svg class="size-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                    </svg>
                    Admin
                </span>
            </div>
        </div>
    </div>

    {{-- Google Users Card --}}
    <div class="flex flex-col bg-viewlix-dark border border-viewlix-border shadow-sm rounded-xl">
        <div class="p-4 md:p-5">
            <div class="flex items-center gap-x-2">
                <p class="text-xs uppercase tracking-wide text-gray-400">Google users</p>
            </div>
            <div class="mt-1 flex items-center gap-x-2">
                <h3 class="text-xl sm:text-2xl font-medium text-white">{{ number_format($stats['google_users']) }}</h3>
                <span class="inline-flex items-center gap-x-1 py-0.5 px-2 rounded-full bg-blue-500/10 text-blue-400 text-xs">
                    <svg class="size-3" viewBox="0 0 24 24">
                        <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    </svg>
                    OAuth
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Google Analytics Section --}}
@if(config('services.firebase.project_id') && config('services.firebase.measurement_id'))
{{-- Analytics Stats Cards --}}
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
    {{-- Real-time Active Users --}}
    <div class="flex flex-col bg-viewlix-dark border border-viewlix-border shadow-sm rounded-xl">
        <div class="p-4 md:p-5">
            <div class="flex items-center gap-x-2">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                </span>
                <p class="text-xs uppercase tracking-wide text-gray-400">Active Now</p>
            </div>
            <div class="mt-1 flex items-center gap-x-2">
                <h3 class="text-xl sm:text-2xl font-medium text-white" id="realtime-users">
                    <span class="inline-block w-8 h-6 bg-viewlix-gray/50 rounded animate-pulse"></span>
                </h3>
                <span class="inline-flex items-center gap-x-1 py-0.5 px-2 rounded-full bg-green-500/10 text-green-400 text-xs">
                    Live
                </span>
            </div>
        </div>
    </div>

    {{-- Today's Visitors --}}
    <div class="flex flex-col bg-viewlix-dark border border-viewlix-border shadow-sm rounded-xl">
        <div class="p-4 md:p-5">
            <div class="flex items-center gap-x-2">
                <p class="text-xs uppercase tracking-wide text-gray-400">Today's Visitors</p>
            </div>
            <div class="mt-1 flex items-center gap-x-2">
                <h3 class="text-xl sm:text-2xl font-medium text-white" id="today-users">
                    <span class="inline-block w-12 h-6 bg-viewlix-gray/50 rounded animate-pulse"></span>
                </h3>
                <span class="inline-flex items-center gap-x-1 py-0.5 px-2 rounded-full bg-blue-500/10 text-blue-400 text-xs" id="today-sessions">
                    -- sessions
                </span>
            </div>
        </div>
    </div>

    {{-- 7 Days Visitors --}}
    <div class="flex flex-col bg-viewlix-dark border border-viewlix-border shadow-sm rounded-xl">
        <div class="p-4 md:p-5">
            <div class="flex items-center gap-x-2">
                <p class="text-xs uppercase tracking-wide text-gray-400">Last 7 Days</p>
            </div>
            <div class="mt-1 flex items-center gap-x-2">
                <h3 class="text-xl sm:text-2xl font-medium text-white" id="week-users">
                    <span class="inline-block w-12 h-6 bg-viewlix-gray/50 rounded animate-pulse"></span>
                </h3>
                <span class="inline-flex items-center gap-x-1 py-0.5 px-2 rounded-full bg-purple-500/10 text-purple-400 text-xs" id="week-pageviews">
                    -- views
                </span>
            </div>
        </div>
    </div>

    {{-- 30 Days Visitors --}}
    <div class="flex flex-col bg-viewlix-dark border border-viewlix-border shadow-sm rounded-xl">
        <div class="p-4 md:p-5">
            <div class="flex items-center gap-x-2">
                <p class="text-xs uppercase tracking-wide text-gray-400">Last 30 Days</p>
            </div>
            <div class="mt-1 flex items-center gap-x-2">
                <h3 class="text-xl sm:text-2xl font-medium text-white" id="month-users">
                    <span class="inline-block w-16 h-6 bg-viewlix-gray/50 rounded animate-pulse"></span>
                </h3>
                <span class="inline-flex items-center gap-x-1 py-0.5 px-2 rounded-full bg-orange-500/10 text-orange-400 text-xs" id="month-pageviews">
                    -- views
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Charts Section --}}
<div class="grid lg:grid-cols-2 gap-4 sm:gap-6">
    {{-- Visitors Trend Chart --}}
    <div class="bg-viewlix-dark border border-viewlix-border rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-viewlix-border">
            <h2 class="text-lg font-semibold text-white">Visitors Trend</h2>
            <p class="text-sm text-gray-400">Daily active users over the last 30 days</p>
        </div>
        <div class="p-6">
            <div class="h-64 relative">
                <canvas id="visitorsChart"></canvas>
                <div id="visitorsChartLoader" class="absolute inset-0 flex items-center justify-center bg-viewlix-dark/50">
                    <div class="flex items-center gap-2 text-gray-400">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm">Loading chart...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Page Views Chart --}}
    <div class="bg-viewlix-dark border border-viewlix-border rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-viewlix-border">
            <h2 class="text-lg font-semibold text-white">Page Views</h2>
            <p class="text-sm text-gray-400">Daily page views over the last 30 days</p>
        </div>
        <div class="p-6">
            <div class="h-64 relative">
                <canvas id="pageviewsChart"></canvas>
                <div id="pageviewsChartLoader" class="absolute inset-0 flex items-center justify-center bg-viewlix-dark/50">
                    <div class="flex items-center gap-2 text-gray-400">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm">Loading chart...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Additional Stats Row --}}
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
    {{-- New Users Today --}}
    <div class="flex flex-col bg-viewlix-dark border border-viewlix-border shadow-sm rounded-xl">
        <div class="p-4 md:p-5">
            <div class="flex items-center gap-x-2">
                <p class="text-xs uppercase tracking-wide text-gray-400">New Users Today</p>
            </div>
            <div class="mt-1">
                <h3 class="text-xl sm:text-2xl font-medium text-white" id="today-new-users">
                    <span class="inline-block w-8 h-6 bg-viewlix-gray/50 rounded animate-pulse"></span>
                </h3>
            </div>
        </div>
    </div>

    {{-- Avg Session Duration --}}
    <div class="flex flex-col bg-viewlix-dark border border-viewlix-border shadow-sm rounded-xl">
        <div class="p-4 md:p-5">
            <div class="flex items-center gap-x-2">
                <p class="text-xs uppercase tracking-wide text-gray-400">Avg Session</p>
            </div>
            <div class="mt-1">
                <h3 class="text-xl sm:text-2xl font-medium text-white" id="avg-session">
                    <span class="inline-block w-16 h-6 bg-viewlix-gray/50 rounded animate-pulse"></span>
                </h3>
            </div>
        </div>
    </div>

    {{-- Bounce Rate --}}
    <div class="flex flex-col bg-viewlix-dark border border-viewlix-border shadow-sm rounded-xl">
        <div class="p-4 md:p-5">
            <div class="flex items-center gap-x-2">
                <p class="text-xs uppercase tracking-wide text-gray-400">Bounce Rate</p>
            </div>
            <div class="mt-1">
                <h3 class="text-xl sm:text-2xl font-medium text-white" id="bounce-rate">
                    <span class="inline-block w-12 h-6 bg-viewlix-gray/50 rounded animate-pulse"></span>
                </h3>
            </div>
        </div>
    </div>

    {{-- Total Sessions (30 days) --}}
    <div class="flex flex-col bg-viewlix-dark border border-viewlix-border shadow-sm rounded-xl">
        <div class="p-4 md:p-5">
            <div class="flex items-center gap-x-2">
                <p class="text-xs uppercase tracking-wide text-gray-400">Sessions (30d)</p>
            </div>
            <div class="mt-1">
                <h3 class="text-xl sm:text-2xl font-medium text-white" id="month-sessions">
                    <span class="inline-block w-16 h-6 bg-viewlix-gray/50 rounded animate-pulse"></span>
                </h3>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Recent Users Table --}}
<div class="bg-viewlix-dark border border-viewlix-border rounded-xl shadow-sm overflow-hidden">
    {{-- Header --}}
    <div class="px-6 py-4 flex flex-col md:flex-row md:justify-between md:items-center gap-3 border-b border-viewlix-border">
        <div>
            <h2 class="text-lg font-semibold text-white">Recent Users</h2>
            <p class="text-sm text-gray-400">Latest user registrations on your platform.</p>
        </div>
        <div>
            <div class="flex gap-2">
                <a href="{{ route('admin.users') }}" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-viewlix-border bg-viewlix-gray text-gray-300 shadow-sm hover:bg-viewlix-border hover:text-white focus:outline-none focus:ring-2 focus:ring-viewlix-red transition-colors">
                    View all
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </a>
                <a href="{{ route('admin.users-management.index') }}" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-viewlix-border bg-viewlix-gray text-gray-300 shadow-sm hover:bg-viewlix-border hover:text-white focus:outline-none focus:ring-2 focus:ring-viewlix-red transition-colors" title="Monitor user sessions and IP addresses">
                    Sessions & Activity
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-viewlix-gray/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-start">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">User</span>
                    </th>
                    <th scope="col" class="px-6 py-3 text-start">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Status</span>
                    </th>
                    <th scope="col" class="px-6 py-3 text-start">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Role</span>
                    </th>
                    <th scope="col" class="px-6 py-3 text-start">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Created</span>
                    </th>
                    <th scope="col" class="px-6 py-3 text-end">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Actions</span>
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-viewlix-border">
                @forelse($recentUsers as $user)
                <tr class="hover:bg-viewlix-gray/30 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-x-3">
                            @if($user->avatar)
                                <img class="inline-block size-10 rounded-full object-cover" src="{{ $user->avatar }}" alt="{{ $user->name }}">
                            @else
                                <span class="inline-flex items-center justify-center size-10 rounded-full bg-gradient-to-br from-viewlix-red to-viewlix-red-dark">
                                    <span class="text-sm font-medium text-white leading-none uppercase">{{ substr($user->name, 0, 1) }}</span>
                                </span>
                            @endif
                            <div class="grow">
                                <span class="block text-sm font-semibold text-white">{{ $user->name }}</span>
                                <span class="block text-sm text-gray-400">{{ $user->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-x-2">
                            @if($user->email_verified_at)
                                <span class="py-1 px-2 inline-flex items-center gap-x-1 text-xs font-medium bg-teal-500/10 text-teal-400 rounded-full">
                                    <svg class="size-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                                        <path d="m9 12 2 2 4-4"/>
                                    </svg>
                                    Verified
                                </span>
                            @else
                                <span class="py-1 px-2 inline-flex items-center gap-x-1 text-xs font-medium bg-yellow-500/10 text-yellow-400 rounded-full">
                                    <svg class="size-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="12" x2="12" y1="8" y2="12"/>
                                        <line x1="12" x2="12.01" y1="16" y2="16"/>
                                    </svg>
                                    Pending
                                </span>
                            @endif
                            @if($user->google_id)
                                <span class="py-1 px-1.5 inline-flex items-center text-xs font-medium bg-blue-500/10 text-blue-400 rounded-full">
                                    <svg class="size-3" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    </svg>
                                </span>
                            @endif
                            {{-- Presence indicator --}}
                            @if(method_exists($user, 'isOnline') && $user->isOnline())
                                <span class="py-1 px-2 inline-flex items-center gap-x-2 text-xs font-medium text-green-400">
                                    <span class="inline-block w-2 h-2 rounded-full bg-green-400"></span>
                                    Online
                                </span>
                            @else
                                <span class="py-1 px-2 inline-flex items-center gap-x-2 text-xs font-medium text-gray-400">
                                    <span class="inline-block w-2 h-2 rounded-full bg-gray-600"></span>
                                    Last seen {{ optional($user->lastActive())->diffForHumans() ?? 'N/A' }}
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->is_admin)
                            <span class="py-1 px-2 inline-flex items-center gap-x-1 text-xs font-medium bg-viewlix-red/10 text-viewlix-red rounded-full">
                                <svg class="size-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                                </svg>
                                Admin
                            </span>
                        @else
                            <span class="py-1 px-2 inline-flex items-center text-xs font-medium bg-viewlix-gray text-gray-300 rounded-full">
                                User
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-300">{{ $user->created_at->diffForHumans() }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-end">
                        <a href="{{ route('admin.users.edit', $user) }}" 
                           class="py-1.5 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-viewlix-border text-gray-300 hover:bg-viewlix-gray hover:text-white focus:outline-none focus:ring-2 focus:ring-viewlix-red transition-colors">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/>
                                <path d="m15 5 4 4"/>
                            </svg>
                            Edit
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="size-12 text-gray-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <p class="text-gray-400">No users found</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

@if(config('services.firebase.project_id') && config('services.firebase.measurement_id'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    let visitorsChart = null;
    let pageviewsChart = null;

    // Chart.js default configuration for dark theme
    Chart.defaults.color = '#9ca3af';
    Chart.defaults.borderColor = 'rgba(75, 85, 99, 0.3)';

    // Fetch analytics data from API
    async function fetchAnalyticsData() {
        try {
            const response = await fetch('{{ route("admin.analytics.data") }}');
            const data = await response.json();
            updateDashboard(data);
        } catch (error) {
            console.error('Failed to fetch analytics data:', error);
            showFallbackData();
        }
    }

    // Update dashboard with analytics data
    function updateDashboard(data) {
        // Update realtime users
        const realtimeEl = document.getElementById('realtime-users');
        if (realtimeEl && data.realtime) {
            realtimeEl.textContent = formatNumber(data.realtime.active_users || 0);
        }

        // Update today's stats
        if (data.today) {
            updateElement('today-users', formatNumber(data.today.active_users || 0));
            updateElement('today-sessions', `${formatNumber(data.today.sessions || 0)} sessions`);
            updateElement('today-new-users', formatNumber(data.today.new_users || 0));
            
            // Format session duration
            const avgDuration = data.today.avg_session_duration || 0;
            const minutes = Math.floor(avgDuration / 60);
            const seconds = Math.round(avgDuration % 60);
            updateElement('avg-session', `${minutes}m ${seconds}s`);
            
            updateElement('bounce-rate', `${data.today.bounce_rate || 0}%`);
        }

        // Update week stats
        if (data.week) {
            updateElement('week-users', formatNumber(data.week.active_users || 0));
            updateElement('week-pageviews', `${formatNumber(data.week.page_views || 0)} views`);
        }

        // Update month stats
        if (data.month) {
            updateElement('month-users', formatNumber(data.month.active_users || 0));
            updateElement('month-pageviews', `${formatNumber(data.month.page_views || 0)} views`);
            updateElement('month-sessions', formatNumber(data.month.sessions || 0));
        }

        // Update charts
        if (data.trend) {
            updateCharts(data.trend);
        }

        // Hide loaders
        hideLoader('visitorsChartLoader');
        hideLoader('pageviewsChartLoader');
    }

    // Show fallback when API is not configured
    function showFallbackData() {
        const elements = [
            'realtime-users', 'today-users', 'today-sessions', 'today-new-users',
            'avg-session', 'bounce-rate', 'week-users', 'week-pageviews',
            'month-users', 'month-pageviews', 'month-sessions'
        ];
        
        elements.forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.textContent = '--';
            }
        });

        // Show message in chart areas
        const visitorsLoader = document.getElementById('visitorsChartLoader');
        const pageviewsLoader = document.getElementById('pageviewsChartLoader');
        
        if (visitorsLoader) {
            visitorsLoader.innerHTML = `
                <div class="text-center text-gray-400">
                    <p class="text-sm">Analytics API not configured</p>
                    <p class="text-xs mt-1">Set GOOGLE_ANALYTICS_PROPERTY_ID in .env</p>
                </div>
            `;
        }
        if (pageviewsLoader) {
            pageviewsLoader.innerHTML = `
                <div class="text-center text-gray-400">
                    <p class="text-sm">Analytics API not configured</p>
                    <p class="text-xs mt-1">Set GOOGLE_ANALYTICS_PROPERTY_ID in .env</p>
                </div>
            `;
        }
    }

    // Update a single element
    function updateElement(id, value) {
        const el = document.getElementById(id);
        if (el) {
            el.textContent = value;
        }
    }

    // Format large numbers
    function formatNumber(num) {
        if (num >= 1000000) {
            return (num / 1000000).toFixed(1) + 'M';
        }
        if (num >= 1000) {
            return (num / 1000).toFixed(1) + 'K';
        }
        return num.toLocaleString();
    }

    // Hide loader element
    function hideLoader(id) {
        const el = document.getElementById(id);
        if (el) {
            el.style.display = 'none';
        }
    }

    // Create/update charts
    function updateCharts(trend) {
        const visitorsCtx = document.getElementById('visitorsChart');
        const pageviewsCtx = document.getElementById('pageviewsChart');

        if (!trend.labels || trend.labels.length === 0) {
            return;
        }

        // Visitors Chart
        if (visitorsCtx) {
            if (visitorsChart) {
                visitorsChart.destroy();
            }
            
            visitorsChart = new Chart(visitorsCtx, {
                type: 'line',
                data: {
                    labels: trend.labels,
                    datasets: [{
                        label: 'Active Users',
                        data: trend.active_users || [],
                        borderColor: '#22c55e',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 2,
                        pointHoverRadius: 6,
                    }, {
                        label: 'Sessions',
                        data: trend.sessions || [],
                        borderColor: '#3b82f6',
                        backgroundColor: 'transparent',
                        tension: 0.4,
                        pointRadius: 2,
                        pointHoverRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            titleColor: '#fff',
                            bodyColor: '#9ca3af',
                            borderColor: 'rgba(75, 85, 99, 0.3)',
                            borderWidth: 1,
                            padding: 12,
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                            },
                            ticks: {
                                maxTicksLimit: 10,
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(75, 85, 99, 0.2)',
                            }
                        }
                    }
                }
            });
        }

        // Page Views Chart
        if (pageviewsCtx) {
            if (pageviewsChart) {
                pageviewsChart.destroy();
            }
            
            pageviewsChart = new Chart(pageviewsCtx, {
                type: 'bar',
                data: {
                    labels: trend.labels,
                    datasets: [{
                        label: 'Page Views',
                        data: trend.page_views || [],
                        backgroundColor: 'rgba(168, 85, 247, 0.6)',
                        borderColor: '#a855f7',
                        borderWidth: 1,
                        borderRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false,
                        },
                        tooltip: {
                            backgroundColor: 'rgba(17, 24, 39, 0.9)',
                            titleColor: '#fff',
                            bodyColor: '#9ca3af',
                            borderColor: 'rgba(75, 85, 99, 0.3)',
                            borderWidth: 1,
                            padding: 12,
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false,
                            },
                            ticks: {
                                maxTicksLimit: 10,
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(75, 85, 99, 0.2)',
                            }
                        }
                    }
                }
            });
        }
    }

    // Initial fetch
    fetchAnalyticsData();

    // Refresh every 5 minutes
    setInterval(fetchAnalyticsData, 5 * 60 * 1000);
});
</script>
@endif
@endpush

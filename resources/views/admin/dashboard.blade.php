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

{{-- Recent Users Table --}}
<div class="bg-viewlix-dark border border-viewlix-border rounded-xl shadow-sm overflow-hidden">
    {{-- Header --}}
    <div class="px-6 py-4 flex flex-col md:flex-row md:justify-between md:items-center gap-3 border-b border-viewlix-border">
        <div>
            <h2 class="text-lg font-semibold text-white">Recent Users</h2>
            <p class="text-sm text-gray-400">Latest user registrations on your platform.</p>
        </div>
        <div>
            <a href="{{ route('admin.users') }}" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-viewlix-border bg-viewlix-gray text-gray-300 shadow-sm hover:bg-viewlix-border hover:text-white focus:outline-none focus:ring-2 focus:ring-viewlix-red transition-colors">
                View all
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m9 18 6-6-6-6"/>
                </svg>
            </a>
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

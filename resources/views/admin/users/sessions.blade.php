@extends('layouts.admin')

@section('title', 'User Sessions & Activity')
@section('subtitle', 'Monitor user sessions, IP addresses, and device information.')

@section('header-actions')
{{-- Search --}}
<div class="relative">
    <form action="{{ route('admin.users-management.index') }}" method="GET" class="flex items-center gap-2">
        <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                <svg class="shrink-0 size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
            </div>
            <input type="text" 
                   name="search" 
                   value="{{ $search }}"
                   class="py-2 ps-9 pe-4 block w-full md:w-64 bg-viewlix-gray border-viewlix-border rounded-lg text-sm text-white placeholder-gray-400 focus:border-viewlix-red focus:ring-viewlix-red" 
                   placeholder="Search users...">
        </div>
        <button type="submit" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-viewlix-border bg-viewlix-red text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-viewlix-red">
            Search
        </button>
        @if($search)
            <a href="{{ route('admin.users-management.index') }}" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-viewlix-border bg-viewlix-gray text-gray-300 hover:bg-viewlix-border hover:text-white">
                Clear
            </a>
        @endif
    </form>
</div>
@endsection

@section('content')
<div class="bg-viewlix-dark border border-viewlix-border rounded-xl shadow-sm overflow-hidden">
    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-viewlix-gray/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-start">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">User</span>
                    </th>
                    <th scope="col" class="px-6 py-3 text-start">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Active Sessions</span>
                    </th>
                    <th scope="col" class="px-6 py-3 text-start">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Current IP</span>
                    </th>
                    <th scope="col" class="px-6 py-3 text-start">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Location</span>
                    </th>
                    <th scope="col" class="px-6 py-3 text-start">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Device / OS</span>
                    </th>
                    <th scope="col" class="px-6 py-3 text-start">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Last Active</span>
                    </th>
                    <th scope="col" class="px-6 py-3 text-end">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Actions</span>
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-viewlix-border">
                @forelse($users as $user)
                <tr class="hover:bg-viewlix-gray/30 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-x-3">
                            @if($user->avatar)
                                <img class="w-10 h-10 rounded-full object-cover" src="{{ $user->avatar }}" alt="{{ $user->name }}">
                            @else
                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-viewlix-red to-red-700 flex-shrink-0">
                                    <span class="text-xs font-medium text-white leading-none uppercase">{{ substr($user->name, 0, 1) }}</span>
                                </span>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-white">{{ $user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center gap-x-1.5 py-1.5 px-3 rounded-full text-xs font-medium {{ $user->sessions_count > 0 ? 'bg-green-500/10 text-green-500' : 'bg-gray-500/10 text-gray-500' }}">
                            <span class="size-2 {{ $user->sessions_count > 0 ? 'bg-green-500' : 'bg-gray-500' }} rounded-full"></span>
                            {{ $user->sessions_count }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->sessions && $user->sessions->first())
                            <span class="text-sm text-gray-300 font-mono">{{ $user->sessions->first()->ip_address }}</span>
                        @else
                            <span class="text-sm text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->sessions && $user->sessions->first())
                            <div class="text-sm">
                                <p class="text-gray-300">🌍 {{ $user->sessions->first()->country ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">{{ $user->sessions->first()->city ?? 'Unknown' }}</p>
                            </div>
                        @else
                            <span class="text-sm text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($user->sessions && $user->sessions->first())
                            <div class="text-sm">
                                <p class="text-gray-300">
                                    @if(str_contains($user->sessions->first()->device_type, 'mobile'))
                                        📱 Mobile
                                    @elseif(str_contains($user->sessions->first()->device_type, 'tablet'))
                                        📱 Tablet
                                    @else
                                        💻 Desktop
                                    @endif
                                </p>
                                <p class="text-xs text-gray-500">{{ $user->sessions->first()->os ?? 'Unknown' }} • {{ $user->sessions->first()->browser ?? 'Unknown' }}</p>
                            </div>
                        @else
                            <span class="text-sm text-gray-500">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm">
                            @if($user->sessions && $user->sessions->first())
                                <p class="text-gray-300">{{ $user->sessions->first()->last_activity_at?->diffForHumans() ?? 'Never' }}</p>
                                <p class="text-xs text-gray-500">{{ $user->sessions->first()->last_activity_at?->format('M d, Y H:i') }}</p>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-end">
                        <div class="flex justify-end gap-x-2">
                            <a href="{{ route('admin.users-management.show', $user) }}" class="inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-transparent text-viewlix-red hover:text-red-400 focus:outline-none focus:ring-2 focus:ring-viewlix-red focus:ring-offset-2 focus:ring-offset-viewlix-darker">
                                View
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <p class="text-gray-400">No users found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if($users->hasPages())
<div class="mt-6">
    {{ $users->links() }}
</div>
@endif
@endsection

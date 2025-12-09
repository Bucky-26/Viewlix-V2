@extends('layouts.admin')

@section('title', 'Users')
@section('subtitle', 'Manage all registered users on your platform.')

@section('header-actions')
{{-- Search --}}
<div class="relative">
    <form action="{{ route('admin.users') }}" method="GET" class="flex items-center gap-2">
        @if(request('role'))
            <input type="hidden" name="role" value="{{ request('role') }}">
        @endif
        <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                <svg class="shrink-0 size-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <path d="m21 21-4.3-4.3"/>
                </svg>
            </div>
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}"
                   class="py-2 ps-9 pe-4 block w-full md:w-64 bg-viewlix-gray border-viewlix-border rounded-lg text-sm text-white placeholder-gray-400 focus:border-viewlix-red focus:ring-viewlix-red" 
                   placeholder="Search users...">
        </div>
    </form>
</div>

{{-- Filter Dropdown --}}
<div x-data="{ open: false }" class="relative inline-block">
    <button @click="open = !open" type="button" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-viewlix-border bg-viewlix-gray text-gray-300 hover:bg-viewlix-border hover:text-white focus:outline-none focus:ring-2 focus:ring-viewlix-red">
        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 6h18"/>
            <path d="M7 12h10"/>
            <path d="M10 18h4"/>
        </svg>
        Filter
        @if(request('role'))
            <span class="py-0.5 px-1.5 rounded-full text-xs font-medium bg-viewlix-red text-white">1</span>
        @endif
    </button>
    <div x-show="open" 
         @click.outside="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 min-w-48 bg-viewlix-darker border border-viewlix-border shadow-lg rounded-xl p-2 mt-2 z-50">
        <span class="block py-2 px-3 text-xs font-medium uppercase text-gray-400">Role</span>
        <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm {{ !request('role') ? 'bg-viewlix-red text-white' : 'text-gray-300 hover:bg-viewlix-gray hover:text-white' }}" href="{{ route('admin.users', array_merge(request()->except('role'), [])) }}">
            All Users
        </a>
        <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm {{ request('role') === 'admin' ? 'bg-viewlix-red text-white' : 'text-gray-300 hover:bg-viewlix-gray hover:text-white' }}" href="{{ route('admin.users', array_merge(request()->except('role'), ['role' => 'admin'])) }}">
            Administrators
        </a>
        <a class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm {{ request('role') === 'user' ? 'bg-viewlix-red text-white' : 'text-gray-300 hover:bg-viewlix-gray hover:text-white' }}" href="{{ route('admin.users', array_merge(request()->except('role'), ['role' => 'user'])) }}">
            Regular Users
        </a>
    </div>
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
                @forelse($users as $user)
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
                                <span class="py-1 px-1.5 inline-flex items-center gap-x-1 text-xs font-medium bg-blue-500/10 text-blue-400 rounded-full">
                                    <svg class="size-3" viewBox="0 0 24 24">
                                        <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                    </svg>
                                    Google
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
                        <span class="text-sm text-gray-300">{{ $user->created_at->format('M d, Y') }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center justify-end gap-2">
                            {{-- Toggle Admin Button --}}
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.toggle-admin', $user) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        title="{{ $user->is_admin ? 'Demote to User' : 'Promote to Admin' }}"
                                        class="py-1.5 px-2 inline-flex justify-center items-center rounded-lg text-gray-300 hover:text-white hover:bg-viewlix-gray focus:outline-none focus:ring-2 focus:ring-viewlix-red transition-colors">
                                    @if($user->is_admin)
                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m7 15 5 5 5-5"/>
                                            <path d="m7 9 5-5 5 5"/>
                                        </svg>
                                    @else
                                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                                        </svg>
                                    @endif
                                </button>
                            </form>
                            @endif

                            {{-- Edit Button --}}
                            <a href="{{ route('admin.users.edit', $user) }}" 
                               title="Edit User"
                               class="py-1.5 px-2 inline-flex justify-center items-center rounded-lg text-gray-300 hover:text-white hover:bg-viewlix-gray focus:outline-none focus:ring-2 focus:ring-viewlix-red transition-colors">
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/>
                                    <path d="m15 5 4 4"/>
                                </svg>
                            </a>

                            {{-- Delete Button --}}
                            @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.delete', $user) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        title="Delete User"
                                        class="py-1.5 px-2 inline-flex justify-center items-center rounded-lg text-gray-300 hover:text-red-400 hover:bg-red-500/10 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 6h18"/>
                                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                        <line x1="10" x2="10" y1="11" y2="17"/>
                                        <line x1="14" x2="14" y1="11" y2="17"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
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
                            @if(request('search'))
                                <a href="{{ route('admin.users') }}" class="text-sm text-viewlix-red hover:underline">Clear search</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
    <div class="px-6 py-4 border-t border-viewlix-border">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <p class="text-sm text-gray-400">
                Showing <span class="font-medium text-white">{{ $users->firstItem() }}</span> to <span class="font-medium text-white">{{ $users->lastItem() }}</span> of <span class="font-medium text-white">{{ $users->total() }}</span> results
            </p>
            <nav class="flex items-center gap-x-1" aria-label="Pagination">
                {{-- Previous --}}
                @if($users->onFirstPage())
                    <span class="min-h-[38px] min-w-[38px] py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm rounded-lg text-gray-500 cursor-not-allowed">
                        <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m15 18-6-6 6-6"/>
                        </svg>
                        <span class="sr-only">Previous</span>
                    </span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="min-h-[38px] min-w-[38px] py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm rounded-lg text-gray-300 hover:text-white hover:bg-viewlix-gray focus:outline-none focus:ring-2 focus:ring-viewlix-red">
                        <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m15 18-6-6 6-6"/>
                        </svg>
                        <span class="sr-only">Previous</span>
                    </a>
                @endif

                {{-- Page Numbers --}}
                <div class="flex items-center gap-x-1">
                    @foreach($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                        @if($page == $users->currentPage())
                            <span class="min-h-[38px] min-w-[38px] flex justify-center items-center bg-viewlix-red text-white py-2 px-3 text-sm rounded-lg">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="min-h-[38px] min-w-[38px] flex justify-center items-center text-gray-300 hover:text-white hover:bg-viewlix-gray py-2 px-3 text-sm rounded-lg">{{ $page }}</a>
                        @endif
                    @endforeach
                </div>

                {{-- Next --}}
                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="min-h-[38px] min-w-[38px] py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm rounded-lg text-gray-300 hover:text-white hover:bg-viewlix-gray focus:outline-none focus:ring-2 focus:ring-viewlix-red">
                        <span class="sr-only">Next</span>
                        <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </a>
                @else
                    <span class="min-h-[38px] min-w-[38px] py-2 px-2.5 inline-flex justify-center items-center gap-x-1.5 text-sm rounded-lg text-gray-500 cursor-not-allowed">
                        <span class="sr-only">Next</span>
                        <svg class="shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </span>
                @endif
            </nav>
        </div>
    </div>
    @endif
</div>
@endsection

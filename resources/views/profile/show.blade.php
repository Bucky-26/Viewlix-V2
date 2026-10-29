@extends('layouts.app')

@section('title', $user->name . ' - Profile | Viewlix')

@section('content')
<div class="min-h-screen pb-16 pt-6 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
    {{-- Top Alert Messages --}}
    @if(session('status') === 'profile-updated')
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl flex items-center justify-between shadow-lg shadow-emerald-500/5">
            <div class="flex items-center gap-3 text-emerald-400 text-sm font-medium">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Profile updated successfully!</span>
            </div>
            <button @click="show = false" class="text-emerald-400/60 hover:text-emerald-400 text-lg leading-none">&times;</button>
        </div>
    @endif

    @if(session('status') === 'avatar-updated')
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 p-4 bg-blue-500/10 border border-blue-500/30 rounded-2xl flex items-center justify-between shadow-lg shadow-blue-500/5">
            <div class="flex items-center gap-3 text-blue-400 text-sm font-medium">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>{{ session('avatar_status_detail', 'Profile picture updated successfully.') }}</span>
            </div>
            <button @click="show = false" class="text-blue-400/60 hover:text-blue-400 text-lg leading-none">&times;</button>
        </div>
    @endif

    @if(session('status') === 'avatar-deleted')
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 p-4 bg-gray-500/10 border border-gray-500/30 rounded-2xl flex items-center justify-between">
            <div class="flex items-center gap-3 text-gray-300 text-sm font-medium">
                <svg class="w-5 h-5 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                <span>Profile picture removed.</span>
            </div>
            <button @click="show = false" class="text-gray-400 hover:text-white text-lg leading-none">&times;</button>
        </div>
    @endif

    {{-- Main Profile Hero Banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-[#161726]/90 via-[#0f1019]/90 to-[#0c0d15]/95 border border-white/10 shadow-2xl backdrop-blur-2xl mb-8">
        {{-- Ambient decorative light effects --}}
        <div class="absolute -top-24 -left-20 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -top-24 -right-20 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 inset-x-0 h-px bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>

        <div class="relative p-6 sm:p-8 lg:p-10">
            <div class="flex flex-col md:flex-row items-center md:items-start justify-between gap-6">
                {{-- Left: Avatar & Identity --}}
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 text-center sm:text-left">
                    {{-- Avatar Container with Glow & Badge --}}
                    <div class="relative group">
                        <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-3xl p-1 bg-gradient-to-tr from-viewlix-red via-blue-500 to-indigo-500 shadow-[0_0_25px_rgba(0,74,173,0.45)]">
                            @if($user->avatar)
                                <img src="{{ $user->avatar }}" 
                                     alt="{{ $user->name }}" 
                                     class="w-full h-full rounded-[22px] object-cover bg-[#0d0e17]">
                            @else
                                <div class="w-full h-full rounded-[22px] bg-gradient-to-tr from-[#0f172a] to-[#1e1b4b] flex items-center justify-center text-white text-4xl font-extrabold uppercase tracking-wider">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>

                        {{-- Quick Avatar Edit Action Button --}}
                        <a href="{{ route('profile.edit') }}#avatar" 
                           class="absolute -bottom-2 -right-2 p-2.5 rounded-xl bg-[#0f1019] text-gray-200 border border-white/20 shadow-lg hover:bg-viewlix-red hover:text-white hover:border-viewlix-red transition-all" 
                           title="Change profile avatar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </a>
                    </div>

                    {{-- User Details --}}
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2.5">
                            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">{{ $user->name }}</h1>
                            
                            @if($user->isAdmin())
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-red-500/15 text-red-400 border border-red-500/30 shadow-[0_0_12px_rgba(239,68,68,0.2)]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400 animate-pulse"></span>
                                    Administrator
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/15 text-blue-300 border border-blue-500/30 shadow-[0_0_12px_rgba(59,130,246,0.2)]">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                                    Member
                                </span>
                            @endif

                            @if($user->hasVerifiedEmail())
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20" title="Email verified">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Verified
                                </span>
                            @endif
                        </div>

                        <p class="text-gray-400 text-sm font-medium flex items-center justify-center sm:justify-start gap-2">
                            <span>{{ $user->email }}</span>
                        </p>

                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 pt-1 text-xs text-gray-400">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Joined {{ $user->created_at ? $user->created_at->format('M Y') : '2026' }}
                            </span>


                            @if($user->google_id)
                                <span class="flex items-center gap-1 text-emerald-400 text-[11px] font-medium">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24">
                                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                                    </svg>
                                    Google Linked
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Right: Quick Action Buttons --}}
                <div class="flex flex-wrap items-center justify-center md:justify-end gap-3 w-full md:w-auto">
                    <a href="{{ route('profile.edit') }}" 
                       class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-viewlix-red to-blue-600 hover:from-blue-600 hover:to-indigo-600 text-white text-sm font-semibold shadow-lg shadow-blue-500/20 hover:shadow-blue-500/40 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Profile & Settings
                    </a>

                    @if($user->isAdmin())
                        <a href="{{ route('admin.gate.enter') }}" 
                           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white/[0.04] hover:bg-white/[0.08] text-red-400 hover:text-red-300 border border-red-500/30 text-sm font-semibold transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Admin Panel
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
        {{-- Favorites Stat --}}
        <a href="{{ route('favorites') }}" class="group relative p-5 sm:p-6 rounded-2xl bg-[#13141f]/70 hover:bg-[#181926]/90 border border-white/5 hover:border-rose-500/30 shadow-xl backdrop-blur-xl transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Saved Favorites</span>
                <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center group-hover:scale-110 group-hover:bg-rose-500/20 transition-all">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-white group-hover:text-rose-400 transition-colors">
                {{ number_format($favoritesCount) }}
            </div>
            <p class="text-xs text-gray-500 mt-1 flex items-center gap-1 group-hover:text-gray-400">
                <span>View favorites list</span>
                <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </p>
        </a>

        {{-- Watch History Stat --}}
        <a href="{{ route('watch-history') }}" class="group relative p-5 sm:p-6 rounded-2xl bg-[#13141f]/70 hover:bg-[#181926]/90 border border-white/5 hover:border-blue-500/30 shadow-xl backdrop-blur-xl transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Watch History</span>
                <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-400 flex items-center justify-center group-hover:scale-110 group-hover:bg-blue-500/20 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-white group-hover:text-blue-400 transition-colors">
                {{ number_format($watchHistoryCount) }}
            </div>
            <p class="text-xs text-gray-500 mt-1 flex items-center gap-1 group-hover:text-gray-400">
                <span>Continue watching</span>
                <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </p>
        </a>

        {{-- Active Devices & Sessions Stat --}}
        <a href="{{ route('profile.edit') }}#security" class="group relative p-5 sm:p-6 rounded-2xl bg-[#13141f]/70 hover:bg-[#181926]/90 border border-white/5 hover:border-emerald-500/30 shadow-xl backdrop-blur-xl transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Active Devices</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center group-hover:scale-110 group-hover:bg-emerald-500/20 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-white group-hover:text-emerald-400 transition-colors">
                {{ max(1, $sessionsCount) }}
            </div>
            <p class="text-xs text-gray-500 mt-1 flex items-center gap-1 group-hover:text-gray-400">
                <span>Manage device security</span>
                <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </p>
        </a>

        {{-- Support Desk Stat --}}
        @if(Auth::user() && Auth::user()->isAdmin())
            <a href="{{ route('admin.support.index') }}" class="group relative p-5 sm:p-6 rounded-2xl bg-[#13141f]/70 hover:bg-[#181926]/90 border border-white/5 hover:border-purple-500/30 shadow-xl backdrop-blur-xl transition-all duration-300">
        @else
            <a href="javascript:void(0)" onclick="const btn = document.getElementById('chat-support-toggle-btn'); if(btn) btn.click();" class="group relative p-5 sm:p-6 rounded-2xl bg-[#13141f]/70 hover:bg-[#181926]/90 border border-white/5 hover:border-purple-500/30 shadow-xl backdrop-blur-xl transition-all duration-300">
        @endif
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Live Support</span>
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center group-hover:scale-110 group-hover:bg-purple-500/20 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                    </svg>
                </div>
            </div>
            <div class="text-2xl sm:text-3xl font-black text-white group-hover:text-purple-400 transition-colors">
                {{ $ticketsCount }}
            </div>
            <p class="text-xs text-gray-500 mt-1 flex items-center gap-1 group-hover:text-gray-400">
                <span>Open chat session</span>
                <svg class="w-3 h-3 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </p>
        </a>
    </div>

    {{-- Content Grid: Recent Favorites & Recent Watch History --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- Left: Favorites Showcase --}}
        <div class="p-6 rounded-3xl bg-[#13141f]/80 border border-white/10 backdrop-blur-xl shadow-xl flex flex-col">
            <div class="flex items-center justify-between mb-5 pb-4 border-b border-white/5">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-rose-500/15 text-rose-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white">Recent Favorites</h2>
                        <p class="text-xs text-gray-400">Movies & shows saved to your library</p>
                    </div>
                </div>
                <a href="{{ route('favorites') }}" class="text-xs font-semibold text-blue-400 hover:text-blue-300 flex items-center gap-1">
                    <span>View all ({{ $favoritesCount }})</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            @if($recentFavorites->isNotEmpty())
                <div class="grid grid-cols-3 sm:grid-cols-6 gap-3 flex-1">
                    @foreach($recentFavorites as $fav)
                        <div class="group relative rounded-xl overflow-hidden bg-white/5 border border-white/10 aspect-[2/3] hover:border-viewlix-red transition-all">
                            @if($fav->poster_path)
                                <img src="https://image.tmdb.org/t/p/w300{{ $fav->poster_path }}" 
                                     alt="{{ $fav->title }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                     loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center p-2 text-center text-xs text-gray-400 bg-gray-900">
                                    {{ $fav->title }}
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-2 flex flex-col justify-end">
                                <span class="text-[10px] font-bold text-white line-clamp-2">{{ $fav->title }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center py-10 text-center px-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/[0.03] border border-white/10 flex items-center justify-center text-gray-500 mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-300">No favorites saved yet</p>
                    <p class="text-xs text-gray-500 mt-1 mb-4">Click the heart button on any movie or show to bookmark it here.</p>
                    <a href="{{ route('movies') }}" class="px-4 py-2 rounded-xl bg-white/[0.06] hover:bg-white/[0.12] text-xs font-semibold text-white transition-all">
                        Browse Movies
                    </a>
                </div>
            @endif
        </div>

        {{-- Right: Watch History Showcase --}}
        <div class="p-6 rounded-3xl bg-[#13141f]/80 border border-white/10 backdrop-blur-xl shadow-xl flex flex-col">
            <div class="flex items-center justify-between mb-5 pb-4 border-b border-white/5">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/15 text-blue-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-white">Recent Watch History</h2>
                        <p class="text-xs text-gray-400">Pick up right where you left off</p>
                    </div>
                </div>
                <a href="{{ route('watch-history') }}" class="text-xs font-semibold text-blue-400 hover:text-blue-300 flex items-center gap-1">
                    <span>View all ({{ $watchHistoryCount }})</span>
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            @if($recentWatchHistory->isNotEmpty())
                <div class="grid grid-cols-3 sm:grid-cols-6 gap-3 flex-1">
                    @foreach($recentWatchHistory as $item)
                        <div class="group relative rounded-xl overflow-hidden bg-white/5 border border-white/10 aspect-[2/3] hover:border-blue-500 transition-all">
                            @if($item->poster_path)
                                <img src="https://image.tmdb.org/t/p/w300{{ $item->poster_path }}" 
                                     alt="{{ $item->title }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                     loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center p-2 text-center text-xs text-gray-400 bg-gray-900">
                                    {{ $item->title }}
                                </div>
                            @endif
                            {{-- Play overlay & progress bar --}}
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-transparent flex flex-col justify-end p-2">
                                <span class="text-[10px] font-bold text-white line-clamp-1">{{ $item->title }}</span>
                                @if($item->progress > 0)
                                    <div class="w-full bg-white/20 h-1 rounded-full mt-1 overflow-hidden">
                                        <div class="bg-viewlix-red h-full rounded-full" style="width: {{ min(100, $item->progress) }}%"></div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center py-10 text-center px-4">
                    <div class="w-14 h-14 rounded-2xl bg-white/[0.03] border border-white/10 flex items-center justify-center text-gray-500 mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-300">No watch history yet</p>
                    <p class="text-xs text-gray-500 mt-1 mb-4">Start watching any movie or anime to track your playback progress.</p>
                    <a href="{{ route('home') }}" class="px-4 py-2 rounded-xl bg-white/[0.06] hover:bg-white/[0.12] text-xs font-semibold text-white transition-all">
                        Explore Trending
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Connected Accounts & Storage Card --}}
    <div class="p-6 sm:p-8 rounded-3xl bg-[#13141f]/80 border border-white/10 backdrop-blur-xl shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 pb-4 border-b border-white/5">
            <div>
                <h3 class="text-lg font-bold text-white">Account Security & Profile Preferences</h3>
                <p class="text-xs text-gray-400">Manage your linked accounts and profile photo</p>
            </div>
            <a href="{{ route('profile.edit') }}#security" class="text-xs font-semibold text-blue-400 hover:text-blue-300 flex items-center gap-1">
                <span>Manage Security Settings</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Google OAuth Status --}}
            <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5 flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-white flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-white">Google Account</div>
                        @if($user->google_id)
                            <div class="text-xs text-emerald-400 flex items-center gap-1 mt-0.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                Connected
                            </div>
                        @else
                            <div class="text-xs text-gray-500 mt-0.5">Not linked</div>
                        @endif
                    </div>
                </div>

                @if($user->google_id)
                    <form action="{{ route('auth.google.unlink') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-white/[0.05] hover:bg-red-500/20 text-gray-300 hover:text-red-400 border border-white/10 text-xs font-semibold transition-all">
                            Unlink
                        </button>
                    </form>
                @else
                    <a href="{{ route('auth.google.link') }}" class="px-3.5 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold shadow-md shadow-blue-500/20 transition-all">
                        Link
                    </a>
                @endif
            </div>

            {{-- Profile Photo Status --}}
            <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5 flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-blue-500/10 text-blue-400 border border-blue-500/20 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-white">Profile Photo</div>
                        <div class="text-xs text-gray-400 mt-0.5">
                            {{ $user->avatar ? 'Custom photo set' : 'Default initials avatar' }}
                        </div>
                    </div>
                </div>

                <a href="{{ route('profile.edit') }}#avatar" class="px-3.5 py-1.5 rounded-xl bg-white/[0.05] hover:bg-white/[0.1] text-gray-300 text-xs font-semibold border border-white/10 transition-all">
                    Change
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

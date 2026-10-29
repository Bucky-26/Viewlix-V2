@extends('layouts.app')

@section('title', 'Edit Profile & Settings - Viewlix')

@section('content')
<div class="min-h-screen pb-16 pt-6 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto"
     x-data="{
         isMobile: window.innerWidth < 640,
         activeTab: (window.innerWidth < 640 && !window.location.hash) ? null : (window.location.hash.replace('#', '') || 'profile'),
         setTab(tab) {
             this.activeTab = tab;
             window.location.hash = tab;
             window.scrollTo(0, 0);
         },
         backToMenu() {
             this.activeTab = null;
             history.pushState('', document.title, window.location.pathname + window.location.search);
         }
     }"
     @resize.window="isMobile = window.innerWidth < 640; if (!isMobile && !activeTab) activeTab = 'profile';"
     x-init="
         window.addEventListener('hashchange', () => {
             const hash = window.location.hash.replace('#', '');
             if (hash) activeTab = hash;
         });
     ">

    {{-- Breadcrumb & Back to Profile --}}
    <div class="flex items-center justify-between gap-4 mb-6">
        <a href="{{ route('profile.show') }}" 
           class="inline-flex items-center gap-2 text-xs font-semibold text-gray-400 hover:text-white transition-colors group">
            <div class="w-8 h-8 rounded-xl bg-white/[0.04] border border-white/10 flex items-center justify-center group-hover:bg-white/[0.08] group-hover:-translate-x-0.5 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </div>
            <span>Back to Profile Overview</span>
        </a>

    </div>

    {{-- Header Section --}}
    <div class="mb-8">
        <h1 class="text-3xl font-black text-white tracking-tight">Account Settings</h1>
        <p class="text-gray-400 text-sm mt-1">Manage your profile photo, security credentials, and system preferences</p>
    </div>

    {{-- Global Status Alerts --}}
    @if(session('status') === 'profile-updated')
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl flex items-center justify-between shadow-lg shadow-emerald-500/5">
            <div class="flex items-center gap-3 text-emerald-400 text-sm font-medium">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Profile information updated successfully.</span>
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

    @if(session('status') === 'settings-updated')
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl flex items-center justify-between shadow-lg shadow-emerald-500/5">
            <div class="flex items-center gap-3 text-emerald-400 text-sm font-medium">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Playback and display settings updated.</span>
            </div>
            <button @click="show = false" class="text-emerald-400/60 hover:text-emerald-400 text-lg leading-none">&times;</button>
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

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/30 rounded-2xl text-emerald-400 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-transition class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 rounded-2xl text-rose-400 text-sm">
            {{ session('error') }}
        </div>
    @endif

    {{-- Desktop Tabs Navigation Bar --}}
    <div class="hidden sm:flex items-center gap-2 p-1.5 mb-8 rounded-2xl bg-[#11121d] border border-white/10 overflow-x-auto modern-scrollbar">
        {{-- Tab 1: Profile & Avatar --}}
        <button type="button" 
                @click="setTab('profile')"
                :class="(activeTab === 'profile' || activeTab === 'avatar') ? 'bg-gradient-to-r from-viewlix-red to-blue-600 text-white shadow-lg shadow-blue-500/20' : 'text-gray-400 hover:text-white hover:bg-white/[0.04]'"
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span>Profile & Avatar</span>
        </button>

        {{-- Tab: Playback Settings --}}
        <button type="button" 
                @click="setTab('settings')"
                :class="activeTab === 'settings' ? 'bg-gradient-to-r from-viewlix-red to-blue-600 text-white shadow-lg shadow-blue-500/20' : 'text-gray-400 hover:text-white hover:bg-white/[0.04]'"
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span>Playback Settings</span>
        </button>

        {{-- Tab 2: Security & Password --}}
        <button type="button" 
                @click="setTab('security')"
                :class="activeTab === 'security' ? 'bg-gradient-to-r from-viewlix-red to-blue-600 text-white shadow-lg shadow-blue-500/20' : 'text-gray-400 hover:text-white hover:bg-white/[0.04]'"
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <span>Security & Password</span>
        </button>

        {{-- Tab 3: Connected Accounts --}}
        <button type="button" 
                @click="setTab('accounts')"
                :class="activeTab === 'accounts' ? 'bg-gradient-to-r from-viewlix-red to-blue-600 text-white shadow-lg shadow-blue-500/20' : 'text-gray-400 hover:text-white hover:bg-white/[0.04]'"
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
            <span>Linked Accounts</span>
        </button>

        {{-- Tab 4: Danger Zone --}}
        <button type="button" 
                @click="setTab('danger')"
                :class="activeTab === 'danger' ? 'bg-rose-600 text-white shadow-lg shadow-rose-600/30' : 'text-rose-400/70 hover:text-rose-400 hover:bg-rose-500/10'"
                class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap ml-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <span>Danger Zone</span>
        </button>
    </div>

    {{-- Mobile Menu List (Hidden on Desktop) --}}
    <div x-show="isMobile && !activeTab" class="sm:hidden mb-8 flex flex-col" style="display: none;">
        
        <div class="px-2 pb-2 mt-4 text-xs font-bold uppercase tracking-wider text-gray-500">
            Account
        </div>
        
        {{-- Profile & Avatar --}}
        <button type="button" @click="setTab('profile')" class="w-full flex items-center gap-4 py-4 px-2 text-sm font-medium text-gray-300 hover:text-white transition-all">
            <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span class="flex-grow text-left">Profile & Avatar</span>
        </button>

        {{-- Security & Password --}}
        <button type="button" @click="setTab('security')" class="w-full flex items-center gap-4 py-4 px-2 text-sm font-medium text-gray-300 hover:text-white transition-all">
            <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <span class="flex-grow text-left">Security & Password</span>
        </button>

        {{-- Linked Accounts --}}
        <button type="button" @click="setTab('accounts')" class="w-full flex items-center gap-4 py-4 px-2 text-sm font-medium text-gray-300 hover:text-white transition-all">
            <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
            </svg>
            <span class="flex-grow text-left">Linked Accounts</span>
        </button>
        
        <div class="px-2 pb-2 mt-6 text-xs font-bold uppercase tracking-wider text-gray-500">
            Preferences
        </div>

        {{-- Playback Settings --}}
        <button type="button" @click="setTab('settings')" class="w-full flex items-center gap-4 py-4 px-2 text-sm font-medium text-gray-300 hover:text-white transition-all">
            <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="flex-grow text-left">Playback Settings</span>
        </button>

        <div class="px-2 pb-2 mt-6 text-xs font-bold uppercase tracking-wider text-gray-500">
            Other
        </div>

        {{-- Danger Zone --}}
        <button type="button" @click="setTab('danger')" class="w-full flex items-center gap-4 py-4 px-2 text-sm font-medium text-rose-400 hover:text-rose-300 transition-all">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <span class="flex-grow text-left">Danger Zone</span>
        </button>
    </div>

    {{-- Back to Menu Button (Mobile Only) --}}
    <div x-show="isMobile && activeTab" class="mb-4" style="display: none;">
        <button type="button" @click="backToMenu()" class="flex items-center gap-2 text-sm font-semibold text-gray-400 hover:text-white transition-colors">
            <div class="w-8 h-8 rounded-xl bg-white/[0.04] border border-white/10 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                </svg>
            </div>
            <span>Back to Settings Menu</span>
        </button>
    </div>

    {{-- Tab 1: Profile Information & Avatar --}}
    <div x-show="activeTab === 'profile' || activeTab === 'avatar'" class="space-y-8" style="display: none;">
        {{-- Avatar Card --}}
        <div class="p-6 sm:p-8 rounded-3xl bg-[#13141f]/80 border border-white/10 backdrop-blur-xl shadow-xl">
            @include('profile.partials.update-avatar-form')
        </div>

        {{-- Personal Info Card --}}
        <div class="p-6 sm:p-8 rounded-3xl bg-[#13141f]/80 border border-white/10 backdrop-blur-xl shadow-xl">
            @include('profile.partials.update-profile-information-form')
        </div>
    </div>

    {{-- Tab: Playback Settings --}}
    <div x-show="activeTab === 'settings'" class="space-y-8" style="display: none;">
        <div class="p-6 sm:p-8 rounded-3xl bg-[#13141f]/80 border border-white/10 backdrop-blur-xl shadow-xl">
            @include('profile.partials.update-settings-form')
        </div>
    </div>

    {{-- Tab 2: Security & Password --}}
    <div x-show="activeTab === 'security'" class="space-y-8" style="display: none;">
        {{-- Password Card --}}
        <div class="p-6 sm:p-8 rounded-3xl bg-[#13141f]/80 border border-white/10 backdrop-blur-xl shadow-xl">
            @include('profile.partials.update-password-form')
        </div>

        {{-- Active Devices & Sessions Card --}}
        <div class="p-6 sm:p-8 rounded-3xl bg-[#13141f]/80 border border-white/10 backdrop-blur-xl shadow-xl">
            <div class="flex items-center justify-between pb-4 mb-6 border-b border-white/5">
                <div>
                    <h3 class="text-lg font-bold text-white">Active Devices & Sessions</h3>
                    <p class="text-xs text-gray-400">Browsers and devices currently logged into your Viewlix account</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                    {{ count($sessions) }} Recorded
                </span>
            </div>

            @if($sessions->isNotEmpty())
                <div class="space-y-3">
                    @foreach($sessions as $session)
                        <div class="p-4 rounded-2xl bg-white/[0.02] border border-white/5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-white/[0.04] text-gray-300 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-white flex items-center gap-2">
                                        <span>{{ $session->os ?? $session->device_type ?? 'Device' }} • {{ $session->browser ?? 'Browser' }}</span>
                                        @if($loop->first)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/20 text-emerald-300">
                                                Active Device
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-[11px] text-gray-400 mt-0.5 flex items-center gap-2 font-mono">
                                        <span>IP: {{ $session->ip_address }}</span>
                                        @if($session->country || $session->city)
                                            <span>•</span>
                                            <span>{{ implode(', ', array_filter([$session->city, $session->country])) }}</span>
                                        @endif
                                        <span>•</span>
                                        <span>{{ $session->last_activity_at ? $session->last_activity_at->diffForHumans() : 'Active' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-gray-500 text-center py-4">Your current active session is secured with HTTPS.</p>
            @endif
        </div>
    </div>

    {{-- Tab 3: Connected Accounts --}}
    <div x-show="activeTab === 'accounts'" class="space-y-8" style="display: none;">
        <div class="p-6 sm:p-8 rounded-3xl bg-[#13141f]/80 border border-white/10 backdrop-blur-xl shadow-xl">
            <header class="pb-4 mb-6 border-b border-white/5">
                <h3 class="text-lg font-bold text-white">Social & Single Sign-On</h3>
                <p class="text-xs text-gray-400">Link external accounts for one-click access across all your devices.</p>
            </header>

            {{-- Google Account Link Item --}}
            <div class="p-5 rounded-2xl bg-white/[0.02] border border-white/5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center shadow-md flex-shrink-0">
                        <svg class="w-7 h-7" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-white">Google Identity</div>
                        <p class="text-xs text-gray-400 mt-0.5">Use Google sign-in to quickly authenticate without passwords</p>
                        @if($user->google_id)
                            <div class="inline-flex items-center gap-1.5 text-xs text-emerald-400 mt-1.5 font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                Connected & Synchronized
                            </div>
                        @else
                            <div class="text-xs text-gray-500 mt-1.5">No Google account linked</div>
                        @endif
                    </div>
                </div>

                @if($user->google_id)
                    <form action="{{ route('auth.google.unlink') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-white/[0.05] hover:bg-rose-500/20 text-gray-300 hover:text-rose-400 border border-white/10 text-xs font-bold transition-all">
                            Unlink Account
                        </button>
                    </form>
                @else
                    <a href="{{ route('auth.google.link') }}" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-viewlix-red to-blue-600 text-white text-xs font-bold shadow-lg shadow-blue-500/20 hover:shadow-blue-500/40 transition-all flex items-center justify-center gap-2">
                        <span>Connect Google</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- Tab 4: Danger Zone --}}
    <div x-show="activeTab === 'danger'" class="space-y-8" style="display: none;">
        <div class="p-6 sm:p-8 rounded-3xl bg-rose-500/[0.03] border border-rose-500/20 backdrop-blur-xl shadow-xl">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Account - Viewlix')

@section('content')
<div class="min-h-[calc(100vh-200px)] flex flex-col items-center justify-center py-8">
    <div class="w-full max-w-2xl flex flex-col items-center">
        <div class="mb-8 w-full text-center">
            <h1 class="text-3xl font-bold text-white mb-2">Account</h1>
            <p class="text-gray-400">Manage your account settings</p>
        </div>
        @if(session('success'))
            <div class="w-full max-w-2xl mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-xl text-green-400 text-center">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="w-full max-w-2xl mb-6 p-4 bg-red-500/10 border border-red-500/20 rounded-xl text-red-400 text-center">
                {{ session('error') }}
            </div>
        @endif
        <div class="w-full max-w-2xl flex flex-col items-center">
            {{-- Profile Section --}}
            <div class="bg-viewlix-gray rounded-2xl border border-viewlix-border p-6 mb-6 w-full max-w-2xl mx-auto flex flex-col items-center">
                <h2 class="text-lg font-semibold text-white mb-4 text-center">Profile</h2>
                <div class="flex flex-col items-center gap-4 mb-6">
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="w-20 h-20 rounded-full object-cover">
                    @else
                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-viewlix-red to-viewlix-red-dark flex items-center justify-center text-white font-bold text-2xl">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-white">{{ Auth::user()->name }}</h3>
                        <p class="text-sm text-gray-400">{{ Auth::user()->email }}</p>
                        @if(Auth::user()->google_id)
                            <span class="inline-flex items-center gap-1 mt-1 text-xs text-green-400">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                Google Connected
                            </span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" class="px-4 py-2 bg-viewlix-gray-light text-white text-sm font-medium rounded-xl hover:bg-viewlix-border transition-colors">
                    Edit Profile
                </a>
            </div>
            {{-- Connected Accounts Section --}}
                <div class="bg-viewlix-gray rounded-2xl border border-viewlix-border p-6 mb-6 w-full max-w-2xl mx-auto flex flex-col items-center">
                <h2 class="text-lg font-semibold text-white mb-4 text-center">Connected Accounts</h2>
                <p class="text-sm text-gray-400 mb-4 text-center">Link your social accounts for easier login and sync across devices.</p>
                <div class="space-y-4 w-full">
                {{-- Google Account --}}
                <div class="flex items-center justify-between p-4 bg-viewlix-dark rounded-xl border border-viewlix-border">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6" viewBox="0 0 24 24">
                                <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-white">Google</h4>
                            @if(Auth::user()->google_id)
                                <p class="text-xs text-green-400">Connected</p>
                            @else
                                <p class="text-xs text-gray-400">Not connected</p>
                            @endif
                        </div>
                    </div>
                    
                    @if(Auth::user()->google_id)
                        <form action="{{ route('auth.google.unlink') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-viewlix-gray-light text-gray-300 text-sm font-medium rounded-lg hover:bg-red-600 hover:text-white transition-colors">
                                Unlink
                            </button>
                        </form>
                    @else
                        <a href="{{ route('auth.google.link') }}" class="px-4 py-2 bg-viewlix-red text-white text-sm font-medium rounded-lg hover:bg-viewlix-red-dark transition-colors">
                            Connect
                        </a>
                    @endif
                </div>
            </div>
        </div>
        
        {{-- Settings Section --}}
            <div class="bg-viewlix-gray rounded-2xl border border-viewlix-border p-6 mb-6 w-full max-w-2xl mx-auto flex flex-col items-center">
            <h2 class="text-lg font-semibold text-white mb-4">Preferences</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between py-3 border-b border-viewlix-border">
                    <div>
                        <h4 class="text-sm font-medium text-white">Notifications</h4>
                        <p class="text-xs text-gray-400">Receive updates about new content</p>
                    </div>
                    <button type="button" class="relative inline-flex h-6 w-11 items-center rounded-full bg-viewlix-red transition-colors" role="switch">
                        <span class="translate-x-6 inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                    </button>
                </div>
                <div class="flex items-center justify-between py-3 border-b border-viewlix-border">
                    <div>
                        <h4 class="text-sm font-medium text-white">Dark Mode</h4>
                        <p class="text-xs text-gray-400">Always use dark theme</p>
                    </div>
                    <button type="button" class="relative inline-flex h-6 w-11 items-center rounded-full bg-viewlix-red transition-colors" role="switch">
                        <span class="translate-x-6 inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                    </button>
                </div>
                <div class="flex items-center justify-between py-3">
                    <div>
                        <h4 class="text-sm font-medium text-white">Auto-play</h4>
                        <p class="text-xs text-gray-400">Automatically play next episode</p>
                    </div>
                    <button type="button" class="relative inline-flex h-6 w-11 items-center rounded-full bg-viewlix-gray-light transition-colors" role="switch">
                        <span class="translate-x-1 inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                    </button>
                </div>
            </div>
        </div>
        
        {{-- Danger Zone --}}
            <div class="bg-viewlix-gray rounded-2xl border border-red-900/50 p-6 w-full max-w-2xl mx-auto flex flex-col items-center">
            <h2 class="text-lg font-semibold text-red-400 mb-4">Danger Zone</h2>
            <p class="text-sm text-gray-400 mb-4">Once you delete your account, there is no going back. Please be certain.</p>
            <a href="{{ route('profile.edit') }}" class="inline-block px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-xl transition-colors">
                Delete Account
            </a>
        </div>
    </div>
</div>
@endsection

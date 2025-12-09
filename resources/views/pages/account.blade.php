@extends('layouts.app')

@section('title', 'Account - Viewlix')

@section('content')
<div class="min-h-[calc(100vh-200px)]">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Account</h1>
        <p class="text-gray-400">Manage your account settings</p>
    </div>
    
    <div class="max-w-2xl">
        {{-- Profile Section --}}
        <div class="bg-viewlix-gray rounded-2xl border border-viewlix-border p-6 mb-6">
            <h2 class="text-lg font-semibold text-white mb-4">Profile</h2>
            <div class="flex items-center gap-4 mb-6">
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-viewlix-red to-viewlix-red-dark flex items-center justify-center text-white font-bold text-2xl">
                    V
                </div>
                <div>
                    <h3 class="text-lg font-medium text-white">Viewlix User</h3>
                    <p class="text-sm text-gray-400">user@viewlix.site</p>
                </div>
            </div>
            <button class="px-4 py-2 bg-viewlix-gray-light text-white text-sm font-medium rounded-xl hover:bg-viewlix-border transition-colors">
                Edit Profile
            </button>
        </div>
        
        {{-- Settings Section --}}
        <div class="bg-viewlix-gray rounded-2xl border border-viewlix-border p-6 mb-6">
            <h2 class="text-lg font-semibold text-white mb-4">Settings</h2>
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
        <div class="bg-viewlix-gray rounded-2xl border border-red-900/50 p-6">
            <h2 class="text-lg font-semibold text-red-400 mb-4">Danger Zone</h2>
            <p class="text-sm text-gray-400 mb-4">Once you delete your account, there is no going back. Please be certain.</p>
            <button class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-xl transition-colors">
                Delete Account
            </button>
        </div>
    </div>
</div>
@endsection

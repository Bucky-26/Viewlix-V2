@extends('layouts.app')

@section('title', 'Profile - Viewlix')

@section('content')
<div class="min-h-[calc(100vh-200px)]">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Profile Settings</h1>
        <p class="text-gray-400">Manage your account settings and preferences</p>
    </div>

    <div class="max-w-2xl space-y-6">
        {{-- Update Profile Information --}}
        <div class="p-6 bg-viewlix-gray rounded-xl border border-viewlix-border">
            @include('profile.partials.update-profile-information-form')
        </div>

        {{-- Update Password --}}
        <div class="p-6 bg-viewlix-gray rounded-xl border border-viewlix-border">
            @include('profile.partials.update-password-form')
        </div>

        {{-- Delete Account --}}
        <div class="p-6 bg-viewlix-gray rounded-xl border border-viewlix-border">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection

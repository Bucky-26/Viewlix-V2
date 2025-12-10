@extends('layouts.app')

@section('title', 'Profile - Viewlix')

@section('content')
<div class="min-h-[calc(100vh-200px)] flex flex-col items-center justify-center py-8">
    <div class="w-full max-w-2xl space-y-6 flex flex-col items-center mx-auto">
        <div class="mb-8 w-full text-center mx-auto">
            <h1 class="text-3xl font-bold text-white mb-2">Profile Settings</h1>
            <p class="text-gray-400">Manage your account settings and preferences</p>
        </div>
        {{-- Update Avatar --}}
        <div class="p-6 bg-viewlix-gray rounded-xl border border-viewlix-border w-full max-w-2xl mx-auto flex flex-col items-center">
            @include('profile.partials.update-avatar-form')
        </div>
        {{-- Update Profile Information --}}
        <div class="p-6 bg-viewlix-gray rounded-xl border border-viewlix-border w-full max-w-2xl mx-auto">
            @include('profile.partials.update-profile-information-form')
        </div>
        {{-- Update Password --}}
        <div class="p-6 bg-viewlix-gray rounded-xl border border-viewlix-border w-full max-w-2xl mx-auto">
            @include('profile.partials.update-password-form')
        </div>
        {{-- Delete Account --}}
        <div class="p-6 bg-viewlix-gray rounded-xl border border-viewlix-border w-full max-w-2xl mx-auto">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
</div>
@endsection

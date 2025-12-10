@extends('layouts.app')

@section('title', 'Terms of Service - Viewlix')

@section('content')
<div class="min-h-[calc(100vh-200px)] flex flex-col items-center justify-center py-8">
    <div class="w-full max-w-2xl bg-viewlix-gray rounded-xl border border-viewlix-border p-8 mx-auto">
        <h1 class="text-3xl font-bold text-white mb-4">Terms of Service</h1>
    <p class="text-gray-400 mb-6">Last updated: December 10, 2025</p>
    <p class="text-gray-400 mb-6">Website: <a href=\"https://viewlix.site\" class=\"text-blue-400 underline\" target=\"_blank\">https://viewlix.site</a></p>
        <h2 class="text-xl font-semibold text-white mb-2">1. Acceptance of Terms</h2>
        <p class="text-gray-300 mb-4">By accessing or using Viewlix, you agree to be bound by these Terms of Service. If you do not agree, please do not use our service.</p>
        <h2 class="text-xl font-semibold text-white mb-2">2. User Responsibilities</h2>
        <p class="text-gray-300 mb-4">You are responsible for your account and any activity that occurs under it. Please use Viewlix in compliance with all applicable laws and regulations.</p>
        <h2 class="text-xl font-semibold text-white mb-2">3. Changes to Terms</h2>
        <p class="text-gray-300 mb-4">We may update these Terms from time to time. Continued use of the service after changes constitutes acceptance of the new terms.</p>
        <h2 class="text-xl font-semibold text-white mb-2">4. Contact</h2>
    <p class="text-gray-300">If you have any questions about these Terms, please contact us at <a href=\"mailto:contact@viewlix.site\" class=\"text-blue-400 underline\">contact@viewlix.site</a>.</p>
    </div>
</div>
@endsection

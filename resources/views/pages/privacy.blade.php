@extends('layouts.app')

@section('title', 'Privacy Policy - Viewlix')

@section('content')
<div class="min-h-[calc(100vh-200px)] flex flex-col items-center justify-center py-8">
    <div class="w-full max-w-2xl bg-viewlix-gray rounded-xl border border-viewlix-border p-8 mx-auto">
        <h1 class="text-3xl font-bold text-white mb-4">Privacy Policy</h1>
    <p class="text-gray-400 mb-6">Last updated: December 10, 2025</p>
    <p class="text-gray-400 mb-6">Website: <a href=\"https://viewlix.site\" class=\"text-blue-400 underline\" target=\"_blank\">https://viewlix.site</a></p>
        <h2 class="text-xl font-semibold text-white mb-2">1. Information We Collect</h2>
        <p class="text-gray-300 mb-4">We collect information you provide when you register, use our services, or contact us. This may include your name, email, and usage data.</p>
        <h2 class="text-xl font-semibold text-white mb-2">2. How We Use Information</h2>
        <p class="text-gray-300 mb-4">We use your information to provide, maintain, and improve our services, and to communicate with you about updates or offers.</p>
        <h2 class="text-xl font-semibold text-white mb-2">3. Data Security</h2>
        <p class="text-gray-300 mb-4">We implement reasonable security measures to protect your data, but cannot guarantee absolute security.</p>
        <h2 class="text-xl font-semibold text-white mb-2">4. Contact</h2>
    <p class="text-gray-300">If you have any questions about this Privacy Policy, please contact us at <a href=\"mailto:contact@viewlix.site\" class=\"text-blue-400 underline\">contact@viewlix.site</a>.</p>
    </div>
</div>
@endsection

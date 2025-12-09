<x-guest-layout>
    <div class="text-center mb-6">
        <div class="w-16 h-16 bg-viewlix-red/10 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-viewlix-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-white">Forgot Password?</h2>
    </div>

    <div class="mb-4 text-sm text-gray-400 text-center">
        {{ __('No problem! Just enter your email address and we\'ll send you a password reset link.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="your@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-primary-button class="w-full">
                {{ __('Send Reset Link') }}
            </x-primary-button>
        </div>

        <div class="text-center mt-4">
            <a class="text-sm text-gray-400 hover:text-viewlix-red transition-colors" href="{{ route('login') }}">
                {{ __('Back to sign in') }}
            </a>
        </div>
    </form>
</x-guest-layout>

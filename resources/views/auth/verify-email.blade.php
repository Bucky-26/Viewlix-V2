<x-guest-layout>
    <div class="text-center mb-6">
        <div class="w-16 h-16 bg-viewlix-red/10 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-viewlix-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-white">Verify Your Email</h2>
    </div>

    <div class="mb-4 text-sm text-gray-400 text-center">
        {{ __('Thanks for signing up! Before getting started, please verify your email address by clicking on the link we just sent to you. If you didn\'t receive the email, we\'ll gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-400 bg-green-400/10 border border-green-400/20 rounded-xl px-4 py-3 text-center">
            {{ __('A new verification link has been sent to your email address.') }}
        </div>
    @endif

    <div class="mt-6 flex flex-col gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button class="w-full">
                {{ __('Resend Verification Email') }}
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full px-6 py-3 bg-viewlix-gray hover:bg-viewlix-gray-light text-white font-semibold text-sm rounded-xl border border-viewlix-border transition-colors">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>

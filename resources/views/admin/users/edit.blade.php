@extends('layouts.admin')

@section('title', 'Edit User')
@section('subtitle', 'Update user information and permissions.')

@section('header-actions')
<a href="{{ route('admin.users') }}" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-viewlix-border bg-viewlix-gray text-gray-300 shadow-sm hover:bg-viewlix-border hover:text-white focus:outline-none focus:ring-2 focus:ring-viewlix-red transition-colors">
    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m15 18-6-6 6-6"/>
    </svg>
    Back to Users
</a>
@endsection

@section('content')
<div class="max-w-2xl">
    {{-- User Card (Preline-style) --}}
    <div class="rounded-xl border border-viewlix-border bg-viewlix-dark shadow-sm overflow-hidden">
        <div class="p-6 sm:p-8">
            <div class="flex items-center gap-6">
                <div class="flex-shrink-0">
                    @if($user->avatar)
                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-full object-cover ring-2 ring-viewlix-border">
                    @else
                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-viewlix-red to-viewlix-red-dark flex items-center justify-center ring-2 ring-viewlix-border">
                            <span class="text-2xl font-medium text-white leading-none uppercase">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <h2 class="text-xl lg:text-2xl font-semibold text-white truncate">{{ $user->name }}</h2>
                    <p class="mt-1 text-sm text-gray-400 truncate">Member since {{ $user->created_at->format('F j, Y') }}</p>

                    <div class="mt-3 flex flex-wrap items-center gap-2">
                        @if($user->is_admin)
                            <span class="inline-flex items-center gap-x-2 rounded-full px-2 py-1 text-xs font-medium bg-viewlix-red/10 text-viewlix-red">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/></svg>
                                Admin
                            </span>
                        @endif

                        @if($user->google_id)
                            <span class="inline-flex items-center gap-x-2 rounded-full px-2 py-1 text-xs font-medium bg-blue-500/10 text-blue-400">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/></svg>
                                Google
                            </span>
                        @endif

                        @if($user->email_verified_at)
                            <span class="inline-flex items-center gap-x-2 rounded-full px-2 py-1 text-xs font-medium bg-teal-500/10 text-teal-400">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/></svg>
                                Verified
                            </span>
                        @else
                            <span class="inline-flex items-center gap-x-2 rounded-full px-2 py-1 text-xs font-medium bg-yellow-500/10 text-yellow-400">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/></svg>
                                Unverified
                            </span>
                        @endif
                    </div>
                </div>

                <div class="ml-auto flex-shrink-0">
                    <div class="flex items-center gap-2">
                        @if(!$user->email_verified_at)
                            <form action="{{ route('admin.users.verify', $user) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-x-2 rounded-md bg-teal-500/10 text-teal-400 px-3 py-2 text-sm font-medium hover:bg-teal-500/20 focus:outline-none focus:ring-2 focus:ring-teal-500">Verify</button>
                            </form>

                            <form action="{{ route('admin.users.resend-verification', $user) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-x-2 rounded-md border border-viewlix-border bg-viewlix-gray text-gray-300 px-3 py-2 text-sm font-medium hover:bg-viewlix-border">Resend</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        {{-- End User Card --}}

        {{-- Edit Form --}}
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="p-4 sm:p-6 space-y-5">
                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-white mb-2">Name</label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name', $user->name) }}"
                           class="py-3 px-4 block w-full bg-viewlix-gray border-viewlix-border rounded-lg text-sm text-white placeholder-gray-400 focus:border-viewlix-red focus:ring-viewlix-red"
                           required>
                    @error('name')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-white mb-2">Email</label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           value="{{ old('email', $user->email) }}"
                           class="py-3 px-4 block w-full bg-viewlix-gray border-viewlix-border rounded-lg text-sm text-white placeholder-gray-400 focus:border-viewlix-red focus:ring-viewlix-red"
                           required>
                    @error('email')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Admin Toggle --}}
                @if($user->id !== auth()->id())
                <div class="flex items-center justify-between p-4 bg-viewlix-gray/50 rounded-xl">
                    <div>
                        <label for="is_admin" class="text-sm font-medium text-white">Administrator</label>
                        <p class="text-xs text-gray-400">Grant admin privileges to this user</p>
                    </div>
                    <div class="relative inline-block">
                        <input type="checkbox" 
                               name="is_admin" 
                               id="is_admin"
                               value="1"
                               {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}
                               class="relative w-11 h-6 p-px bg-viewlix-border border-transparent text-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:ring-viewlix-red disabled:opacity-50 disabled:pointer-events-none checked:bg-none checked:text-viewlix-red checked:border-viewlix-red focus:checked:border-viewlix-red before:inline-block before:size-5 before:bg-white checked:before:bg-white before:translate-x-0 checked:before:translate-x-full before:rounded-full before:shadow before:transform before:ring-0 before:transition before:ease-in-out before:duration-200">
                    </div>
                </div>
                @else
                <div class="bg-yellow-500/10 border border-yellow-500/30 text-sm text-yellow-400 rounded-xl p-4 flex items-center gap-3">
                    <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/>
                        <path d="M12 9v4"/>
                        <path d="M12 17h.01"/>
                    </svg>
                    You cannot change your own admin status.
                </div>
                @endif
            </div>

            {{-- Form Footer --}}
            <div class="p-4 sm:p-6 border-t border-viewlix-border bg-viewlix-gray/30">
                <div class="flex flex-wrap items-center justify-end gap-2">
                    <a href="{{ route('admin.users') }}" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-viewlix-border bg-viewlix-gray text-gray-300 shadow-sm hover:bg-viewlix-border hover:text-white focus:outline-none focus:ring-2 focus:ring-viewlix-red transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-viewlix-red text-white hover:bg-viewlix-red-dark focus:outline-none focus:ring-2 focus:ring-viewlix-red focus:ring-offset-2 focus:ring-offset-viewlix-dark transition-colors">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                            <polyline points="17 21 17 13 7 13 7 21"/>
                            <polyline points="7 3 7 8 15 8"/>
                        </svg>
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Danger Zone --}}
    @if($user->id !== auth()->id())
    <div class="mt-6 bg-viewlix-dark border border-red-500/30 rounded-xl shadow-sm overflow-hidden">
        <div class="p-4 sm:p-6">
            <div class="flex items-start gap-4">
                <div class="shrink-0 size-10 inline-flex justify-center items-center rounded-lg bg-red-500/10">
                    <svg class="shrink-0 size-5 text-red-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18"/>
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                        <line x1="10" x2="10" y1="11" y2="17"/>
                        <line x1="14" x2="14" y1="11" y2="17"/>
                    </svg>
                </div>
                <div class="grow">
                    <h3 class="text-lg font-semibold text-red-400">Danger Zone</h3>
                    <p class="mt-1 text-sm text-gray-400">Once you delete a user, there is no going back. Please be certain.</p>
                    <div class="mt-4">
                        <form action="{{ route('admin.users.delete', $user) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete {{ $user->name }}? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-red-500/30 bg-red-500/10 text-red-400 hover:bg-red-500 hover:text-white hover:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 6h18"/>
                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                </svg>
                                Delete this user
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@if(session('success') || session('error') || session('status'))
    <div id="flash-toast" class="fixed bottom-6 right-6 z-50">
        <div id="flash-toast-inner" class="max-w-sm w-full px-4 py-3 rounded-lg shadow-lg text-white {{ session('error') ? 'bg-red-600' : 'bg-teal-600' }}">
            <div class="flex items-start gap-3">
                <div class="flex-1 text-sm">
                    @if(session('success'))
                        {{ session('success') }}
                    @elseif(session('status'))
                        {{ session('status') }}
                    @else
                        {{ session('error') }}
                    @endif
                </div>
                <button id="flash-toast-close" class="ml-2 text-white opacity-80 hover:opacity-100">&times;</button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        (function(){
            const toast = document.getElementById('flash-toast');
            const close = document.getElementById('flash-toast-close');
            if(!toast) return;
            // Auto-hide after 5s
            const timeout = setTimeout(() => { toast.style.display = 'none'; }, 5000);
            close.addEventListener('click', function(){ clearTimeout(timeout); toast.style.display = 'none'; });
        })();
    </script>
    @endpush
@endif

@endsection

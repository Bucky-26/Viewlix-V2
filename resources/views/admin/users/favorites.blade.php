@extends('layouts.admin')

@section('title', 'User Favorites')

@section('content')
<div class="max-w-6xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-white">Favorites for {{ $user->name }}</h1>
            <p class="text-sm text-gray-400">{{ $user->email }}</p>
        </div>
        <a href="{{ route('admin.users.edit', $user) }}" class="py-2 px-3 rounded-lg bg-viewlix-gray text-gray-300 border border-viewlix-border">Back to user</a>
    </div>

    @if($favorites->count())
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($favorites as $favorite)
                <div class="rounded-xl overflow-hidden bg-viewlix-dark border border-viewlix-border">
                    <div class="p-3">
                        <img src="{{ $favorite->poster_url }}" alt="{{ $favorite->title }}" class="w-full h-44 object-cover rounded">
                    </div>
                    <div class="p-3 border-t border-viewlix-border">
                        <h3 class="text-sm text-white font-medium truncate">{{ $favorite->title }}</h3>
                        <p class="text-xs text-gray-400 mt-1">{{ strtoupper($favorite->media_type) }} • {{ $favorite->release_date ? \Carbon\Carbon::parse($favorite->release_date)->format('Y') : '' }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $favorites->links() }}
        </div>
    @else
        <div class="rounded-xl p-6 bg-viewlix-dark border border-viewlix-border">
            <h3 class="text-lg text-white">No favorites found</h3>
            <p class="text-gray-400">This user has not added any favorites.</p>
        </div>
    @endif
</div>
@endsection

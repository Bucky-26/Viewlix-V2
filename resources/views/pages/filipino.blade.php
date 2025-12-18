@extends('layouts.app')
@section('title', 'Filipino Movies & Series')
@section('content')

<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8 text-white">Filipino Movies & Series</h1>

    {{-- Filipino Movies --}}
    <section class="mb-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-white">Tagalog Movies</h2>
            <a href="#" class="text-blue-600 hover:text-blue-800">See all →</a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @forelse($movies as $movie)
                @php
                    $title = $movie['title'] ?? $movie['name'] ?? '';
                    $release = $movie['release_date'] ?? '';
                    $year = $release ? substr($release, 0, 4) : '';
                    $poster = $movie['poster_path'] ?? null;
                @endphp

                <a href="{{ route('movie.show', ['id' => $movie['id']]) }}" class="group">
                    @if($poster && isset($tmdb))
                        <img src="{{ $tmdb->getImageUrl($poster, 'w500') }}"
                             alt="{{ $title }}"
                             loading="lazy"
                             class="w-full rounded-lg shadow-lg object-cover">
                    @else
                        <div class="w-full rounded-lg shadow-lg bg-viewlix-darker aspect-[2/3] flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif

                    <h3 class="mt-2 font-semibold text-sm text-white truncate">{{ $title }}</h3>
                    <p class="text-gray-400 text-xs">{{ $year }}</p>
                </a>

            @empty
                <p class="col-span-full text-gray-500">No Filipino movies found.</p>
            @endforelse
        </div>
    </section>

    {{-- Filipino TV Shows --}}
    <section>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold text-white">Tagalog TV Series</h2>
            <a href="#" class="text-blue-600 hover:text-blue-800">See all →</a>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @forelse($tvShows as $tv)
                @php
                    $title = $tv['name'] ?? $tv['title'] ?? '';
                    $firstAir = $tv['first_air_date'] ?? '';
                    $year = $firstAir ? substr($firstAir, 0, 4) : '';
                    $poster = $tv['poster_path'] ?? null;
                @endphp

                <a href="{{ route('tv-show.show', ['id' => $tv['id']]) }}" class="group">
                    @if($poster && isset($tmdb))
                        <img src="{{ $tmdb->getImageUrl($poster, 'w500') }}"
                             alt="{{ $title }}"
                             loading="lazy"
                             class="w-full rounded-lg shadow-lg object-cover">
                    @else
                        <div class="w-full rounded-lg shadow-lg bg-viewlix-darker aspect-[2/3] flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif

                    <h3 class="mt-2 font-semibold text-sm text-white truncate">{{ $title }}</h3>
                    <p class="text-gray-400 text-xs">{{ $year }}</p>
                </a>

            @empty
                <p class="col-span-full text-gray-500">No Filipino TV shows found.</p>
            @endforelse
        </div>
    </section>
</div>

@endsection
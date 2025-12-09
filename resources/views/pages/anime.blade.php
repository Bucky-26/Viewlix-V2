@extends('layouts.app')

@section('title', 'Anime - Viewlix')

@section('content')
<div class="min-h-[calc(100vh-200px)]">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Anime</h1>
        <p class="text-gray-400">Japanese animation series</p>
    </div>
    
    {{-- Filters --}}
    <div class="flex flex-wrap items-center gap-3 mb-8">
        <a href="{{ route('anime', ['filter' => 'popular']) }}" 
           class="px-4 py-2 text-sm font-medium rounded-xl transition-colors {{ $filter === 'popular' ? 'bg-viewlix-red text-white' : 'bg-viewlix-gray text-gray-300 hover:bg-viewlix-gray-light' }}">
            Popular
        </a>
        <a href="{{ route('anime', ['filter' => 'top_rated']) }}" 
           class="px-4 py-2 text-sm font-medium rounded-xl transition-colors {{ $filter === 'top_rated' ? 'bg-viewlix-red text-white' : 'bg-viewlix-gray text-gray-300 hover:bg-viewlix-gray-light' }}">
            Top Rated
        </a>
        <a href="{{ route('anime', ['filter' => 'airing']) }}" 
           class="px-4 py-2 text-sm font-medium rounded-xl transition-colors {{ $filter === 'airing' ? 'bg-viewlix-red text-white' : 'bg-viewlix-gray text-gray-300 hover:bg-viewlix-gray-light' }}">
            Currently Airing
        </a>
    </div>
    
    {{-- Anime Grid --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
        @forelse($animeList as $anime)
            @include('partials.media-card', ['item' => $anime, 'type' => 'anime'])
        @empty
            <div class="col-span-full text-center py-12">
                <p class="text-gray-400">No anime found.</p>
            </div>
        @endforelse
    </div>
    
    {{-- Pagination --}}
    @if($totalPages > 1)
    <div class="flex items-center justify-center gap-2 mt-8">
        @if($currentPage > 1)
        <a href="{{ route('anime', ['filter' => $filter, 'page' => $currentPage - 1]) }}" 
           class="px-4 py-2 bg-viewlix-gray text-white rounded-xl hover:bg-viewlix-gray-light transition-colors">
            Previous
        </a>
        @endif
        
        <span class="px-4 py-2 text-gray-400">
            Page {{ $currentPage }} of {{ min($totalPages, 500) }}
        </span>
        
        @if($currentPage < $totalPages && $currentPage < 500)
        <a href="{{ route('anime', ['filter' => $filter, 'page' => $currentPage + 1]) }}" 
           class="px-4 py-2 bg-viewlix-gray text-white rounded-xl hover:bg-viewlix-gray-light transition-colors">
            Next
        </a>
        @endif
    </div>
    @endif
</div>
@endsection

@extends('layouts.app')

@section('title', 'Search: ' . $query . ' - Viewlix')

@section('content')
<div class="min-h-[calc(100vh-200px)]">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Search Results</h1>
        <p class="text-gray-400">Showing results for "<span class="text-white">{{ $query }}</span>"</p>
    </div>
    
    {{-- Search Form --}}
    <form action="{{ route('search') }}" method="GET" class="mb-8">
        <div class="flex gap-3">
            <input type="text" name="q" value="{{ $query }}" 
                   class="flex-1 px-4 py-3 bg-viewlix-gray border border-viewlix-border rounded-xl text-white placeholder-gray-500 focus:outline-none focus:border-viewlix-red"
                   placeholder="Search movies, TV shows, anime...">
            <button type="submit" class="px-6 py-3 bg-viewlix-red hover:bg-viewlix-red-dark text-white font-semibold rounded-xl transition-colors">
                Search
            </button>
        </div>
    </form>
    
    {{-- Filter Tabs --}}
    <div class="flex flex-wrap items-center gap-3 mb-8">
        <a href="{{ route('search', ['q' => $query]) }}" 
           class="px-4 py-2 text-sm font-medium rounded-xl transition-colors {{ !$filter ? 'bg-viewlix-red text-white' : 'bg-viewlix-gray text-gray-300 hover:bg-viewlix-gray-light' }}">
            All
        </a>
        <a href="{{ route('search', ['q' => $query, 'filter' => 'movie']) }}" 
           class="px-4 py-2 text-sm font-medium rounded-xl transition-colors {{ $filter === 'movie' ? 'bg-viewlix-red text-white' : 'bg-viewlix-gray text-gray-300 hover:bg-viewlix-gray-light' }}">
            Movies
        </a>
        <a href="{{ route('search', ['q' => $query, 'filter' => 'tv']) }}" 
           class="px-4 py-2 text-sm font-medium rounded-xl transition-colors {{ $filter === 'tv' ? 'bg-viewlix-red text-white' : 'bg-viewlix-gray text-gray-300 hover:bg-viewlix-gray-light' }}">
            TV Shows
        </a>
    </div>
    
    {{-- Results Grid --}}
    @if(!empty($results))
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
        @foreach($results as $item)
            @php
                $mediaType = $item['media_type'] ?? ($filter ?: 'movie');
                $isAnime = $mediaType === 'tv' && !empty($item['origin_country']) && in_array('JP', $item['origin_country']);
            @endphp
            @include('partials.media-card', ['item' => $item, 'type' => $isAnime ? 'anime' : $mediaType])
        @endforeach
    </div>
    
    {{-- Pagination --}}
    @if($totalPages > 1)
    <div class="flex items-center justify-center gap-2 mt-8">
        @if($currentPage > 1)
        <a href="{{ route('search', ['q' => $query, 'filter' => $filter, 'page' => $currentPage - 1]) }}" 
           class="px-4 py-2 bg-viewlix-gray text-white rounded-xl hover:bg-viewlix-gray-light transition-colors">
            Previous
        </a>
        @endif
        
        <span class="px-4 py-2 text-gray-400">
            Page {{ $currentPage }} of {{ min($totalPages, 500) }}
        </span>
        
        @if($currentPage < $totalPages && $currentPage < 500)
        <a href="{{ route('search', ['q' => $query, 'filter' => $filter, 'page' => $currentPage + 1]) }}" 
           class="px-4 py-2 bg-viewlix-gray text-white rounded-xl hover:bg-viewlix-gray-light transition-colors">
            Next
        </a>
        @endif
    </div>
    @endif
    
    @else
    <div class="text-center py-16">
        <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <p class="text-gray-400 text-lg">No results found for "{{ $query }}"</p>
        <p class="text-gray-500 mt-2">Try different keywords or check your spelling</p>
    </div>
    @endif
</div>
@endsection

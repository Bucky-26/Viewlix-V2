@extends('layouts.app')

@section('title', 'About Viewlix')
@section('description', 'Learn about Viewlix — watch movies and TV shows for free without ads or subscription.')

@section('content')
<div class="max-w-3xl mx-auto">
    <x-card>
    <header class="mb-6">
    <h1 class="text-4xl font-extrabold leading-tight">About ViewLix</h1>
    <p class="mt-2 text-lg text-gray-200">Watch your favorite movies and TV shows for free — no ads or subscription</p>
    </header>

    <section class="space-y-4">
        <details class="group bg-gray-800/40 rounded-lg p-4" open>
            <summary class="flex items-center justify-between cursor-pointer list-none">
                <h2 class="text-2xl font-semibold">What is ViewLix?</h2>
                <svg class="w-5 h-5 text-gray-200 transform group-open:rotate-180 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </summary>
            <div class="mt-3 text-gray-100">
                <p>
                    ViewLix is a community-focused streaming portal that helps you discover and watch movies, TV shows, and anime.
                    We provide a fast, minimal, and modern viewing experience without requiring paid subscriptions.
                </p>
            </div>
        </details>

        <details class="group bg-gray-800/40 rounded-lg p-4">
            <summary class="flex items-center justify-between cursor-pointer list-none">
                <h2 class="text-2xl font-semibold">Why Choose ViewLix?</h2>
                <svg class="w-5 h-5 text-gray-200 transform group-open:rotate-180 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </summary>
            <div class="mt-3 text-gray-100">
                <ul class="list-disc list-inside space-y-1">
                    <li>Free to use — no subscription required.</li>
                    <li>No ads in the viewing experience.</li>
                    <li>Simple, fast discovery and search.</li>
                    <li>Works across devices including mobile and progressive web apps.</li>
                </ul>
            </div>
        </details>

        <details class="group bg-gray-800/40 rounded-lg p-4">
            <summary class="flex items-center justify-between cursor-pointer list-none">
                <h2 class="text-2xl font-semibold">Content Library</h2>
                <svg class="w-5 h-5 text-gray-200 transform group-open:rotate-180 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </summary>
            <div class="mt-3 text-gray-100">
                <p>
                    Our library contains movies, TV shows, and anime across genres. We keep the catalog updated and surface details like synopsis, cast,
                    and recommendations to help you find your next watch.
                </p>
            </div>
        </details>
    </section>
    </x-card>
</div>

@endsection

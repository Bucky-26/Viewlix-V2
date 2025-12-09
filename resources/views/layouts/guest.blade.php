<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Viewlix') }}</title>

        <!-- PWA -->
        @PwaHead
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-viewlix-dark">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <div>
                <a href="/" class="flex items-center gap-3">
                    <svg class="h-12 w-12 text-viewlix-red" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M4 4h4l4 12 4-12h4L12 22 4 4z"/>
                    </svg>
                    <span class="text-3xl font-bold text-white">View<span class="text-viewlix-red">lix</span></span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-6 bg-viewlix-gray/80 backdrop-blur-sm border border-viewlix-border shadow-xl overflow-hidden sm:rounded-xl">
                {{ $slot }}
            </div>
            
            <p class="mt-6 text-sm text-gray-500">
                &copy; {{ date('Y') }} Viewlix. All rights reserved.
            </p>
        </div>
    </body>
</html>

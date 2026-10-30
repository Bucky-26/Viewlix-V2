<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - Admin | {{ config('app.name', 'Viewlix') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Firebase Initialization -->
    <script>
        window.firebaseConfig = {
            apiKey: "{{ config('services.firebase.api_key') }}",
            authDomain: "{{ config('services.firebase.auth_domain') }}",
            databaseURL: "{{ config('services.firebase.database_url') }}",
            projectId: "{{ config('services.firebase.project_id') }}",
            storageBucket: "{{ config('services.firebase.storage_bucket') }}",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
            appId: "{{ config('services.firebase.app_id') }}",
            measurementId: "{{ config('services.firebase.measurement_id') }}"
        };
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.initFirebase === 'function' && window.firebaseConfig.apiKey) {
                window.initFirebase(window.firebaseConfig);
            }
        });
    </script>

    <style>
        [x-cloak] { display: none !important; }
        /* Mini sidebar transitions */
        .sidebar-mini {
            width: 5rem;
            transition: width 0.2s ease-in-out;
        }
        .sidebar-mini:hover {
            width: 16rem;
        }
        .sidebar-mini .nav-text,
        .sidebar-mini .logo-text,
        .sidebar-mini .user-info,
        .sidebar-mini .section-title {
            opacity: 0;
            white-space: nowrap;
            transition: opacity 0.15s ease-in-out;
        }
        .sidebar-mini:hover .nav-text,
        .sidebar-mini:hover .logo-text,
        .sidebar-mini:hover .user-info,
        .sidebar-mini:hover .section-title {
            opacity: 1;
        }
        .sidebar-mini .nav-icon {
            min-width: 1.25rem;
        }
        /* Content area transition */
        .main-content {
            margin-left: 5rem;
            transition: margin-left 0.2s ease-in-out;
        }
        @media (max-width: 1023px) {
            .main-content {
                margin-left: 0;
            }
        }
        /* Hide scrollbars completely on navigation */
        .no-scrollbar::-webkit-scrollbar,
        .sidebar-mini::-webkit-scrollbar,
        .sidebar-mini *::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }
        .no-scrollbar,
        .sidebar-mini,
        .sidebar-mini * {
            -ms-overflow-style: none !important; /* IE and Edge */
            scrollbar-width: none !important;    /* Firefox */
        }
    </style>
</head>
<body class="bg-viewlix-darker text-gray-200">
    {{-- Mobile Header --}}
    <div x-data="{ mobileMenuOpen: false }" class="lg:hidden">
        <div class="sticky top-0 inset-x-0 z-20 bg-viewlix-dark border-b border-viewlix-border px-4 sm:px-6">
            <div class="flex items-center justify-between py-3">
                {{-- Logo --}}
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-viewlix-red rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z"/>
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-white">Viewlix</span>
                    <span class="px-2 py-0.5 bg-viewlix-red/20 text-viewlix-red text-xs font-medium rounded">Admin</span>
                </a>

                {{-- Mobile Menu Toggle --}}
                <button type="button" 
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="size-9 flex justify-center items-center border border-viewlix-border text-gray-400 hover:text-white hover:bg-viewlix-gray rounded-lg transition-colors"
                        aria-label="Toggle navigation">
                    <svg x-show="!mobileMenuOpen" class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" x2="21" y1="6" y2="6"/>
                        <line x1="3" x2="21" y1="12" y2="12"/>
                        <line x1="3" x2="21" y1="18" y2="18"/>
                    </svg>
                    <svg x-show="mobileMenuOpen" class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18"/>
                        <path d="m6 6 12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Mobile Menu --}}
            <div x-show="mobileMenuOpen" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="pb-4 border-t border-viewlix-border pt-4">
                <nav class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-viewlix-red text-white' : 'text-gray-300 hover:bg-viewlix-gray hover:text-white' }}">
                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.analytics') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.analytics') ? 'bg-viewlix-red text-white' : 'text-gray-300 hover:bg-viewlix-gray hover:text-white' }}">
                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 3v18h18"/>
                            <path d="m19 9-5 5-4-4-3 3"/>
                        </svg>
                        Analytics
                    </a>
                    <a href="{{ route('admin.users') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.users*') ? 'bg-viewlix-red text-white' : 'text-gray-300 hover:bg-viewlix-gray hover:text-white' }}">
                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                        Users
                    </a>
                    <a href="{{ route('admin.notifications') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.notifications*') ? 'bg-viewlix-red text-white' : 'text-gray-300 hover:bg-viewlix-gray hover:text-white' }}">
                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
                            <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                        </svg>
                        Notifications
                    </a>
                    <a href="{{ route('admin.banners.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.banners*') ? 'bg-viewlix-red text-white' : 'text-gray-300 hover:bg-viewlix-gray hover:text-white' }}">
                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14M5 12a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2M5 12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2m-7-5h.01M12 8v8"/>
                        </svg>
                        Banners
                    </a>
                    <a href="{{ route('admin.video-sources.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.video-sources*') ? 'bg-viewlix-red text-white' : 'text-gray-300 hover:bg-viewlix-gray hover:text-white' }}">
                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="5 3 19 12 5 21 5 3"/>
                        </svg>
                        Video Sources
                    </a>
                    <a href="{{ route('admin.support.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.support*') ? 'bg-viewlix-red text-white' : 'text-gray-300 hover:bg-viewlix-gray hover:text-white' }}">
                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        Live Support
                    </a>
                    <a href="{{ route('admin.mobile-app.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm {{ request()->routeIs('admin.mobile-app*') ? 'bg-viewlix-red text-white' : 'text-gray-300 hover:bg-viewlix-gray hover:text-white' }}">
                        <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="14" height="20" x="5" y="2" rx="2" ry="2"/>
                            <path d="M12 18h.01"/>
                        </svg>
                        Mobile App
                    </a>
                    <div class="pt-3 mt-3 border-t border-viewlix-border">
                        <a href="{{ route('home') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:bg-viewlix-gray hover:text-white">
                            <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 3h6v6"/>
                                <path d="M10 14 21 3"/>
                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                            </svg>
                            Back to Site
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="mt-1">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-red-400 hover:bg-red-500/10">
                                <svg class="size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                    <polyline points="16 17 21 12 16 7"/>
                                    <line x1="21" x2="9" y1="12" y2="12"/>
                                </svg>
                                Sign out
                            </button>
                        </form>
                    </div>
                </nav>
            </div>
        </div>
    </div>

    {{-- Desktop Mini Sidebar --}}
    <aside class="hidden lg:block fixed top-0 start-0 bottom-0 z-50 sidebar-mini bg-viewlix-dark border-e border-viewlix-border overflow-hidden">
        <div class="flex flex-col h-full">
            {{-- Logo --}}
            <div class="px-4 py-4 border-b border-viewlix-border flex items-center gap-3">
                <div class="shrink-0 w-8 h-8 bg-viewlix-red rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z"/>
                    </svg>
                </div>
                <div class="logo-text flex items-center gap-2 overflow-hidden">
                    <span class="text-xl font-bold text-white">Viewlix</span>
                    <span class="px-2 py-0.5 bg-viewlix-red/20 text-viewlix-red text-xs font-medium rounded">Admin</span>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="flex-1 overflow-y-auto overflow-x-hidden p-3 no-scrollbar">
                <nav class="space-y-1 no-scrollbar">
                    {{-- Dashboard --}}
                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-viewlix-red text-white' : 'text-gray-300 hover:bg-viewlix-gray hover:text-white' }}"
                       title="Dashboard">
                        <svg class="nav-icon shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                        <span class="nav-text">Dashboard</span>
                    </a>

                    {{-- Analytics --}}
                    <a href="{{ route('admin.analytics') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.analytics') ? 'bg-viewlix-red text-white' : 'text-gray-300 hover:bg-viewlix-gray hover:text-white' }}"
                       title="Analytics">
                        <svg class="nav-icon shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 3v18h18"/>
                            <path d="m19 9-5 5-4-4-3 3"/>
                        </svg>
                        <span class="nav-text">Analytics</span>
                    </a>

                    {{-- Users Section --}}
                    <div class="pt-4">
                        <p class="section-title px-3 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Management</p>
                        
                        <a href="{{ route('admin.users') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.users*') && !request('role') ? 'bg-viewlix-red text-white' : 'text-gray-300 hover:bg-viewlix-gray hover:text-white' }}"
                           title="All Users">
                            <svg class="nav-icon shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <span class="nav-text">All Users</span>
                        </a>

                        <a href="{{ route('admin.users', ['role' => 'admin']) }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request('role') === 'admin' ? 'bg-viewlix-red text-white' : 'text-gray-300 hover:bg-viewlix-gray hover:text-white' }}"
                           title="Administrators">
                            <svg class="nav-icon shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
                            </svg>
                            <span class="nav-text">Administrators</span>
                        </a>

                        <a href="{{ route('admin.notifications') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.notifications*') ? 'bg-viewlix-red text-white' : 'text-gray-300 hover:bg-viewlix-gray hover:text-white' }}"
                           title="Notifications">
                            <svg class="nav-icon shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
                                <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                            </svg>
                            <span class="nav-text">Notifications</span>
                        </a>

                        <a href="{{ route('admin.banners.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.banners*') ? 'bg-viewlix-red text-white' : 'text-gray-300 hover:bg-viewlix-gray hover:text-white' }}"
                           title="Banners">
                            <svg class="nav-icon shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12h14M5 12a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2M5 12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2m-7-5h.01M12 8v8"/>
                            </svg>
                            <span class="nav-text">Banners</span>
                        </a>

                        <a href="{{ route('admin.video-sources.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.video-sources*') ? 'bg-viewlix-red text-white' : 'text-gray-300 hover:bg-viewlix-gray hover:text-white' }}"
                           title="Video Sources">
                            <svg class="nav-icon shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="5 3 19 12 5 21 5 3"/>
                            </svg>
                            <span class="nav-text">Video Sources</span>
                        </a>

                        <a href="{{ route('admin.support.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.support*') ? 'bg-viewlix-red text-white' : 'text-gray-300 hover:bg-viewlix-gray hover:text-white' }}"
                           title="Live Support & Tickets">
                            <svg class="nav-icon shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                            <span class="nav-text">Live Support</span>
                        </a>

                        <a href="{{ route('admin.mobile-app.index') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors {{ request()->routeIs('admin.mobile-app*') ? 'bg-viewlix-red text-white' : 'text-gray-300 hover:bg-viewlix-gray hover:text-white' }}"
                           title="Mobile App Management">
                            <svg class="nav-icon shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="14" height="20" x="5" y="2" rx="2" ry="2"/>
                                <path d="M12 18h.01"/>
                            </svg>
                            <span class="nav-text">Mobile App</span>
                        </a>
                    </div>

                    {{-- Quick Links --}}
                    <div class="pt-4">
                        <p class="section-title px-3 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Quick Links</p>
                        
                        <a href="{{ route('home') }}" 
                           class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium text-gray-300 hover:bg-viewlix-gray hover:text-white transition-colors"
                           title="Back to Site">
                            <svg class="nav-icon shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 3h6v6"/>
                                <path d="M10 14 21 3"/>
                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                            </svg>
                            <span class="nav-text">Back to Site</span>
                        </a>
                    </div>
                </nav>
            </div>

            {{-- User Section --}}
            <div class="p-3 border-t border-viewlix-border">
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" 
                            type="button" 
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm text-gray-300 hover:bg-viewlix-gray transition-colors">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" class="shrink-0 size-8 rounded-full object-cover">
                        @else
                            <div class="shrink-0 size-8 rounded-full bg-gradient-to-br from-viewlix-red to-viewlix-red-dark flex items-center justify-center text-white text-sm font-semibold uppercase">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        @endif
                        <div class="user-info flex-1 text-start min-w-0 overflow-hidden">
                            <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </button>

                    {{-- User Dropdown --}}
                    <div x-show="open" 
                         @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute bottom-full left-0 w-full mb-2 bg-viewlix-darker border border-viewlix-border shadow-lg rounded-xl p-1 z-50">
                        <a class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-300 hover:bg-viewlix-gray hover:text-white" href="{{ route('home') }}">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M15 3h6v6"/>
                                <path d="M10 14 21 3"/>
                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                            </svg>
                            <span>Back to Site</span>
                        </a>
                        <a class="flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-gray-300 hover:bg-viewlix-gray hover:text-white" href="{{ route('account') }}">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <circle cx="12" cy="10" r="3"/>
                                <path d="M7 20.662V19a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1.662"/>
                            </svg>
                            <span>Account Settings</span>
                        </a>
                        <div class="border-t border-viewlix-border my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-3 py-2 px-3 rounded-lg text-sm text-red-400 hover:bg-red-500/10">
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                    <polyline points="16 17 21 12 16 7"/>
                                    <line x1="21" x2="9" y1="12" y2="12"/>
                                </svg>
                                <span>Sign out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    {{-- Main Content --}}
    <main class="main-content min-h-screen">
        <div class="p-4 sm:p-6 lg:p-8 space-y-4 sm:space-y-6">
            {{-- Page Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-semibold text-white">@yield('title', 'Dashboard')</h1>
                    @hasSection('subtitle')
                        <p class="mt-1 text-sm text-gray-400">@yield('subtitle')</p>
                    @endif
                </div>
                @hasSection('header-actions')
                <div class="flex items-center gap-3">
                    @yield('header-actions')
                </div>
                @endif
            </div>

            {{-- Flash Messages --}}
            @if(session('success'))
            <div class="bg-teal-500/10 border border-teal-500/50 text-sm text-teal-400 rounded-xl p-4 flex items-center gap-3" role="alert">
                <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/>
                    <path d="m9 12 2 2 4-4"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-500/10 border border-red-500/50 text-sm text-red-400 rounded-xl p-4 flex items-center gap-3" role="alert">
                <svg class="shrink-0 size-5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" x2="12" y1="8" y2="12"/>
                    <line x1="12" x2="12.01" y1="16" y2="16"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
            @endif

            {{-- Page Content --}}
            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>

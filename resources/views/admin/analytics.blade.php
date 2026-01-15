@extends('layouts.admin')

@section('title', 'Analytics')
@section('subtitle', 'View your website analytics powered by Firebase & Google Analytics.')

@section('content')
{{-- Firebase Project Info --}}
<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">
    {{-- Project ID Card --}}
    <div class="flex flex-col bg-viewlix-dark border border-viewlix-border shadow-sm rounded-xl">
        <div class="p-4 md:p-5">
            <div class="flex items-center gap-x-2">
                <p class="text-xs uppercase tracking-wide text-gray-400">Firebase Project</p>
            </div>
            <div class="mt-1 flex items-center gap-x-2">
                <h3 class="text-lg font-medium text-white truncate">{{ $firebaseConfig['project_id'] ?? 'Not Configured' }}</h3>
            </div>
        </div>
    </div>

    {{-- Measurement ID Card --}}
    <div class="flex flex-col bg-viewlix-dark border border-viewlix-border shadow-sm rounded-xl">
        <div class="p-4 md:p-5">
            <div class="flex items-center gap-x-2">
                <p class="text-xs uppercase tracking-wide text-gray-400">Measurement ID</p>
            </div>
            <div class="mt-1 flex items-center gap-x-2">
                <h3 class="text-lg font-medium text-white">{{ $firebaseConfig['measurement_id'] ?? 'Not Configured' }}</h3>
            </div>
        </div>
    </div>

    {{-- Quick Link Card --}}
    <div class="flex flex-col bg-viewlix-dark border border-viewlix-border shadow-sm rounded-xl">
        <div class="p-4 md:p-5">
            <div class="flex items-center gap-x-2">
                <p class="text-xs uppercase tracking-wide text-gray-400">Google Analytics Console</p>
            </div>
            <div class="mt-2">
                <a href="https://analytics.google.com/" target="_blank" rel="noopener noreferrer" 
                   class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-viewlix-border bg-viewlix-gray text-gray-300 shadow-sm hover:bg-viewlix-border hover:text-white focus:outline-none focus:ring-2 focus:ring-viewlix-red transition-colors">
                    Open Google Analytics
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h6v6"/>
                        <path d="M10 14 21 3"/>
                        <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Analytics Embed Section --}}
<div class="bg-viewlix-dark border border-viewlix-border rounded-xl shadow-sm overflow-hidden">
    {{-- Header --}}
    <div class="px-6 py-4 flex flex-col md:flex-row md:justify-between md:items-center gap-3 border-b border-viewlix-border">
        <div>
            <h2 class="text-lg font-semibold text-white">Analytics Dashboard</h2>
            <p class="text-sm text-gray-400">Real-time and historical data from Google Analytics via Firebase.</p>
        </div>
        <div class="flex gap-2">
            @if($firebaseConfig['project_id'])
            <a href="https://console.firebase.google.com/project/{{ $firebaseConfig['project_id'] }}/analytics" 
               target="_blank" 
               rel="noopener noreferrer"
               class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-orange-500/50 bg-orange-500/10 text-orange-400 shadow-sm hover:bg-orange-500/20 hover:text-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-500 transition-colors">
                <svg class="shrink-0 size-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3.89 15.673L6.255.461A.542.542 0 0 1 7.27.289l2.543 4.771zm16.795 3.691L18.433 5.365a.543.543 0 0 0-.918-.295l-14.2 14.294 7.857 4.428a1.62 1.62 0 0 0 1.587 0zM14.3 11.434l-2.293-4.243-7.9 13.173z"/>
                </svg>
                Firebase Console
                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h6v6"/>
                    <path d="M10 14 21 3"/>
                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                </svg>
            </a>
            @endif
        </div>
    </div>

    {{-- Analytics Content --}}
    <div class="p-6">
        @if($firebaseConfig['project_id'] && $firebaseConfig['measurement_id'])
            {{-- Embedded Google Analytics via Looker Studio --}}
            <div class="mb-6">
                <div class="bg-viewlix-gray/50 rounded-lg p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <svg class="shrink-0 size-5 text-blue-400 mt-0.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 16v-4"/>
                            <path d="M12 8h.01"/>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-white">How to embed your Analytics dashboard</h4>
                            <p class="text-sm text-gray-400 mt-1">
                                To embed a custom dashboard, create a report in <a href="https://lookerstudio.google.com/" target="_blank" class="text-viewlix-red hover:underline">Google Looker Studio</a> 
                                connected to your Google Analytics property, then use the embed code below.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Stats Overview --}}
            <div class="grid md:grid-cols-2 gap-6">
                {{-- Google Analytics Links --}}
                <div class="bg-viewlix-gray/30 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                        <svg class="size-5 text-green-400" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M22.84 2.998v17.004a3 3 0 0 1-2.998 2.998h-.006a2.997 2.997 0 0 1-2.992-2.998V2.998A2.997 2.997 0 0 1 19.836 0h.006a3 3 0 0 1 2.998 2.998zM14.5 22h-.006a2.997 2.997 0 0 1-2.992-2.998V9.498A2.997 2.997 0 0 1 14.494 6.5h.006a3 3 0 0 1 2.998 2.998v9.504A3 3 0 0 1 14.5 22zM6.166 22H6.16a2.997 2.997 0 0 1-2.992-2.998v-3.004A2.997 2.997 0 0 1 6.16 13h.006a3 3 0 0 1 2.998 2.998v3.004A3 3 0 0 1 6.166 22z"/>
                        </svg>
                        Google Analytics Reports
                    </h3>
                    <div class="space-y-3">
                        <a href="https://analytics.google.com/analytics/web/#/p{{ str_replace('G-', '', $firebaseConfig['measurement_id']) }}/reports/reportinghub" 
                           target="_blank" 
                           class="flex items-center justify-between p-3 bg-viewlix-dark rounded-lg hover:bg-viewlix-border transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center">
                                    <svg class="size-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 3v18h18"/>
                                        <path d="m19 9-5 5-4-4-3 3"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white">Reports Overview</p>
                                    <p class="text-xs text-gray-400">View all analytics reports</p>
                                </div>
                            </div>
                            <svg class="size-4 text-gray-500 group-hover:text-white transition-colors" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </a>

                        <a href="https://analytics.google.com/analytics/web/#/p{{ str_replace('G-', '', $firebaseConfig['measurement_id']) }}/reports/dashboard" 
                           target="_blank" 
                           class="flex items-center justify-between p-3 bg-viewlix-dark rounded-lg hover:bg-viewlix-border transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center">
                                    <svg class="size-5 text-purple-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="7" height="9" x="3" y="3" rx="1"/>
                                        <rect width="7" height="5" x="14" y="3" rx="1"/>
                                        <rect width="7" height="9" x="14" y="12" rx="1"/>
                                        <rect width="7" height="5" x="3" y="16" rx="1"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white">Dashboard</p>
                                    <p class="text-xs text-gray-400">Real-time metrics overview</p>
                                </div>
                            </div>
                            <svg class="size-4 text-gray-500 group-hover:text-white transition-colors" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </a>

                        <a href="https://analytics.google.com/analytics/web/#/p{{ str_replace('G-', '', $firebaseConfig['measurement_id']) }}/reports/realtime" 
                           target="_blank" 
                           class="flex items-center justify-between p-3 bg-viewlix-dark rounded-lg hover:bg-viewlix-border transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-green-500/10 flex items-center justify-center">
                                    <svg class="size-5 text-green-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white">Realtime</p>
                                    <p class="text-xs text-gray-400">See active users right now</p>
                                </div>
                            </div>
                            <svg class="size-4 text-gray-500 group-hover:text-white transition-colors" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Firebase Analytics Links --}}
                <div class="bg-viewlix-gray/30 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                        <svg class="size-5 text-orange-400" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3.89 15.673L6.255.461A.542.542 0 0 1 7.27.289l2.543 4.771zm16.795 3.691L18.433 5.365a.543.543 0 0 0-.918-.295l-14.2 14.294 7.857 4.428a1.62 1.62 0 0 0 1.587 0zM14.3 11.434l-2.293-4.243-7.9 13.173z"/>
                        </svg>
                        Firebase Analytics
                    </h3>
                    <div class="space-y-3">
                        <a href="https://console.firebase.google.com/project/{{ $firebaseConfig['project_id'] }}/analytics/app/web/overview" 
                           target="_blank" 
                           class="flex items-center justify-between p-3 bg-viewlix-dark rounded-lg hover:bg-viewlix-border transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-orange-500/10 flex items-center justify-center">
                                    <svg class="size-5 text-orange-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 20V10"/>
                                        <path d="M18 20V4"/>
                                        <path d="M6 20v-4"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white">Analytics Overview</p>
                                    <p class="text-xs text-gray-400">Firebase Analytics dashboard</p>
                                </div>
                            </div>
                            <svg class="size-4 text-gray-500 group-hover:text-white transition-colors" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </a>

                        <a href="https://console.firebase.google.com/project/{{ $firebaseConfig['project_id'] }}/analytics/app/web/events" 
                           target="_blank" 
                           class="flex items-center justify-between p-3 bg-viewlix-dark rounded-lg hover:bg-viewlix-border transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-yellow-500/10 flex items-center justify-center">
                                    <svg class="size-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 2v4"/>
                                        <path d="m6.8 14-3.5 2"/>
                                        <path d="m20.7 16-3.5-2"/>
                                        <path d="M6.8 10 3.3 8"/>
                                        <path d="m20.7 8-3.5 2"/>
                                        <circle cx="12" cy="12" r="6"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white">Events</p>
                                    <p class="text-xs text-gray-400">View tracked events</p>
                                </div>
                            </div>
                            <svg class="size-4 text-gray-500 group-hover:text-white transition-colors" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </a>

                        <a href="https://console.firebase.google.com/project/{{ $firebaseConfig['project_id'] }}/analytics/app/web/streamview" 
                           target="_blank" 
                           class="flex items-center justify-between p-3 bg-viewlix-dark rounded-lg hover:bg-viewlix-border transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-cyan-500/10 flex items-center justify-center">
                                    <svg class="size-5 text-cyan-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white">StreamView</p>
                                    <p class="text-xs text-gray-400">Live event stream</p>
                                </div>
                            </div>
                            <svg class="size-4 text-gray-500 group-hover:text-white transition-colors" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </a>

                        <a href="https://console.firebase.google.com/project/{{ $firebaseConfig['project_id'] }}/analytics/app/web/audiences" 
                           target="_blank" 
                           class="flex items-center justify-between p-3 bg-viewlix-dark rounded-lg hover:bg-viewlix-border transition-colors group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-pink-500/10 flex items-center justify-center">
                                    <svg class="size-5 text-pink-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white">Audiences</p>
                                    <p class="text-xs text-gray-400">User segments & cohorts</p>
                                </div>
                            </div>
                            <svg class="size-4 text-gray-500 group-hover:text-white transition-colors" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m9 18 6-6-6-6"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Custom Embed Section --}}
            <div class="mt-6">
                <div class="bg-viewlix-gray/30 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                        <svg class="size-5 text-viewlix-red" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="16 18 22 12 16 6"/>
                            <polyline points="8 6 2 12 8 18"/>
                        </svg>
                        Embed Custom Dashboard
                    </h3>
                    <p class="text-sm text-gray-400 mb-4">
                        To embed a Looker Studio report directly in this page, replace the iframe src below with your dashboard's embed URL.
                    </p>
                    <div class="bg-viewlix-dark rounded-lg p-4 overflow-x-auto">
                        <code class="text-sm text-gray-300">
                            &lt;iframe width="100%" height="600" src="https://lookerstudio.google.com/embed/reporting/YOUR-REPORT-ID" frameborder="0" style="border:0" allowfullscreen&gt;&lt;/iframe&gt;
                        </code>
                    </div>
                </div>
            </div>

        @else
            {{-- Not Configured State --}}
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-yellow-500/10 mb-4">
                    <svg class="size-8 text-yellow-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" x2="12" y1="8" y2="12"/>
                        <line x1="12" x2="12.01" y1="16" y2="16"/>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-white mb-2">Firebase Analytics Not Configured</h3>
                <p class="text-gray-400 max-w-md mx-auto mb-6">
                    To use Google Analytics via Firebase, please configure your Firebase project settings in your <code class="text-viewlix-red">.env</code> file.
                </p>
                <div class="bg-viewlix-gray/50 rounded-lg p-4 text-left max-w-lg mx-auto">
                    <p class="text-xs uppercase tracking-wide text-gray-500 mb-2">Required Environment Variables</p>
                    <code class="text-sm text-gray-300 block space-y-1">
                        <span class="block">FIREBASE_PROJECT_ID=your-project-id</span>
                        <span class="block">FIREBASE_MEASUREMENT_ID=G-XXXXXXXXXX</span>
                    </code>
                </div>
                <div class="mt-6">
                    <a href="https://console.firebase.google.com/" target="_blank" rel="noopener noreferrer" 
                       class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-viewlix-red text-white hover:bg-viewlix-red-dark focus:outline-none focus:ring-2 focus:ring-viewlix-red transition-colors">
                        Go to Firebase Console
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h6v6"/>
                            <path d="M10 14 21 3"/>
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>
                        </svg>
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

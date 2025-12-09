@extends('layouts.admin')

@section('title', 'Notifications')
@section('subtitle', 'Send and manage notifications to all users.')

@section('header-actions')
<a href="{{ route('admin.notifications.create') }}" class="py-2 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-viewlix-red text-white hover:bg-viewlix-red-dark focus:outline-none focus:ring-2 focus:ring-viewlix-red transition-colors">
    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 5v14"/>
        <path d="M5 12h14"/>
    </svg>
    Send Notification
</a>
@endsection

@section('content')
<div class="bg-viewlix-dark border border-viewlix-border rounded-xl shadow-sm overflow-hidden">
    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-viewlix-gray/50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-start">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Notification</span>
                    </th>
                    <th scope="col" class="px-6 py-3 text-start">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Type</span>
                    </th>
                    <th scope="col" class="px-6 py-3 text-start">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Sent By</span>
                    </th>
                    <th scope="col" class="px-6 py-3 text-start">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Date</span>
                    </th>
                    <th scope="col" class="px-6 py-3 text-end">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Actions</span>
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-viewlix-border">
                @forelse($notifications as $id => $notification)
                <tr class="hover:bg-viewlix-gray/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="max-w-xs">
                            <p class="text-sm font-semibold text-white truncate">{{ $notification['title'] ?? 'Untitled' }}</p>
                            <p class="text-sm text-gray-400 truncate">{{ $notification['message'] ?? '' }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php $type = $notification['type'] ?? 'announcement'; @endphp
                        @if($type === 'announcement')
                            <span class="py-1 px-2 inline-flex items-center gap-x-1 text-xs font-medium bg-blue-500/10 text-blue-400 rounded-full">
                                <svg class="size-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m3 11 18-5v12L3 14v-3z"/>
                                    <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
                                </svg>
                                Announcement
                            </span>
                        @elseif($type === 'new_content')
                            <span class="py-1 px-2 inline-flex items-center gap-x-1 text-xs font-medium bg-green-500/10 text-green-400 rounded-full">
                                <svg class="size-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m22 8-6 4 6 4V8Z"/>
                                    <rect width="14" height="12" x="2" y="6" rx="2" ry="2"/>
                                </svg>
                                New Content
                            </span>
                        @else
                            <span class="py-1 px-2 inline-flex items-center gap-x-1 text-xs font-medium bg-yellow-500/10 text-yellow-400 rounded-full">
                                <svg class="size-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 12a9 9 0 1 1-9-9c2.52 0 4.93 1 6.74 2.74L21 8"/>
                                    <path d="M21 3v5h-5"/>
                                </svg>
                                Update
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-300">{{ $notification['sent_by'] ?? 'System' }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $timestamp = isset($notification['created_at']) ? $notification['created_at'] / 1000 : time();
                            $date = \Carbon\Carbon::createFromTimestamp($timestamp);
                        @endphp
                        <span class="text-sm text-gray-300">{{ $date->format('M d, Y H:i') }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-end">
                        <form action="{{ route('admin.notifications.destroy', $id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this notification from history?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    title="Delete"
                                    class="py-1.5 px-2 inline-flex justify-center items-center rounded-lg text-gray-300 hover:text-red-400 hover:bg-red-500/10 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                                <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 6h18"/>
                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/>
                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/>
                                    <line x1="10" x2="10" y1="11" y2="17"/>
                                    <line x1="14" x2="14" y1="11" y2="17"/>
                                </svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="size-12 text-gray-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
                                <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
                            </svg>
                            <p class="text-gray-400">No notifications sent yet</p>
                            <a href="{{ route('admin.notifications.create') }}" class="text-sm text-viewlix-red hover:underline">Send your first notification</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Info Card --}}
<div class="mt-6 bg-viewlix-dark border border-viewlix-border rounded-xl p-6">
    <div class="flex items-start gap-4">
        <div class="shrink-0 size-10 inline-flex justify-center items-center rounded-lg bg-blue-500/10">
            <svg class="shrink-0 size-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <path d="M12 16v-4"/>
                <path d="M12 8h.01"/>
            </svg>
        </div>
        <div>
            <h3 class="text-lg font-semibold text-white">How Notifications Work</h3>
            <ul class="mt-2 text-sm text-gray-400 space-y-1">
                <li>• <strong>In-App:</strong> Notifications appear in users' notification bell (real-time via Firebase)</li>
                <li>• <strong>Push:</strong> If enabled, sends browser/PWA push notifications via FCM</li>
                <li>• <strong>Mass Send:</strong> Notifications are sent to ALL registered users</li>
                <li>• <strong>History:</strong> All sent notifications are stored for reference</li>
            </ul>
        </div>
    </div>
</div>
@endsection

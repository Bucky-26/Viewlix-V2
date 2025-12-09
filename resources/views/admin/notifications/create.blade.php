@extends('layouts.admin')

@section('title', 'Send Notification')
@section('subtitle', 'Send a notification to all users.')

@section('header-actions')
<a href="{{ route('admin.notifications') }}" class="py-2 px-3 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-viewlix-border bg-viewlix-gray text-gray-300 shadow-sm hover:bg-viewlix-border hover:text-white focus:outline-none focus:ring-2 focus:ring-viewlix-red transition-colors">
    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m15 18-6-6 6-6"/>
    </svg>
    Back to Notifications
</a>
@endsection

@section('content')
<div class="max-w-2xl">
    <form action="{{ route('admin.notifications.send') }}" method="POST">
        @csrf

        <div class="bg-viewlix-dark border border-viewlix-border rounded-xl shadow-sm overflow-hidden">
            <div class="p-4 sm:p-6 space-y-5">
                {{-- Notification Type --}}
                <div>
                    <label class="block text-sm font-medium text-white mb-3">Notification Type</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="relative">
                            <input type="radio" name="type" value="announcement" checked class="peer sr-only">
                            <div class="p-4 rounded-xl border-2 border-viewlix-border bg-viewlix-gray/50 cursor-pointer transition-all peer-checked:border-viewlix-red peer-checked:bg-viewlix-red/10">
                                <div class="flex flex-col items-center gap-2 text-center">
                                    <svg class="size-6 text-blue-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m3 11 18-5v12L3 14v-3z"/>
                                        <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
                                    </svg>
                                    <span class="text-sm font-medium text-white">Announcement</span>
                                </div>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="type" value="new_content" class="peer sr-only">
                            <div class="p-4 rounded-xl border-2 border-viewlix-border bg-viewlix-gray/50 cursor-pointer transition-all peer-checked:border-viewlix-red peer-checked:bg-viewlix-red/10">
                                <div class="flex flex-col items-center gap-2 text-center">
                                    <svg class="size-6 text-green-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m22 8-6 4 6 4V8Z"/>
                                        <rect width="14" height="12" x="2" y="6" rx="2" ry="2"/>
                                    </svg>
                                    <span class="text-sm font-medium text-white">New Content</span>
                                </div>
                            </div>
                        </label>
                        <label class="relative">
                            <input type="radio" name="type" value="update" class="peer sr-only">
                            <div class="p-4 rounded-xl border-2 border-viewlix-border bg-viewlix-gray/50 cursor-pointer transition-all peer-checked:border-viewlix-red peer-checked:bg-viewlix-red/10">
                                <div class="flex flex-col items-center gap-2 text-center">
                                    <svg class="size-6 text-yellow-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 12a9 9 0 1 1-9-9c2.52 0 4.93 1 6.74 2.74L21 8"/>
                                        <path d="M21 3v5h-5"/>
                                    </svg>
                                    <span class="text-sm font-medium text-white">Update</span>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-white mb-2">Title <span class="text-red-400">*</span></label>
                    <input type="text" 
                           name="title" 
                           id="title" 
                           value="{{ old('title') }}"
                           maxlength="100"
                           class="py-3 px-4 block w-full bg-viewlix-gray border-viewlix-border rounded-lg text-sm text-white placeholder-gray-400 focus:border-viewlix-red focus:ring-viewlix-red"
                           placeholder="e.g., New Movies Added!"
                           required>
                    @error('title')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Message --}}
                <div>
                    <label for="message" class="block text-sm font-medium text-white mb-2">Message <span class="text-red-400">*</span></label>
                    <textarea name="message" 
                              id="message" 
                              rows="4"
                              maxlength="500"
                              class="py-3 px-4 block w-full bg-viewlix-gray border-viewlix-border rounded-lg text-sm text-white placeholder-gray-400 focus:border-viewlix-red focus:ring-viewlix-red resize-none"
                              placeholder="Write your notification message..."
                              required>{{ old('message') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Max 500 characters</p>
                    @error('message')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Link (Optional) --}}
                <div>
                    <label for="link" class="block text-sm font-medium text-white mb-2">Link <span class="text-gray-500">(Optional)</span></label>
                    <input type="text" 
                           name="link" 
                           id="link" 
                           value="{{ old('link') }}"
                           class="py-3 px-4 block w-full bg-viewlix-gray border-viewlix-border rounded-lg text-sm text-white placeholder-gray-400 focus:border-viewlix-red focus:ring-viewlix-red"
                           placeholder="e.g., /movies or /movie/12345">
                    <p class="mt-1 text-xs text-gray-500">Where to navigate when notification is clicked</p>
                    @error('link')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Image URL (Optional) --}}
                <div>
                    <label for="image" class="block text-sm font-medium text-white mb-2">Image URL <span class="text-gray-500">(Optional)</span></label>
                    <input type="url" 
                           name="image" 
                           id="image" 
                           value="{{ old('image') }}"
                           class="py-3 px-4 block w-full bg-viewlix-gray border-viewlix-border rounded-lg text-sm text-white placeholder-gray-400 focus:border-viewlix-red focus:ring-viewlix-red"
                           placeholder="https://image.tmdb.org/t/p/w500/...">
                    <p class="mt-1 text-xs text-gray-500">Image to show in push notification (poster, banner, etc.)</p>
                    @error('image')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Push Notification Toggle --}}
                <div class="flex items-center justify-between p-4 bg-viewlix-gray/50 rounded-xl">
                    <div>
                        <label for="send_push" class="text-sm font-medium text-white">Send Push Notification</label>
                        <p class="text-xs text-gray-400">Also send as browser/PWA push notification via FCM</p>
                    </div>
                    <div class="relative inline-block">
                        <input type="checkbox" 
                               name="send_push" 
                               id="send_push"
                               value="1"
                               checked
                               class="relative w-11 h-6 p-px bg-viewlix-border border-transparent text-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:ring-viewlix-red disabled:opacity-50 disabled:pointer-events-none checked:bg-none checked:text-viewlix-red checked:border-viewlix-red focus:checked:border-viewlix-red before:inline-block before:size-5 before:bg-white checked:before:bg-white before:translate-x-0 checked:before:translate-x-full before:rounded-full before:shadow before:transform before:ring-0 before:transition before:ease-in-out before:duration-200">
                    </div>
                </div>
            </div>

            {{-- Form Footer --}}
            <div class="p-4 sm:p-6 border-t border-viewlix-border bg-viewlix-gray/30">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <p class="text-sm text-gray-400">
                        <svg class="inline-block size-4 mr-1 text-yellow-400" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/>
                            <path d="M12 9v4"/>
                            <path d="M12 17h.01"/>
                        </svg>
                        This will be sent to <strong class="text-white">all users</strong>
                    </p>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.notifications') }}" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg border border-viewlix-border bg-viewlix-gray text-gray-300 shadow-sm hover:bg-viewlix-border hover:text-white focus:outline-none focus:ring-2 focus:ring-viewlix-red transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="py-2.5 px-4 inline-flex items-center gap-x-2 text-sm font-medium rounded-lg bg-viewlix-red text-white hover:bg-viewlix-red-dark focus:outline-none focus:ring-2 focus:ring-viewlix-red focus:ring-offset-2 focus:ring-offset-viewlix-dark transition-colors">
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m22 2-7 20-4-9-9-4Z"/>
                                <path d="M22 2 11 13"/>
                            </svg>
                            Send Notification
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

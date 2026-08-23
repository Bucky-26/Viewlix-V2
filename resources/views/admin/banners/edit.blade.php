@extends('layouts.admin')

@section('content')
<div class="p-6">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">Edit Banner</h1>

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <strong>Error:</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 max-w-4xl">
        <form method="POST" action="{{ route('admin.banners.update', $banner) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Type Selection -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Banner Type *</label>
                <select name="type" id="type" required class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Type</option>
                    @foreach($types as $type)
                        <option value="{{ $type }}" @selected(old('type', $banner->type) === $type)>{{ ucfirst($type) }} Banner</option>
                    @endforeach
                </select>
                @error('type')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title *</label>
                <input type="text" name="title" id="title" required value="{{ old('title', $banner->title) }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500" placeholder="Enter banner title">
                @error('title')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                <textarea name="description" id="description" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500" placeholder="Enter banner description">{{ old('description', $banner->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Icon (Emoji or SVG) -->
            <div>
                <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Icon (Emoji or SVG)</label>
                <textarea name="icon" id="icon" rows="3" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 font-mono text-sm" placeholder="e.g., 🎉 or <svg>...</svg>">{{ old('icon', $banner->icon) }}</textarea>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Emoji: 🎉 | SVG: &lt;svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-full h-full"&gt;&lt;path d="..."/&gt;&lt;/svg&gt;
                </p>
                @error('icon')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image URL -->
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Image URL</label>
                <input type="text" name="image" id="image" value="{{ old('image', $banner->image) }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500" placeholder="https://example.com/image.jpg">
                @error('image')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Button Text -->
            <div>
                <label for="button_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Button Text (for Popup)</label>
                <input type="text" name="button_text" id="button_text" value="{{ old('button_text', $banner->button_text) }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500" placeholder="Learn More">
                @error('button_text')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Button URL -->
            <div>
                <label for="button_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Button URL</label>
                <input type="url" name="button_url" id="button_url" value="{{ old('button_url', $banner->button_url) }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500" placeholder="https://example.com">
                @error('button_url')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Hotlinks (JSON) -->
            <div>
                <label for="hotlinks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Hotlinks (JSON - for Header Banner)</label>
                <textarea name="hotlinks" id="hotlinks" rows="4" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 font-mono text-sm" placeholder='[{"text":"Learn More","url":"/pricing","target":"_self"},{"text":"Subscribe","url":"/subscribe","target":"_blank"}]'>{{ old('hotlinks', !empty($banner->hotlinks) ? (is_array($banner->hotlinks) ? json_encode($banner->hotlinks, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $banner->hotlinks) : '') }}</textarea>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Format: [{"text":"Link Text","url":"https://example.com","target":"_self"}]</p>
                @error('hotlinks')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Is Active -->
            <div class="flex items-center space-x-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" @checked(old('is_active', $banner->is_active)) class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">Active</label>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-4">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Update Banner
                </button>
                <a href="{{ route('admin.banners.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 dark:bg-gray-600 dark:text-gray-300 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

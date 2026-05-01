@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Banner Management</h1>
        <a href="{{ route('admin.banners.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            + New Banner
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-x-auto">
        <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($banners as $banner)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 text-sm font-semibold rounded-full @if($banner->type === 'popup') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 @else bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 @endif">
                                {{ ucfirst($banner->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $banner->title }}</p>
                            @if($banner->description)
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $banner->description }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($banner->is_active)
                                <span class="px-2 py-1 text-xs font-semibold text-green-800 dark:text-green-200 bg-green-100 dark:bg-green-900 rounded-full">Active</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold text-gray-800 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 rounded-full">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $banner->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2 flex gap-2">
                            <form method="POST" action="{{ route('admin.banners.toggle', $banner) }}" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1 rounded text-white text-xs font-semibold transition @if($banner->is_active) bg-yellow-500 hover:bg-yellow-600 @else bg-green-500 hover:bg-green-600 @endif">
                                    @if($banner->is_active) Deactivate @else Activate @endif
                                </button>
                            </form>
                            <a href="{{ route('admin.banners.edit', $banner) }}" class="px-3 py-1 rounded bg-blue-500 text-white text-xs font-semibold hover:bg-blue-600 transition">Edit</a>
                            <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" class="inline" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 rounded bg-red-500 text-white text-xs font-semibold hover:bg-red-600 transition">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            No banners found. <a href="{{ route('admin.banners.create') }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Create one now</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($banners->hasPages())
        <div class="mt-4">
            {{ $banners->links() }}
        </div>
    @endif
</div>
@endsection

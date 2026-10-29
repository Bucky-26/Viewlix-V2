<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VideoSource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VideoSourceController extends Controller
{
    /**
     * Display a listing of video sources with default selector
     */
    public function index(): View
    {
        $sources = VideoSource::orderByDesc('priority')->orderBy('id')->get();

        $defaultMovieSource = $sources->firstWhere('is_default_movie', true);
        $defaultTvSource = $sources->firstWhere('is_default_tv', true);
        $defaultAnimeSource = $sources->firstWhere('is_default_anime', true);

        return view('admin.video-sources.index', compact(
            'sources',
            'defaultMovieSource',
            'defaultTvSource',
            'defaultAnimeSource'
        ));
    }

    /**
     * Show the form for creating a new video source
     */
    public function create(): View
    {
        return view('admin.video-sources.create');
    }

    /**
     * Store a newly created video source
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'base_url' => 'nullable|string|max:500',
            'movie_url_pattern' => 'nullable|string',
            'tv_url_pattern' => 'nullable|string',
            'anime_url_pattern' => 'nullable|string',
            'priority' => 'nullable|integer',
        ]);

        $validated['supports_movie'] = $request->boolean('supports_movie');
        $validated['supports_tv'] = $request->boolean('supports_tv');
        $validated['supports_anime'] = $request->boolean('supports_anime');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['priority'] = $validated['priority'] ?? 0;

        $isDefaultMovie = $request->boolean('is_default_movie');
        $isDefaultTv = $request->boolean('is_default_tv');
        $isDefaultAnime = $request->boolean('is_default_anime');

        if ($isDefaultMovie) {
            VideoSource::query()->update(['is_default_movie' => false]);
        }
        if ($isDefaultTv) {
            VideoSource::query()->update(['is_default_tv' => false]);
        }
        if ($isDefaultAnime) {
            VideoSource::query()->update(['is_default_anime' => false]);
        }

        $validated['is_default_movie'] = $isDefaultMovie;
        $validated['is_default_tv'] = $isDefaultTv;
        $validated['is_default_anime'] = $isDefaultAnime;

        VideoSource::create($validated);

        return redirect()->route('admin.video-sources.index')->with('success', 'Video source created successfully.');
    }

    /**
     * Show the form for editing the specified video source
     */
    public function edit(VideoSource $videoSource): View
    {
        return view('admin.video-sources.edit', compact('videoSource'));
    }

    /**
     * Update the specified video source
     */
    public function update(Request $request, VideoSource $videoSource): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'base_url' => 'nullable|string|max:500',
            'movie_url_pattern' => 'nullable|string',
            'tv_url_pattern' => 'nullable|string',
            'anime_url_pattern' => 'nullable|string',
            'priority' => 'nullable|integer',
        ]);

        $validated['supports_movie'] = $request->boolean('supports_movie');
        $validated['supports_tv'] = $request->boolean('supports_tv');
        $validated['supports_anime'] = $request->boolean('supports_anime');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['priority'] = $validated['priority'] ?? 0;

        $isDefaultMovie = $request->boolean('is_default_movie');
        $isDefaultTv = $request->boolean('is_default_tv');
        $isDefaultAnime = $request->boolean('is_default_anime');

        if ($isDefaultMovie) {
            VideoSource::where('id', '!=', $videoSource->id)->update(['is_default_movie' => false]);
        }
        if ($isDefaultTv) {
            VideoSource::where('id', '!=', $videoSource->id)->update(['is_default_tv' => false]);
        }
        if ($isDefaultAnime) {
            VideoSource::where('id', '!=', $videoSource->id)->update(['is_default_anime' => false]);
        }

        $validated['is_default_movie'] = $isDefaultMovie;
        $validated['is_default_tv'] = $isDefaultTv;
        $validated['is_default_anime'] = $isDefaultAnime;

        $videoSource->update($validated);

        return redirect()->route('admin.video-sources.index')->with('success', "Video source '{$videoSource->name}' updated successfully.");
    }

    /**
     * Remove the specified video source
     */
    public function destroy(VideoSource $videoSource): RedirectResponse
    {
        $name = $videoSource->name;
        $videoSource->delete();

        return redirect()->route('admin.video-sources.index')->with('success', "Video source '{$name}' deleted successfully.");
    }

    /**
     * Toggle active/inactive status
     */
    public function toggle(VideoSource $videoSource): RedirectResponse
    {
        $videoSource->update(['is_active' => !$videoSource->is_active]);

        $status = $videoSource->is_active ? 'activated' : 'deactivated';
        return redirect()->back()->with('success', "Video source '{$videoSource->name}' {$status}.");
    }

    /**
     * Set a video source as the default for a content type
     */
    public function setDefault(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'source_id' => 'required|exists:video_sources,id',
            'type' => 'required|in:movie,tv,anime',
        ]);

        $source = VideoSource::findOrFail($validated['source_id']);
        $source->setAsDefault($validated['type']);

        $typeName = match ($validated['type']) {
            'movie' => 'Movies',
            'tv' => 'TV Shows',
            'anime' => 'Anime',
        };

        return redirect()->back()->with('success', "'{$source->name}' is now the default source for {$typeName}.");
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /**
     * Display a listing of the banners
     */
    public function index()
    {
        $banners = Banner::latest()->paginate(10);
        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new banner
     */
    public function create()
    {
        $types = ['popup', 'header'];
        return view('admin.banners.create', compact('types'));
    }

    /**
     * Store a newly created banner in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:popup,header',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'icon' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'button_url' => 'nullable|url',
            'hotlinks' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Properly decode hotlinks JSON if provided as string
        if (!empty($validated['hotlinks'])) {
            if (is_string($validated['hotlinks'])) {
                $decoded = json_decode($validated['hotlinks'], true);
                $validated['hotlinks'] = is_array($decoded) ? $decoded : null;
            }
        } else {
            $validated['hotlinks'] = null;
        }

        // If this is a header banner being activated, deactivate all other header banners
        if ($validated['type'] === 'header' && $validated['is_active']) {
            Banner::where('type', 'header')->where('id', '!=', null)->update(['is_active' => false]);
        }

        Banner::create($validated);

        return redirect()->route('admin.banners.index')
                       ->with('success', 'Banner created successfully.');
    }

    /**
     * Show the form for editing the specified banner
     */
    public function edit(Banner $banner)
    {
        $types = ['popup', 'header'];
        return view('admin.banners.edit', compact('banner', 'types'));
    }

    /**
     * Update the specified banner in storage
     */
    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'type' => 'required|in:popup,header',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'icon' => 'nullable|string',
            'button_text' => 'nullable|string|max:255',
            'button_url' => 'nullable|url',
            'hotlinks' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Properly decode hotlinks JSON if provided as string
        if (!empty($validated['hotlinks'])) {
            if (is_string($validated['hotlinks'])) {
                $decoded = json_decode($validated['hotlinks'], true);
                $validated['hotlinks'] = is_array($decoded) ? $decoded : null;
            }
        } else {
            $validated['hotlinks'] = null;
        }

        // If this is a header banner being activated, deactivate all other header banners
        if ($validated['type'] === 'header' && $validated['is_active']) {
            Banner::where('type', 'header')->where('id', '!=', $banner->id)->update(['is_active' => false]);
        }

        $banner->update($validated);

        return redirect()->route('admin.banners.index')
                       ->with('success', 'Banner updated successfully.');
    }

    /**
     * Toggle banner active status
     */
    public function toggle(Banner $banner)
    {
        $newStatus = !$banner->is_active;
        
        // If this is a header banner being activated, deactivate all other header banners
        if ($banner->type === 'header' && $newStatus) {
            Banner::where('type', 'header')->where('id', '!=', $banner->id)->update(['is_active' => false]);
        }
        
        $banner->update(['is_active' => $newStatus]);

        return redirect()->route('admin.banners.index')
                       ->with('success', 'Banner ' . ($newStatus ? 'activated' : 'deactivated') . ' successfully.');
    }

    /**
     * Remove the specified banner from storage
     */
    public function destroy(Banner $banner)
    {
        $banner->delete();

        return redirect()->route('admin.banners.index')
                       ->with('success', 'Banner deleted successfully.');
    }
}

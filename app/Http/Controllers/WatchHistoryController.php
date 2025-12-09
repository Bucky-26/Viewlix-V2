<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class WatchHistoryController extends Controller
{
    /**
     * Display user's watch history page
     * Data is loaded from Firebase on the client-side
     */
    public function index(): View
    {
        return view('pages.watch-history');
    }
}

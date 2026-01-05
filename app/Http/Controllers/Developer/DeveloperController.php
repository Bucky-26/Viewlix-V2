<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;

class DeveloperController extends Controller
{
    /**
     * Show developer portfolio
     */
    public function portfolio()
    {
        return view('developer.portfolio');
    }

    /**
     * Show blog index
     */
    public function blogIndex()
    {
        return view('developer.blogs');
    }

    /**
     * Show blog post
     */
    public function showBlog($slug)
    {
        return view('developer.blog', ['slug' => $slug]);
    }
}

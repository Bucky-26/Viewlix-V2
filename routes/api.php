<?php

use App\Services\TMDBService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Preview API endpoint
Route::get('/preview/{type}/{id}', function (string $type, int $id, TMDBService $tmdb) {
    try {
        switch ($type) {
            case 'movie':
                $data = $tmdb->getMovie($id);
                break;
            case 'tv':
            case 'anime':
                $data = $tmdb->getTvShow($id);
                break;
            default:
                return response()->json(['error' => 'Invalid type'], 400);
        }
        
        return response()->json($data);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to fetch data'], 500);
    }
});

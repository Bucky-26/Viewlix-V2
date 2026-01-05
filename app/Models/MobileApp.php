<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MobileApp extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform',
        'version',
        'app_store_url',
        'play_store_url',
        'file_path',
        'file_size',
        'download_count',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the iOS app
     */
    public static function ios()
    {
        return static::where('platform', 'ios')->latest()->first();
    }

    /**
     * Get the Android app
     */
    public static function android()
    {
        return static::where('platform', 'android')->latest()->first();
    }

    /**
     * Increment download count
     */
    public function incrementDownloads()
    {
        $this->increment('download_count');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'description',
        'image',
        'icon',
        'button_text',
        'button_url',
        'hotlinks',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'hotlinks' => 'array',
    ];

    /**
     * Get active banners
     */
    public static function active()
    {
        return self::where('is_active', true)->get();
    }
}

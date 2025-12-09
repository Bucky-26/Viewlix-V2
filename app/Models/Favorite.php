<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'media_id',
        'media_type',
        'title',
        'poster_path',
        'backdrop_path',
        'vote_average',
        'release_date',
    ];

    protected $casts = [
        'media_id' => 'integer',
        'vote_average' => 'float',
    ];

    /**
     * Get the user that owns the favorite
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by media type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('media_type', $type);
    }

    /**
     * Get the poster URL
     */
    public function getPosterUrlAttribute(): string
    {
        return $this->poster_path 
            ? 'https://image.tmdb.org/t/p/w500' . $this->poster_path 
            : asset('images/no-poster.png');
    }

    /**
     * Get the backdrop URL
     */
    public function getBackdropUrlAttribute(): string
    {
        return $this->backdrop_path 
            ? 'https://image.tmdb.org/t/p/w1280' . $this->backdrop_path 
            : '';
    }
}

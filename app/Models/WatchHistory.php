<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WatchHistory extends Model
{
    use HasFactory;

    protected $table = 'watch_history';

    protected $fillable = [
        'user_id',
        'media_id',
        'media_type',
        'title',
        'poster_path',
        'backdrop_path',
        'season',
        'episode',
        'progress',
        'watched_time',
        'duration',
    ];

    protected $casts = [
        'media_id' => 'integer',
        'season' => 'integer',
        'episode' => 'integer',
        'progress' => 'float',
        'watched_time' => 'integer',
        'duration' => 'integer',
    ];

    /**
     * Get the user that owns the watch history entry
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
     * Scope to get recently watched
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('updated_at', 'desc');
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

    /**
     * Get formatted watch time
     */
    public function getFormattedWatchTimeAttribute(): string
    {
        $hours = floor($this->watched_time / 3600);
        $minutes = floor(($this->watched_time % 3600) / 60);
        $seconds = $this->watched_time % 60;

        if ($hours > 0) {
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }
        return sprintf('%d:%02d', $minutes, $seconds);
    }

    /**
     * Check if episode is fully watched (>90%)
     */
    public function getIsCompletedAttribute(): bool
    {
        return $this->progress >= 90;
    }
}

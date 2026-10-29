<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class VideoSource extends Model
{
    use HasFactory;

    protected $table = 'video_sources';

    protected $fillable = [
        'name',
        'base_url',
        'movie_url_pattern',
        'tv_url_pattern',
        'anime_url_pattern',
        'supports_movie',
        'supports_tv',
        'supports_anime',
        'is_default_movie',
        'is_default_tv',
        'is_default_anime',
        'is_active',
        'priority',
    ];

    protected $casts = [
        'supports_movie' => 'boolean',
        'supports_tv' => 'boolean',
        'supports_anime' => 'boolean',
        'is_default_movie' => 'boolean',
        'is_default_tv' => 'boolean',
        'is_default_anime' => 'boolean',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }

    /**
     * Clear cached video sources
     */
    public static function clearCache(): void
    {
        Cache::forget('video_sources_movie');
        Cache::forget('video_sources_tv');
        Cache::forget('video_sources_anime');
        Cache::forget('video_sources_all');
    }

    /**
     * Check if this source supports a given content type
     */
    public function supportsType(string $type): bool
    {
        return match ($type) {
            'movie' => $this->supports_movie,
            'tv' => $this->supports_tv,
            'anime' => $this->supports_anime,
            default => false,
        };
    }

    /**
     * Check if this source is default for a given content type
     */
    public function isDefault(string $type): bool
    {
        return match ($type) {
            'movie' => $this->is_default_movie,
            'tv' => $this->is_default_tv,
            'anime' => $this->is_default_anime,
            default => false,
        };
    }

    /**
     * Set this source as default for a given type (and unset others)
     */
    public function setAsDefault(string $type): void
    {
        $column = match ($type) {
            'movie' => 'is_default_movie',
            'tv' => 'is_default_tv',
            'anime' => 'is_default_anime',
            default => null,
        };

        if (!$column) return;

        static::query()->where('id', '!=', $this->id)->update([$column => false]);
        $this->update([$column => true]);
        static::clearCache();
    }

    /**
     * Construct dynamic embed URL for content
     */
    public function buildEmbedUrl(string $type, string|int $id, int|string $season = 1, int|string $episode = 1, ?string $slug = null): ?string
    {
        $pattern = null;
        $baseUrl = rtrim($this->base_url ?? '', '/');

        if ($type === 'movie') {
            $pattern = !empty($this->movie_url_pattern)
                ? $this->movie_url_pattern
                : ($baseUrl ? "{$baseUrl}/movie/{id}" : null);
        } elseif ($type === 'tv') {
            $pattern = !empty($this->tv_url_pattern)
                ? $this->tv_url_pattern
                : ($baseUrl ? "{$baseUrl}/tv/{id}/{season}/{episode}" : null);
        } elseif ($type === 'anime') {
            $pattern = !empty($this->anime_url_pattern)
                ? $this->anime_url_pattern
                : (!empty($this->tv_url_pattern)
                    ? $this->tv_url_pattern
                    : ($baseUrl ? "{$baseUrl}/tv/{id}/{season}/{episode}" : null));
        }

        if (empty($pattern)) {
            return null;
        }

        $replacements = [
            '{id}' => $id,
            '{tmdb}' => $id,
            '{season}' => $season,
            '{episode}' => $episode,
            '{slug}' => $slug ?? $id,
            '{base_url}' => $baseUrl,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $pattern);
    }

    /**
     * Query all active sources for a given content type, sorted with default first
     */
    public static function getSourcesForContent(string $type, string|int $id, int|string $season = 1, int|string $episode = 1, ?string $slug = null): array
    {
        $cacheKey = "video_sources_{$type}";
        $sources = Cache::remember($cacheKey, 3600, function () use ($type) {
            $query = static::where('is_active', true);

            if ($type === 'movie') {
                $query->where('supports_movie', true);
            } elseif ($type === 'tv') {
                $query->where('supports_tv', true);
            } elseif ($type === 'anime') {
                $query->where('supports_anime', true);
            }

            return $query->get()->sortByDesc(function ($src) use ($type) {
                // Default source gets highest priority, then numerical priority
                $isDef = $src->isDefault($type) ? 1 : 0;
                return ($isDef * 1000000) + ($src->priority ?? 0);
            })->values();
        });

        $formatted = [];
        $defaultEmbed = null;

        foreach ($sources as $source) {
            $embedUrl = $source->buildEmbedUrl($type, $id, $season, $episode, $slug);
            if (!empty($embedUrl)) {
                $isDefault = $source->isDefault($type);
                $formatted[] = [
                    'name' => $source->name,
                    'embed' => $embedUrl,
                    'is_default' => $isDefault,
                    'source_id' => $source->id,
                ];

                if ($isDefault && $defaultEmbed === null) {
                    $defaultEmbed = $embedUrl;
                }
            }
        }

        // Fallback: If no default marked or default URL failed, use first source
        if ($defaultEmbed === null && !empty($formatted)) {
            $defaultEmbed = $formatted[0]['embed'];
        }

        return [
            'sources' => $formatted,
            'default_embed' => $defaultEmbed,
        ];
    }
}

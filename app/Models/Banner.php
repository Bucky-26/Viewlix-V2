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
     * Get hotlinks safely as an array even if stored as string or double encoded in DB
     */
    public function getHotlinksAttribute($value)
    {
        if (is_null($value)) {
            return [];
        }

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    /**
     * Set hotlinks safely as an array/json
     */
    public function setHotlinksAttribute($value)
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $this->attributes['hotlinks'] = is_array($decoded) ? json_encode($decoded) : $value;
        } elseif (is_array($value)) {
            $this->attributes['hotlinks'] = json_encode($value);
        } else {
            $this->attributes['hotlinks'] = null;
        }
    }

    /**
     * Get active banners
     */
    public static function active()
    {
        return self::where('is_active', true)->get();
    }
}

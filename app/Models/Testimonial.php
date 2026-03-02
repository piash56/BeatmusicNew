<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Testimonial extends Model
{
    protected $fillable = [
        'customer_name',
        'title',
        'feedback',
        'rating',
        'status',
        'display_on',
        'profile_picture',
    ];

    protected $casts = [
        'display_on' => 'array',
    ];

    /**
     * Normalize storage paths coming from legacy Mongo data.
     * Converts values like "/uploads/testimonials/xxx.png" to "testimonials/xxx.png".
     */
    public static function normalizeStoragePath(?string $path): ?string
    {
        if (! $path || ! is_string($path)) {
            return null;
        }

        $path = str_replace('\\', '/', trim($path));
        $path = ltrim($path, '/');

        // Legacy uploads directory -> new disk directory
        $path = preg_replace('#^uploads/testimonials/#', 'testimonials/', $path);

        return $path ?: null;
    }

    public function getProfilePictureUrlAttribute(): string
    {
        $path = self::normalizeStoragePath($this->profile_picture);

        if ($path) {
            return asset('storage/' . $path);
        }

        return asset('images/default-avatar.png');
    }

    public function getHasProfileImageAttribute(): bool
    {
        $path = self::normalizeStoragePath($this->profile_picture);
        return $path ? Storage::disk('public')->exists($path) : false;
    }
}

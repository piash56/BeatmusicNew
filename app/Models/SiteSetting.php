<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_title', 'logo_url', 'logo_alt', 'favicon', 'footer_text',
        'copyright_text', 'social_links', 'footer_links_1', 'footer_links_2',
        'copyright_links', 'last_updated_by',
    ];

    protected $casts = [
        'social_links' => 'array',
        'footer_links_1' => 'array',
        'footer_links_2' => 'array',
        'copyright_links' => 'array',
    ];

    public static function getSettings(): self
    {
        return Cache::remember('site_settings', 3600, function () {
            return self::first() ?? new self([
                'site_title' => 'Beat Music',
                'logo_alt' => 'Beat Music',
            ]);
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('site_settings');
    }
}

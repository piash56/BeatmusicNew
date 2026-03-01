<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EditorialPlaylist extends Model
{
    protected $fillable = [
        'platform',
        'name',
        'url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public const PLATFORMS = ['Spotify', 'Apple Music', 'Amazon Music'];
}

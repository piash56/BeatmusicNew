<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreSave extends Model
{
    protected $fillable = [
        'user_id', 'track_id', 'platform', 'spotify_user_id',
        'access_token', 'refresh_token', 'token_expires_at', 'status',
        'spotify_track_id', 'processed_at', 'error_message',
        'user_display_name', 'user_email', 'track_title', 'artist_name',
        'release_date', 'is_public_pre_save',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'processed_at' => 'datetime',
        'release_date' => 'date',
        'is_public_pre_save' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }
}

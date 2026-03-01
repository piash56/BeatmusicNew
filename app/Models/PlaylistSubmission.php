<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlaylistSubmission extends Model
{
    protected $fillable = [
        'user_id', 'track_id', 'platform', 'playlist_name', 'playlist_url',
        'status', 'submission_date', 'review_date', 'review_note', 'listeners', 'streams',
    ];

    protected $casts = [
        'submission_date' => 'datetime',
        'review_date' => 'datetime',
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConcertLiveRequest extends Model
{
    protected $fillable = [
        'user_id',
        'concert_live_id',
        'artist_name',
        'status',
        'request_date',
        'admin_notes',
        'updated_by',
        'is_active',
    ];

    protected $casts = [
        'request_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function concertLive()
    {
        return $this->belongsTo(ConcertLive::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VevoAccount extends Model
{
    protected $fillable = [
        'user_id', 'artist_name', 'contact_email', 'telephone',
        'release_name', 'biography', 'status', 'admin_notes',
        'vevo_channel_url', 'approved_at', 'approved_by', 'rejected_at', 'rejected_by',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

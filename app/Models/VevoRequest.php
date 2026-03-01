<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VevoRequest extends Model
{
    protected $fillable = [
        'user_id', 'artist_name', 'contact_email', 'telephone',
        'release_name', 'biography', 'status', 'admin_notes',
        'processed_by', 'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

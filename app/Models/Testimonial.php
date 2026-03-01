<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'customer_name', 'title', 'feedback', 'rating', 'status', 'display_on', 'profile_picture',
    ];

    protected $casts = [
        'display_on' => 'array',
    ];

    public function getProfilePictureUrlAttribute(): string
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }
        return asset('images/default-avatar.png');
    }
}

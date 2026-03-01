<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadioNetwork extends Model
{
    protected $fillable = ['name', 'cover_image', 'is_active', 'created_by'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function promotions()
    {
        return $this->hasMany(RadioPromotion::class);
    }

    public function getCoverImageUrlAttribute(): string
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        return asset('images/default-radio.png');
    }
}

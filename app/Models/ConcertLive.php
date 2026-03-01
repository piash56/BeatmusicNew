<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConcertLive extends Model
{
    protected $fillable = [
        'name', 'city', 'concert_date', 'slots_available', 'slots_booked', 'is_active', 'created_by',
    ];

    protected $casts = [
        'concert_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function requests()
    {
        return $this->hasMany(ConcertLiveRequest::class);
    }

    public function getSlotsRemainingAttribute(): int
    {
        return max(0, $this->slots_available - $this->slots_booked);
    }

    public function getBookingPercentageAttribute(): float
    {
        if ($this->slots_available <= 0) {
            return 0.0;
        }
        return round($this->slots_booked / $this->slots_available * 100, 1);
    }
}

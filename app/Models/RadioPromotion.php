<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadioPromotion extends Model
{
    protected $fillable = [
        'user_id', 'track_id', 'track_index', 'radio_network_id',
        'status', 'request_date', 'published_date', 'finish_date',
        'updated_by', 'admin_notes', 'is_active', 'likes', 'liked_by', 'liked_by_guests',
    ];

    protected $casts = [
        'request_date' => 'datetime',
        'published_date' => 'datetime',
        'finish_date' => 'datetime',
        'is_active' => 'boolean',
        'liked_by' => 'array',
        'liked_by_guests' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }

    public function radioNetwork()
    {
        return $this->belongsTo(RadioNetwork::class);
    }

    public function isLikedByUser(int $userId): bool
    {
        return in_array($userId, $this->liked_by ?? []);
    }

    public function isLikedByGuest(string $guestUuid): bool
    {
        return in_array($guestUuid, $this->liked_by_guests ?? []);
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if ($this->status !== 'published' || !$this->finish_date) {
            return null;
        }
        return max(0, (int) now()->diffInDays($this->finish_date, false));
    }

    public function getProgressPercentageAttribute(): ?float
    {
        if ($this->status !== 'published' || !$this->published_date || !$this->finish_date) {
            return null;
        }
        $total = $this->published_date->diffInDays($this->finish_date);
        if ($total <= 0) {
            return 100.0;
        }
        $elapsed = $this->published_date->diffInDays(now());
        return min(100.0, max(0.0, round(($elapsed / $total) * 100, 1)));
    }
}

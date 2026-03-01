<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payout extends Model
{
    protected $fillable = [
        'user_id', 'paypal_email', 'amount', 'status',
        'request_date', 'paid_date', 'user_full_name', 'user_email',
        'payout_stats', 'admin_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'request_date' => 'datetime',
        'paid_date' => 'datetime',
        'payout_stats' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

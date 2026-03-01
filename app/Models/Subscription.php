<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id', 'plan_id', 'plan_name', 'start_date', 'end_date',
        'status', 'payment_method', 'stripe_subscription_id', 'stripe_customer_id',
        'paypal_subscription_id', 'billing_cycle', 'amount',
        'renewal_attempts', 'last_renewal_attempt', 'next_renewal_date',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'next_renewal_date' => 'datetime',
        'last_renewal_attempt' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(PricingPlan::class, 'plan_id');
    }
}
